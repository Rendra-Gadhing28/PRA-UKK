<?php
 
namespace Database\Seeders;
 
use App\Models\Categories;
use App\Models\Treatments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
 
class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Perawatan Wajah' => [
                ['name' => 'Facial Basic', 'price' => 50000, 'duration' => 45, 'badge' => 'none'],
                ['name' => 'Facial Acne Treatment', 'price' => 120000, 'duration' => 60, 'badge' => 'none'],
                ['name' => 'Facial Glowing Whitening', 'price' => 150000, 'duration' => 60, 'badge' => 'best_seller'],
                ['name' => 'Hydrafacial', 'price' => 250000, 'duration' => 75, 'badge' => 'new'],
            ],
            'Eyelash' => [
                ['name' => 'Lash Lift and Tint', 'price' => 55000, 'duration' => 20, 'badge' => 'new'],
                ['name' => 'Eyelash Type Y', 'price' => 65000, 'duration' => 35, 'badge' => 'best_seller'],
                ['name' => 'Eyelash Medium', 'price' => 65000, 'duration' => 35, 'badge' => 'none'],
                ['name' => 'Eyelash Natural', 'price' => 65000, 'duration' => 35, 'badge' => 'none'],
                ['name' => 'Eyelash volume', 'price' => 85000, 'duration' => 25, 'badge' => 'none'],
                ['name' => 'Retouch', 'price' => 35000, 'duration' => 30, 'badge' => 'none'],
                ['name' => 'Remove', 'price' => 35000, 'duration' => 30, 'badge' => 'none'],
            ],
            'Perawatan Rambut' => [
                ['name' => 'Creambath', 'price' => 60000, 'duration' => 45, 'badge' => 'best_seller'],
                ['name' => 'Hair Spa', 'price' => 85000, 'duration' => 60, 'badge' => 'none'],
                ['name' => 'Hair Coloring', 'price' => 180000, 'duration' => 90, 'badge' => 'none'],
                ['name' => 'Rebonding', 'price' => 350000, 'duration' => 150, 'badge' => 'none'],
            ],
            'Perawatan Kuku' => [
                ['name' => 'Nail Polos', 'price' => 45000, 'duration' => 30, 'badge' => 'none'],
                ['name' => 'Nail Simple', 'price' => 50000, 'duration' => 30, 'badge' => 'none'],
                ['name' => 'Nail Full Motif', 'price' => 75000, 'duration' => 45, 'badge' => 'new'],
                ['name' => 'Kupal Polos', 'price' => 65000, 'duration' => 40, 'badge' => 'best_seller'],
                ['name' => 'Kupal Simple', 'price' => 65000, 'duration' => 40, 'badge' => 'best_seller'],
                ['name' => 'Kupal Full Motif', 'price' => 75000, 'duration' => 45, 'badge' => 'new'],
            ],
            'Behel' => [
                ['name' => 'Behel Diamond', 'price' => 25000, 'duration' => 15, 'badge' => 'new'],
                ['name' => 'Behel Fasion Atas', 'price' => 120000, 'duration' => 45, 'badge' => 'best_seller'],
                ['name' => 'Behel Fasion Bawah', 'price' => 120000, 'duration' => 45, 'badge' => 'best_seller'],
                ['name' => 'Behel Perawatan Atas', 'price' => 180000, 'duration' => 45, 'badge' => 'none'],
                ['name' => 'Behel Perawatan Bawah', 'price' => 180000, 'duration' => 45, 'badge' => 'none'],
                ['name' => 'Remove Behel', 'price' => 25000, 'duration' => 25, 'badge' => 'none'],
            ]
        ];
 
        foreach ($data as $categoryName => $treatments) {
            $category = Categories::where('name', $categoryName)->first();
 
            if (! $category) {
                continue;
            }
 
            foreach ($treatments as $index => $t) {
                Treatments::create([
                    'category_id' => $category->id,
                    'name' => $t['name'],
                    'slug' => Str::slug($t['name']).'-'.$category->id,
                    'description' => "Layanan {$t['name']} yang dilakukan oleh beautician profesional dengan produk berkualitas.",
                    'benefits' => "Membuat kulit/tubuh lebih sehat, segar, dan terawat.",
                    'price' => $t['price'],
                    'duration_minutes' => $t['duration'],
                    'images' => null,
                    'badge' => $t['badge'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'rating' => fake()->randomFloat(1, 4.0, 5.0),
                    'rating_count' => fake()->numberBetween(5, 120),
                ]);
            }
        }
    }
}
 
