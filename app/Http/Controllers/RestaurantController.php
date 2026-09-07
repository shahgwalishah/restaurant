<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items', 'table', 'customer'])->latest()->get();
        $today = $orders->filter(fn ($o) => $o->created_at->isToday());

        return Inertia::render('Restaurant', ['categories' => Category::with(['items' => fn ($q) => $q->orderBy('name')])->get(), 'tables' => RestaurantTable::orderBy('name')->get(), 'orders' => $orders->take(50)->values(), 'inventory' => InventoryItem::orderBy('name')->get(), 'customers' => Customer::latest()->take(50)->get(), 'expenses' => Expense::latest('expense_date')->take(50)->get(), 'stats' => ['sales' => $today->where('payment_status', 'paid')->sum('total'), 'orders' => $today->count(), 'active' => $orders->whereIn('status', ['pending', 'preparing', 'ready'])->count(), 'lowStock' => InventoryItem::whereColumn('stock', '<=', 'minimum_stock')->count()]]);
    }

    public function order(Request $request)
    {
        $data = $request->validate(['type' => 'required|in:dine_in,takeaway,delivery', 'table_id' => 'nullable|exists:restaurant_tables,id', 'customer_id' => 'nullable|exists:customers,id', 'discount' => 'nullable|numeric|min:0', 'notes' => 'nullable|string|max:500', 'items' => 'required|array|min:1', 'items.*.id' => 'required|exists:menu_items,id', 'items.*.quantity' => 'required|integer|min:1|max:50']);
        $order = DB::transaction(function () use ($data, $request) {
            $menu = MenuItem::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');
            $subtotal = collect($data['items'])->sum(fn ($i) => $menu[$i['id']]->price * $i['quantity']);
            $discount = min((float) ($data['discount'] ?? 0), $subtotal);
            $tax = round(($subtotal - $discount) * .05, 2);
            $order = Order::create(['order_no' => 'MTR-'.now()->format('ymd').'-'.str_pad((string) (Order::count() + 1), 4, '0', STR_PAD_LEFT), 'user_id' => $request->user()->id, 'customer_id' => $data['customer_id'] ?? null, 'restaurant_table_id' => $data['table_id'] ?? null, 'type' => $data['type'], 'subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'total' => $subtotal - $discount + $tax, 'notes' => $data['notes'] ?? null]);
            foreach ($data['items'] as $item) {
                $order->items()->create(['menu_item_id' => $item['id'], 'name' => $menu[$item['id']]->name, 'price' => $menu[$item['id']]->price, 'quantity' => $item['quantity']]);
            }if ($order->restaurant_table_id) {
                RestaurantTable::whereKey($order->restaurant_table_id)->update(['status' => 'occupied']);
            }

            return $order;
        });

        return back()->with('success', "Order {$order->order_no} kitchen ko bhej diya gaya.");
    }

    public function status(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:pending,preparing,ready,served,completed,cancelled']);
        $order->update($data);
        if (in_array($data['status'], ['completed', 'cancelled']) && $order->restaurant_table_id) {
            RestaurantTable::whereKey($order->restaurant_table_id)->update(['status' => 'available']);
        }

        return back();
    }

    public function pay(Request $request, Order $order)
    {
        $data = $request->validate(['payment_method' => 'required|in:cash,card,easypaisa,jazzcash']);
        $order->update(['payment_status' => 'paid', 'payment_method' => $data['payment_method'], 'status' => 'completed']);
        if ($order->restaurant_table_id) {
            RestaurantTable::whereKey($order->restaurant_table_id)->update(['status' => 'available']);
        }

        return back()->with('success', 'Payment receive ho gayi.');
    }

    public function menu(StoreMenuItemRequest $request)
    {
        $data = $request->safe()->except('image');
        $image = $request->file('image');
        $publicDirectory = public_path('images/menu-items');
        $fileName = Str::slug($data['name']).'-'.now()->format('YmdHis').'.'.$image->extension();
        $canUsePublicDirectory = is_dir($publicDirectory)
            || (is_writable(dirname($publicDirectory)) && mkdir($publicDirectory, 0755, true));

        if ($canUsePublicDirectory && is_writable($publicDirectory)) {
            $image->move($publicDirectory, $fileName);
            $data['image_path'] = 'images/menu-items/'.$fileName;
        } else {
            $storedImagePath = $image->storeAs('menu-items', $fileName, 'public');
            $data['image_path'] = 'storage/'.$storedImagePath;
        }

        MenuItem::create($data);

        return back()->with('success', 'Menu item image ke sath add ho gaya.');
    }

    public function category(Request $request)
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]));

        return back()->with('success', 'Menu category add ho gayi.');
    }

    public function inventory(Request $request)
    {
        InventoryItem::create($request->validate(['name' => 'required|max:100', 'unit' => 'required|max:20', 'stock' => 'required|numeric|min:0', 'minimum_stock' => 'required|numeric|min:0', 'unit_cost' => 'required|numeric|min:0', 'supplier' => 'nullable|max:100']));

        return back()->with('success', 'Stock item add ho gaya.');
    }

    public function stock(Request $request, InventoryItem $item)
    {
        $item->update($request->validate(['stock' => 'required|numeric|min:0']));

        return back();
    }

    public function expense(Request $request)
    {
        Expense::create($request->validate(['title' => 'required|max:100', 'category' => 'required|max:50', 'amount' => 'required|numeric|min:1', 'expense_date' => 'required|date', 'notes' => 'nullable|max:300']));

        return back()->with('success', 'Expense save ho gaya.');
    }

    public function customer(Request $request)
    {
        Customer::create($request->validate(['name' => 'required|max:100', 'phone' => 'required|max:20|unique:customers,phone', 'address' => 'nullable|max:250']));

        return back()->with('success', 'Customer add ho gaya.');
    }
}
