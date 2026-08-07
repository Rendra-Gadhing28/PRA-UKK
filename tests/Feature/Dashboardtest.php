<?php

namespace Tests\Feature\User;

use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test suite untuk halaman dashboard pengguna.
 *
 * Memastikan data statistik, booking aktif,
 * dan riwayat booking tampil dengan benar.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_tidak_bisa_diakses_tanpa_login(): void
    {
        $this->get(route('user.dashboard'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function dashboard_berhasil_diakses_oleh_pengguna_yang_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertViewIs('user.dashboard');
    }

    /** @test */
    public function dashboard_menampilkan_data_statistik_booking_yang_benar(): void
    {
        $user = User::factory()->create();

        // Buat booking dengan berbagai status
        Booking::factory()->for($user)->create(['status' => 'completed']);
        Booking::factory()->for($user)->create(['status' => 'completed']);
        Booking::factory()->for($user)->create(['status' => 'canceled']);
        Booking::factory()->for($user)->create(['status' => 'pending']);

        $response = $this->actingAs($user)
            ->get(route('user.dashboard'));

        $response->assertViewHas('statistikBooking', function ($statistik) {
            return $statistik['total'] === 4
                && $statistik['selesai'] === 2
                && $statistik['dibatalkan'] === 1
                && $statistik['menunggu'] === 1;
        });
    }

    /** @test */
    public function dashboard_menampilkan_booking_aktif_dengan_eager_loading(): void
    {
        $user = User::factory()->create();

        Booking::factory()->for($user)->create(['status' => 'confirmed']);
        Booking::factory()->for($user)->create(['status' => 'completed']); // Tidak ditampilkan di aktif

        $response = $this->actingAs($user)
            ->get(route('user.dashboard'));

        $response->assertViewHas('bookingAktif', function ($bookings) {
            return $bookings->count() === 1
                && $bookings->first()->relationLoaded('treatments')
                && $bookings->first()->relationLoaded('beautician');
        });
    }

    /** @test */
    public function dashboard_menampilkan_maksimal_5_riwayat_booking(): void
    {
        $user = User::factory()->create();

        // Buat 7 booking selesai
        Booking::factory()->for($user)->count(7)->create(['status' => 'completed']);

        $response = $this->actingAs($user)
            ->get(route('user.dashboard'));

        $response->assertViewHas('bookingTerbaru', function ($bookings) {
            return $bookings->count() === 5;
        });
    }

    /** @test */
    public function dashboard_menampilkan_data_user_dengan_benar(): void
    {
        $user = User::factory()->create([
            'name'             => 'Dewi Sartika',
            'membership_level' => 'gold',
            'total_points'     => 500,
        ]);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertViewHas('user', $user)
            ->assertSee('Dewi')
            ->assertSee('Gold');
    }
}