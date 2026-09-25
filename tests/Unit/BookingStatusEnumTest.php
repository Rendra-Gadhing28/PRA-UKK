<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use PHPUnit\Framework\TestCase;

class BookingStatusEnumTest extends TestCase
{
    public function test_booking_status_enum_values_and_labels(): void
    {
        $this->assertEquals('Menunggu Pembayaran', BookingStatus::PENDING->badgeLabel());
        $this->assertEquals('Terkonfirmasi', BookingStatus::CONFIRMED->badgeLabel());
        $this->assertEquals('Sedang Berlangsung', BookingStatus::IN_PROGRESS->badgeLabel());
        $this->assertEquals('Selesai', BookingStatus::COMPLETED->badgeLabel());
        $this->assertEquals('Dibatalkan', BookingStatus::CANCELED->badgeLabel());
    }

    public function test_booking_status_enum_badge_classes(): void
    {
        $this->assertStringContainsString('amber', BookingStatus::PENDING->badgeClasses());
        $this->assertStringContainsString('emerald', BookingStatus::CONFIRMED->badgeClasses());
        $this->assertStringContainsString('blue', BookingStatus::IN_PROGRESS->badgeClasses());
        $this->assertStringContainsString('purple', BookingStatus::COMPLETED->badgeClasses());
        $this->assertStringContainsString('rose', BookingStatus::CANCELED->badgeClasses());
    }
}
