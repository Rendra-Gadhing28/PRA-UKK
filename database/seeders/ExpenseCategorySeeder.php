<?php

namespace Database\Seeders;

use App\Models\ExpenseCategories;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gaji Karyawan', 'icon' => 'users'],
            ['name' => 'Bahan & Produk', 'icon' => 'package'],
            ['name' => 'Sewa Tempat', 'icon' => 'home'],
            ['name' => 'Listrik & Air', 'icon' => 'zap'],
            ['name' => 'Marketing & Promosi', 'icon' => 'megaphone'],
            ['name' => 'Peralatan Salon', 'icon' => 'wrench'],
            ['name' => 'Lain-lain', 'icon' => 'more-horizontal'],
        ];

        foreach ($categories as $category) {
            ExpenseCategories::create([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'description' => "Pengeluaran untuk kebutuhan {$category['name']}.",
                'is_active' => true,
            ]);
        }
    }
}