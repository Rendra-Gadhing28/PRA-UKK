<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case CANCELLED = 'cancelled';

    public function badgeClasses(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border border-amber-200',
            self::CONFIRMED => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800 border border-blue-200',
            self::COMPLETED => 'bg-purple-100 text-purple-800 border border-purple-200',
            self::CANCELED, self::CANCELLED => 'bg-rose-100 text-rose-800 border border-rose-200',
        };
    }

    public function badgeLabel(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::CONFIRMED => 'Terkonfirmasi',
            self::IN_PROGRESS => 'Sedang Berlangsung',
            self::COMPLETED => 'Selesai',
            self::CANCELED, self::CANCELLED => 'Dibatalkan',
        };
    }
}
