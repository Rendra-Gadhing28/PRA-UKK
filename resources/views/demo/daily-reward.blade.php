<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Demo Daily Reward Card
        </h2>
    </x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/daily-reward-card.css') }}">
    @endpush

    <div class="min-h-screen py-16 px-4 flex items-center justify-center bg-[#fff8f8]">
        <div class="max-w-md w-full flex justify-center">
            <x-daily-reward-card
                badge="Aura Glow-Up Memancar!"
                title="Day 1: Muka Bantal Eradication!"
                message="Selamat Ratu Beauty! Kamu berhasil mengklaim misi hari ini. Bonus poin PTS telah disimpan ke akunmu!"
                :reward="25"
                :days="[
                    ['label' => 'Sen', 'status' => 'done'],
                    ['label' => 'Sel', 'status' => 'done'],
                    ['label' => 'Rab', 'status' => 'done'],
                    ['label' => 'Kam', 'status' => 'done'],
                    ['label' => 'Jum', 'status' => 'today'],
                    ['label' => 'Sab', 'status' => 'locked'],
                    ['label' => 'Min', 'status' => 'locked'],
                ]"
                cta-text="Siap Glow-Up Banget!"
                streak-label="Streak 7 hari untuk voucher diskon"
                :streak-days-left="3"
                :streak-progress="57"
            />
        </div>
    </div>
</x-app-layout>
