<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Treatments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
                ['name' => 'Behel Diamond', 'price' => 25000, 'duration' => 15, 'badge' => 'best_seller'],
                ['name' => 'Remove Behel', 'price' => 25000, 'duration' => 25, 'badge' => 'none'],
            ]
        ];

        $manager = new ImageManager(new Driver());

        foreach ($data as $categoryName => $treatments) {
            $category = Categories::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            $folderName = Str::slug($categoryName);

            foreach ($treatments as $index => $t) {
                $treatmentSlug = Str::slug($t['name']);
                $imagePathForDb = null;

                $possibleExtensions = ['jpg', 'jpeg', 'png'];
                $sourceFile = null;

                foreach ($possibleExtensions as $ext) {
                    $checkPath = public_path("images/treatments/{$folderName}/{$treatmentSlug}.{$ext}");
                    if (file_exists($checkPath)) {
                        $sourceFile = $checkPath;
                        break;
                    }
                }

                if ($sourceFile) {
                    try {
                        // Gunakan ImageManager v4
                        $image = $manager->read($sourceFile);
                        $encodedImage = $image->toWebp(80);

                        $destinationDir = "treatments/{$folderName}";
                        $destinationPath = "{$destinationDir}/{$treatmentSlug}.webp";

                        if (!Storage::disk('public')->exists($destinationDir)) {
                            Storage::disk('public')->makeDirectory($destinationDir);
                        }

                        // Simpan stream binary hasil encode()
                        Storage::disk('public')->put($destinationPath, (string) $encodedImage);
                        $imagePathForDb = $destinationPath;

                        $this->command->info("✅ Berhasil convert: {$destinationPath}");
                    } catch (\Exception $e) {
                        $this->command->error("❌ Gagal convert {$sourceFile}: " . $e->getMessage());
                    }
                } else {
                    $this->command->warn("⚠️ Gambar tidak ditemukan untuk: {$t['name']}");
                }

                Treatments::create([
                    'category_id' => $category->id,
                    'name' => $t['name'],
                    'slug' => $treatmentSlug.'-'.$category->id,
                    'description' => "Layanan {$t['name']} yang dilakukan oleh beautician profesional dengan produk berkualitas.",
                    'benefits' => "Membuat kulit/tubuh lebih sehat, segar, dan terawat.",
                    'price' => $t['price'],
                    'duration_minutes' => $t['duration'],
                    'images' => $imagePathForDb,
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