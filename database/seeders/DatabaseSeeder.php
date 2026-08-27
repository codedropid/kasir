<?php

namespace Database\Seeders;

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
        // 1. Users
        User::updateOrCreate(
            ['email' => 'admin@kafe.com'],
            [
                'name' => 'Admin Kafe',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@kafe.com'],
            [
                'name' => 'Kasir Utama',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );

        // 2. Payment Methods
        $paymentMethods = [
            ['name' => 'Tunai', 'is_active' => true],
            ['name' => 'QRIS', 'is_active' => true],
            ['name' => 'Kartu Debit', 'is_active' => true],
            ['name' => 'Transfer Bank', 'is_active' => true],
        ];

        foreach ($paymentMethods as $pm) {
            \App\Models\PaymentMethod::updateOrCreate(['name' => $pm['name']], $pm);
        }

        // 3. Categories
        $catMakanan = \App\Models\Category::updateOrCreate(
            ['slug' => 'makanan'],
            ['name' => 'Makanan', 'icon' => 'utensils']
        );

        $catMinuman = \App\Models\Category::updateOrCreate(
            ['slug' => 'minuman'],
            ['name' => 'Minuman', 'icon' => 'cup-soda']
        );

        $catSnack = \App\Models\Category::updateOrCreate(
            ['slug' => 'snack-camilan'],
            ['name' => 'Snack / Camilan', 'icon' => 'cookie']
        );

        // 4. Products
        $products = [
            // Makanan
            [
                'category_id' => $catMakanan->id,
                'name' => 'Nasi Goreng Spesial Kafe',
                'sku' => 'MKN-001',
                'price' => 28000,
                'cost_price' => 14000,
                'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Spaghetti Carbonara Creamy',
                'sku' => 'MKN-002',
                'price' => 35000,
                'cost_price' => 18000,
                'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Chicken Cordon Bleu',
                'sku' => 'MKN-003',
                'price' => 42000,
                'cost_price' => 22000,
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Beef Burger with Fries',
                'sku' => 'MKN-004',
                'price' => 38000,
                'cost_price' => 19000,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Rice Bowl Sambal Matah',
                'sku' => 'MKN-005',
                'price' => 30000,
                'cost_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],

            // Minuman
            [
                'category_id' => $catMinuman->id,
                'name' => 'Espresso Double Shot',
                'sku' => 'MNM-001',
                'price' => 22000,
                'cost_price' => 8000,
                'image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Iced Caramel Macchiato',
                'sku' => 'MNM-002',
                'price' => 30000,
                'cost_price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Matcha Green Tea Latte',
                'sku' => 'MNM-003',
                'price' => 28000,
                'cost_price' => 11000,
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Iced Palm Sugar Latte (Kopi Susu Gula Aren)',
                'sku' => 'MNM-004',
                'price' => 25000,
                'cost_price' => 9500,
                'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Strawberry Mojito Sparkling',
                'sku' => 'MNM-005',
                'price' => 26000,
                'cost_price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],

            // Snack & Camilan
            [
                'category_id' => $catSnack->id,
                'name' => 'French Fries Truffle Oil',
                'sku' => 'SNK-001',
                'price' => 22000,
                'cost_price' => 9000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Crispy Chicken Wings BBQ',
                'sku' => 'SNK-002',
                'price' => 28000,
                'cost_price' => 13000,
                'image' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Choco Lava Cake Warm',
                'sku' => 'SNK-003',
                'price' => 25000,
                'cost_price' => 11000,
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Churros Cinnamon Sugar Dip',
                'sku' => 'SNK-004',
                'price' => 23000,
                'cost_price' => 8500,
                'image' => 'https://images.unsplash.com/photo-1624300629298-e9de39c13be5?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Pisang Goreng Keju Brown Sugar',
                'sku' => 'SNK-005',
                'price' => 20000,
                'cost_price' => 7500,
                'image' => 'https://images.unsplash.com/photo-1528751014936-863e6e7a319c?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
        ];

        foreach ($products as $prod) {
            \App\Models\Product::updateOrCreate(['sku' => $prod['sku']], $prod);
        }
    }
}
