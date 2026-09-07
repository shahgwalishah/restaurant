<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });

        $images = [
            'Chicken Tikka' => 'images/menu/chicken-tikka.png',
            'Malai Boti' => 'images/menu/malai-boti.png',
            'Mutton Karahi (Half)' => 'images/menu/mutton-karahi.png',
            'Chicken Karahi (Half)' => 'images/menu/chicken-karahi.png',
            'Chicken Biryani' => 'images/menu/chicken-biryani.png',
            'Mutton Pulao' => 'images/menu/mutton-pulao.png',
            'Zinger Burger' => 'images/menu/zinger-burger.png',
            'Mint Margarita' => 'images/menu/mint-margarita.png',
            'Mineral Water' => 'images/menu/mineral-water.png',
        ];

        foreach ($images as $name => $imagePath) {
            DB::table('menu_items')->where('name', $name)->update(['image_path' => $imagePath]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
