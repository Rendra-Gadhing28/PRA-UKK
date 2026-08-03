<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name'=>'Perawatan Wajah', 'slug'=>'perawatan-wajah', 'icon'=>'fas fa-face-smile', 'description'=>'Perawatan wajah untuk kulit sehat dan bersinar', 'is_active'=>true, 'sort_order'=>1],
            ['name'=>'Eyelash', 'slug'=>'eyelash', 'icon'=>'fas fa-eye', 'description'=>'Perawatan bulu mata untuk penampilan yang indah', 'is_active'=>true, 'sort_order'=>2],
            ['name'=>'Perawatan Rambut', 'slug'=>'perawatan-rambut', 'icon'=>'fas fa-hair', 'description'=>'Perawatan rambut untuk kesehatan dan keindahan rambut', 'is_active'=>true, 'sort_order'=>3],
            ['name'=>'Perawatan Kuku', 'slug'=>'perawatan-kuku', 'icon'=>'fas fa-hand-sparkles', 'description'=>'Perawatan kuku tangan dan kaki untuk penampilan yang rapi dan cantik', 'is_active'=>true, 'sort_order'=>4],
            ['name'=>'Behel', 'slug'=>'behel', 'icon'=>'fas fa-spa', 'description'=>'Layanan behel untuk penampilan yang indah', 'is_active'=>true, 'sort_order'=>5],
        ];

        foreach ($categories as $category) {
            Categories::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'icon' => $category['icon'],
                'description' => $category['description'],
                'is_active' => $category['is_active'],
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}
