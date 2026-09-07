<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RestaurantWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_create_and_complete_a_dine_in_order(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'BBQ']);
        $item = MenuItem::create(['category_id' => $category->id, 'name' => 'Chicken Tikka', 'price' => 500, 'cost' => 300]);
        $table = RestaurantTable::create(['name' => 'T-01', 'capacity' => 4]);

        $this->actingAs($user)->post('/orders', ['type' => 'dine_in', 'table_id' => $table->id, 'items' => [['id' => $item->id, 'quantity' => 2]]])->assertRedirect();
        $order = Order::first();

        $this->assertSame(1050.0, $order->total);
        $this->assertSame('occupied', $table->fresh()->status);
        $this->actingAs($user)->patch("/orders/{$order->id}/pay", ['payment_method' => 'easypaisa'])->assertRedirect();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('available', $table->fresh()->status);
    }

    public function test_authenticated_user_can_add_a_menu_item_with_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Desserts']);

        $response = $this->actingAs($user)->post('/menu-items', [
            'name' => 'Multani Sohan Halwa',
            'category_id' => $category->id,
            'price' => 650,
            'cost' => 400,
            'description' => 'Traditional Multani sweet',
            'image' => UploadedFile::fake()->image('sohan-halwa.jpg', 800, 800),
        ]);

        $response->assertRedirect()->assertSessionHas('success');
        $menuItem = MenuItem::where('name', 'Multani Sohan Halwa')->firstOrFail();
        $this->assertStringStartsWith('storage/menu-items/', $menuItem->image_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $menuItem->image_path));
    }

    public function test_menu_item_image_rejects_non_image_uploads(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Desserts']);

        $response = $this->actingAs($user)->post('/menu-items', [
            'name' => 'Unsafe upload',
            'category_id' => $category->id,
            'price' => 500,
            'image' => UploadedFile::fake()->create('menu.svg', 20, 'image/svg+xml'),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('menu_items', ['name' => 'Unsafe upload']);
    }

    public function test_kitchen_dashboard_provides_orders_and_stats_for_polling(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Restaurant')
            ->has('orders')
            ->has('stats'));
    }

    public function test_authenticated_user_can_add_a_menu_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Desserts',
            'color' => '#E75B3D',
        ]);

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'name' => 'Desserts',
            'color' => '#E75B3D',
            'active' => true,
        ]);
    }

    public function test_duplicate_menu_category_is_rejected(): void
    {
        $user = User::factory()->create();
        Category::create(['name' => 'Desserts', 'color' => '#E75B3D']);

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Desserts',
            'color' => '#8056A3',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertSame(1, Category::where('name', 'Desserts')->count());
    }
}
