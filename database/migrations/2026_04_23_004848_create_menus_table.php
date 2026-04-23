<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('price_per_kilo', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // Insert the required rice products
        DB::table('menus')->insert([
            [
                'name' => 'Jasmine Rice',
                'category' => 'Premium',
                'price_per_kilo' => 85.00,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dinorado Rice',
                'category' => 'Premium',
                'price_per_kilo' => 95.00,
                'stock' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sinandomeng Rice',
                'category' => 'Regular',
                'price_per_kilo' => 52.00,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brown Rice',
                'category' => 'Health',
                'price_per_kilo' => 70.00,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};