<footer class="bg-surface-container-highest full-width mt-section-gap">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter px-margin-desktop py-12 w-full max-w-container-max mx-auto">
        <div class="space-y-4">
            <div class="text-headline-sm font-headline-sm font-bold text-on-surface">Yalia Beauty</div>
            <p class="text-body-sm font-body-sm text-on-surface-variant">Elevating your natural beauty with expert care and high-end aesthetic treatments.</p>
        </div>
        <div class="space-y-4">
            <div class="text-label-lg font-label-lg text-primary">Quick Links</div>
            <ul class="space-y-2">
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="{{ route('user.dashboard') }}">My Dashboard</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="{{ route('user.treatments.index') }}">Treatments Menu</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">Gift Cards</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">Member Benefits</a></li>
            </ul>
        </div>
        <div class="space-y-4">
            <div class="text-label-lg font-label-lg text-primary">Support</div>
            <ul class="space-y-2">
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">Contact Us</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">Terms of Service</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">Privacy Policy</a></li>
                <li><a class="text-body-sm font-body-sm text-on-surface-variant hover:text-primary" href="#">FAQs</a></li>
            </ul>
        </div>
        <div class="space-y-4">
            <div class="text-label-lg font-label-lg text-primary">Connect</div>
            <div class="flex gap-4">
                <a class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-transform" href="#" aria-label="Share">
                    <span class="material-symbols-outlined">share</span>
                </a>
                <a class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-transform" href="{{ route('profile.edit') }}" aria-label="Profile">
                    <span class="material-symbols-outlined">person</span>
                </a>
            </div>
        </div>
    </div>
    <div class="border-t border-border-subtle py-6 px-margin-desktop text-center">
        <p class="text-body-sm font-body-sm text-on-surface-variant">&copy; {{ now()->year }} Yalia Beauty Boutique. All rights reserved.</p>
    </div>
</footer>
