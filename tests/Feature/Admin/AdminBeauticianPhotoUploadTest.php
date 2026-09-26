<?php

namespace Tests\Feature\Admin;

use App\Models\Beauticians;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBeauticianPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_photo_when_creating_beautician(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->image('terapis.jpg');

        $response = $this->actingAs($admin)->post(route('admin.beauticians.store'), [
            'name' => 'Siti Terapis',
            'phone' => '08123456789',
            'email' => 'siti@yaliabeauty.test',
            'bio' => 'Spesialis Facial Glow',
            'is_active' => '1',
            'photo' => $file,
        ]);

        $response->assertRedirect(route('admin.beauticians.index'));

        $beautician = Beauticians::where('email', 'siti@yaliabeauty.test')->first();
        $this->assertNotNull($beautician);
        $this->assertNotNull($beautician->photo);

        Storage::disk('public')->assertExists(Beauticians::PHOTO_DIRECTORY.'/'.$beautician->photo);
        $this->assertStringContainsString('storage/beauticians/', $beautician->photo_url);
    }

    public function test_admin_can_update_beautician_photo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $oldFile = UploadedFile::fake()->image('old_photo.jpg');
        $oldFileName = 'old_photo.jpg';
        $oldFile->storeAs(Beauticians::PHOTO_DIRECTORY, $oldFileName, 'public');

        $beautician = Beauticians::create([
            'name' => 'Rina Cantik',
            'phone' => '0899887766',
            'email' => 'rina@yaliabeauty.test',
            'bio' => 'Spesialis Rambut',
            'photo' => $oldFileName,
            'is_active' => true,
        ]);

        $newFile = UploadedFile::fake()->image('new_photo.png');

        $response = $this->actingAs($admin)->put(route('admin.beauticians.update', $beautician), [
            'name' => 'Rina Cantik Updated',
            'phone' => '0899887766',
            'email' => 'rina@yaliabeauty.test',
            'bio' => 'Spesialis Rambut & Nail Art',
            'is_active' => '1',
            'photo' => $newFile,
        ]);

        $response->assertRedirect(route('admin.beauticians.index'));

        $beautician->refresh();
        $this->assertNotEquals($oldFileName, $beautician->photo);
        Storage::disk('public')->assertMissing(Beauticians::PHOTO_DIRECTORY.'/'.$oldFileName);
        Storage::disk('public')->assertExists(Beauticians::PHOTO_DIRECTORY.'/'.$beautician->photo);
    }
}
