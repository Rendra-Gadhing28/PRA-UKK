<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test suite untuk sistem autentikasi Yalia Beauty.
 *
 * Mencakup: registrasi, login (email & nomor HP),
 * proteksi akun nonaktif, dan logout.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // HALAMAN FORM
    // =========================================================

    /** @test */
    public function halaman_login_dapat_diakses_oleh_tamu(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login');
    }

    /** @test */
    public function halaman_register_dapat_diakses_oleh_tamu(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertViewIs('auth.register');
    }

    /** @test */
    public function pengguna_yang_sudah_login_diredirect_dari_halaman_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    }

    // =========================================================
    // REGISTRASI
    // =========================================================

    /** @test */
    public function pengguna_baru_dapat_mendaftar_dengan_data_valid(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'Siti Rahayu',
            'email'                 => 'siti@contoh.com',
            'phone'                 => '081234567890',
            'password'              => 'Password1',
            'password_confirmation' => 'Password1',
        ])->assertRedirect(route('user.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email'            => 'siti@contoh.com',
            'membership_level' => 'regular',
            'is_active'        => true,
            'is_admin'         => false,
        ]);
    }

    /** @test */
    public function registrasi_gagal_jika_email_sudah_digunakan(): void
    {
        User::factory()->create(['email' => 'ada@contoh.com']);

        $this->post(route('register.post'), [
            'name'                  => 'User Baru',
            'email'                 => 'ada@contoh.com',
            'phone'                 => '081111111111',
            'password'              => 'Password1',
            'password_confirmation' => 'Password1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function registrasi_gagal_jika_password_tidak_cocok(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'User Test',
            'email'                 => 'test@contoh.com',
            'phone'                 => '081222222222',
            'password'              => 'Password1',
            'password_confirmation' => 'BedaPassword1',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    /** @test */
    public function registrasi_gagal_jika_format_nomor_hp_salah(): void
    {
        $this->post(route('register.post'), [
            'name'                  => 'User Test',
            'email'                 => 'test@contoh.com',
            'phone'                 => '123456', // Format salah
            'password'              => 'Password1',
            'password_confirmation' => 'Password1',
        ])->assertSessionHasErrors('phone');
    }

    // =========================================================
    // LOGIN DENGAN EMAIL
    // =========================================================

    /** @test */
    public function pengguna_dapat_login_dengan_email(): void
    {
        $user = User::factory()->create([
            'email'    => 'budi@contoh.com',
            'password' => bcrypt('RahasiaKu1'),
        ]);

        $this->post(route('login.post'), [
            'email'    => 'budi@contoh.com',
            'password' => 'RahasiaKu1',
        ])->assertRedirect(route('user.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function pengguna_diredirect_ke_admin_dashboard_jika_adalah_admin(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@yalia.com',
            'password' => bcrypt('AdminPassword1'),
            'is_admin' => true,
        ]);

        $this->post(route('login.post'), [
            'email'    => 'admin@yalia.com',
            'password' => 'AdminPassword1',
        ])->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function login_gagal_dengan_password_salah(): void
    {
        User::factory()->create(['email' => 'test@contoh.com']);

        $this->post(route('login.post'), [
            'email'    => 'test@contoh.com',
            'password' => 'PasswordSalah1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // =========================================================
    // LOGIN DENGAN NOMOR HP
    // =========================================================

    /** @test */
    public function pengguna_dapat_login_dengan_nomor_hp(): void
    {
        $user = User::factory()->create([
            'phone'    => '081234567890',
            'password' => bcrypt('RahasiaKu1'),
        ]);

        $this->post(route('login.post'), [
            'email'    => '081234567890', // field 'email' digunakan untuk nomor HP juga
            'password' => 'RahasiaKu1',
        ])->assertRedirect(route('user.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    // =========================================================
    // PROTEKSI AKUN NONAKTIF
    // =========================================================

    /** @test */
    public function login_gagal_untuk_akun_yang_dinonaktifkan(): void
    {
        User::factory()->create([
            'email'     => 'nonaktif@contoh.com',
            'password'  => bcrypt('Password1'),
            'is_active' => false,
        ]);

        $this->post(route('login.post'), [
            'email'    => 'nonaktif@contoh.com',
            'password' => 'Password1',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // =========================================================
    // LOGOUT
    // =========================================================

    /** @test */
    public function pengguna_dapat_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // =========================================================
    // PROTEKSI ROUTE
    // =========================================================

    /** @test */
    public function dashboard_user_tidak_bisa_diakses_tanpa_login(): void
    {
        $this->get(route('user.dashboard'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function dashboard_admin_tidak_bisa_diakses_oleh_user_biasa(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('user.dashboard'));
    }
}