<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen relative overflow-hidden">
        {{-- Background Ornaments --}}
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-amber-200/40 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[400px] h-[400px] bg-rose-200/40 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Profile Info Section --}}
                <div class="md:col-span-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Profile Information</h3>
                    <p class="text-gray-500 text-sm">Update your account's profile information, email address, and role.</p>
                </div>
                <div class="md:col-span-2 bg-white/80 backdrop-blur-xl shadow-lg border border-gray-100 sm:rounded-2xl p-6 sm:p-10 transition-all hover:shadow-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="md:col-span-1 mt-8 md:mt-0">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Update Password</h3>
                    <p class="text-gray-500 text-sm">Ensure your account is using a long, random password to stay secure.</p>
                </div>
                <div class="md:col-span-2 bg-white/80 backdrop-blur-xl shadow-lg border border-gray-100 sm:rounded-2xl p-6 sm:p-10 transition-all hover:shadow-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Delete Account Section --}}
                <div class="md:col-span-1 mt-8 md:mt-0">
                    <h3 class="text-xl font-bold text-red-600 mb-2">Danger Zone</h3>
                    <p class="text-gray-500 text-sm">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                </div>
                <div class="md:col-span-2 bg-red-50/50 backdrop-blur-xl shadow-lg border border-red-100 sm:rounded-2xl p-6 sm:p-10 transition-all hover:shadow-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
