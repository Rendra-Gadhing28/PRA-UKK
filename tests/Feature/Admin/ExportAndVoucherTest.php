<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Vouchers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportAndVoucherTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_export_bookings_excel(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get(route('admin.bookings.export.excel'));

        $response->assertSuccessful();
        Excel::assertDownloaded('Laporan_Booking_Yalia_Beauty_'.now()->format('Ymd_His').'.xlsx');
    }

    public function test_admin_can_export_monthly_dashboard_excel(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get(route('admin.export.excel'));

        $response->assertSuccessful();
        Excel::assertDownloaded('Laporan_Keuangan_Yalia_Beauty_'.now()->format('Y_m').'.xlsx');
    }

    public function test_voucher_code_auto_generated_from_name_if_empty(): void
    {
        $payload = [
            'code' => '',
            'name' => 'Promo Diskon Cantik Lebaran',
            'type' => 'percentage',
            'value' => 15,
            'min_purchase' => 50000,
            'max_discount' => 20000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
            'quota' => 30,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.vouchers.store'), $payload);

        $response->assertRedirect(route('admin.vouchers.index'));
        $voucher = Vouchers::where('name', 'Promo Diskon Cantik Lebaran')->first();
        $this->assertNotNull($voucher);
        $this->assertStringStartsWith('PROMO-DISKON-C', $voucher->code);
    }
}
