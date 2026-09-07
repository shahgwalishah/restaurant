<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create(['name' => 'Muhammad Wali', 'email' => 'admin@multanbites.pk', 'phone' => '0300-1234567', 'role' => 'admin', 'password' => bcrypt('password')]);
        $cats = collect([['name' => 'BBQ', 'color' => '#e85d3f'], ['name' => 'Karahi', 'color' => '#e69b32'], ['name' => 'Rice', 'color' => '#5f8f65'], ['name' => 'Fast Food', 'color' => '#3b82a0'], ['name' => 'Drinks', 'color' => '#8056a3']])->mapWithKeys(fn ($c) => [$c['name'] => Category::create($c)]);
        foreach ([['Chicken Tikka', 'BBQ', 480, 300, 'Charcoal grilled, Multani masala'], ['Malai Boti', 'BBQ', 720, 440, 'Creamy boneless chicken'], ['Mutton Karahi (Half)', 'Karahi', 1650, 1120, 'Fresh tomato and green chilli'], ['Chicken Karahi (Half)', 'Karahi', 1150, 690, 'Traditional desi ghee karahi'], ['Chicken Biryani', 'Rice', 390, 220, 'Aromatic basmati with raita'], ['Mutton Pulao', 'Rice', 560, 350, 'Slow cooked Multani pulao'], ['Zinger Burger', 'Fast Food', 590, 330, 'Crispy chicken with fries'], ['Mint Margarita', 'Drinks', 260, 90, 'Fresh mint and lemon'], ['Mineral Water', 'Drinks', 100, 45, '500ml bottle']] as $i) {
            MenuItem::create(['name' => $i[0], 'category_id' => $cats[$i[1]]->id, 'price' => $i[2], 'cost' => $i[3], 'description' => $i[4], 'featured' => in_array($i[0], ['Chicken Tikka', 'Chicken Biryani', 'Mutton Karahi (Half)'])]);
        }
        foreach ([
            'Chicken Tikka' => 'images/menu/chicken-tikka.png',
            'Malai Boti' => 'images/menu/malai-boti.png',
            'Mutton Karahi (Half)' => 'images/menu/mutton-karahi.png',
            'Chicken Karahi (Half)' => 'images/menu/chicken-karahi.png',
            'Chicken Biryani' => 'images/menu/chicken-biryani.png',
            'Mutton Pulao' => 'images/menu/mutton-pulao.png',
            'Zinger Burger' => 'images/menu/zinger-burger.png',
            'Mint Margarita' => 'images/menu/mint-margarita.png',
            'Mineral Water' => 'images/menu/mineral-water.png',
        ] as $name => $imagePath) {
            MenuItem::where('name', $name)->update(['image_path' => $imagePath]);
        }
        foreach (range(1, 12) as $i) {
            RestaurantTable::create(['name' => 'T-'.str_pad($i, 2, '0', STR_PAD_LEFT), 'area' => $i > 8 ? 'Rooftop' : 'Main Hall', 'capacity' => $i % 3 === 0 ? 6 : 4, 'status' => $i === 2 ? 'occupied' : 'available']);
        }
        Customer::create(['name' => 'Ali Raza', 'phone' => '0301-5551290', 'address' => 'Gulgasht Colony, Multan', 'loyalty_points' => 120]);
        foreach ([['Chicken', 'kg', 8, 10, 620, 'Al-Madina Poultry'], ['Basmati Rice', 'kg', 24, 12, 410, 'Hussain Agahi Traders'], ['Cooking Oil', 'litre', 7, 10, 585, 'Metro Multan'], ['Tomatoes', 'kg', 14, 8, 180, 'Sabzi Mandi Multan'], ['Soft Drinks', 'bottle', 48, 20, 85, 'Multan Beverages']] as $s) {
            InventoryItem::create(['name' => $s[0], 'unit' => $s[1], 'stock' => $s[2], 'minimum_stock' => $s[3], 'unit_cost' => $s[4], 'supplier' => $s[5]]);
        }
        Expense::create(['title' => 'Morning vegetable purchase', 'category' => 'Ingredients', 'amount' => 4800, 'expense_date' => now()]);
    }
}
