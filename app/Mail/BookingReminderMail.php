<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Bookings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Bookings $booking;

    public string $reminderType;

    /**
     * Inisialisasi mailable dengan data booking dan jenis pengingat.
     */
    public function __construct(Bookings $booking, string $reminderType)
    {
        $this->booking = $booking;
        $this->reminderType = $reminderType;
    }

    /**
     * Tentukan amplop email (subjek & pengirim).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pengingat Reservasi Yalia Beauty Salon ({$this->reminderType}) - Code: {$this->booking->booking_code}",
        );
    }

    /**
     * Tentukan template view Blade dan variabel konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_reminder',
            with: [
                'booking' => $this->booking,
                'reminderType' => $this->reminderType,
            ],
        );
    }

    /**
     * Tentukan lampiran file untuk email jika ada.
     */
    public function attachments(): array
    {
        return [];
    }
}
