<style>
    /* Custom animations for the background blobs */
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>

<!-- REVIEWS SECTION -->
<section class="relative min-h-screen py-24 px-4 md:px-8 overflow-hidden flex items-center justify-center">
    
    <!-- Colorful Animated Background (Mesh/Blob Gradient) -->
    <div class="absolute inset-0 w-full h-full bg-slate-50 overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-[20%] right-[-5%] w-[32rem] h-[32rem] bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[28rem] h-[28rem] bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
        <div class="absolute bottom-[10%] right-[20%] w-80 h-80 bg-yellow-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto w-full">
        
        <!-- Section Header -->
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-pink-600">
                What Our Clients Say
            </h2>
            <p class="text-slate-600 max-w-2xl mx-auto text-lg">
                Discover the experiences of our lovely clients. Authentic reviews from our treatments, curated just for you.
            </p>
        </div>

        <!-- Reviews Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start">
            
            @forelse($reviews ?? [] as $review)
                <!-- Dynamic Review Card -->
                <article class="group bg-white/30 backdrop-blur-md border border-white/40 p-6 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] hover:-translate-y-2 hover:bg-white/40 hover:shadow-[0_16px_40px_0_rgba(31,38,135,0.1)] transition-all duration-300 flex flex-col gap-6">
                    
                    <!-- Header: Customer & Beautician -->
                    <header class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <img src="{{ $review->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name ?? 'User') }}" alt="Customer Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm">
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-slate-800 text-base leading-tight">{{ $review->user->name ?? 'Anonymous' }}</h3>
                                <time datetime="{{ $review->created_at->toIso8601String() }}" class="text-xs text-slate-500 mt-0.5">{{ $review->created_at->diffForHumans() }}</time>
                            </div>
                        </div>
                        
                        @if($review->beautician)
                        <div class="flex items-center gap-2 bg-white/50 px-3 py-1.5 rounded-full border border-white/30 shadow-sm backdrop-blur-md">
                            <img src="{{ $review->beautician->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->beautician->name) }}" alt="Beautician" class="w-5 h-5 rounded-full object-cover">
                            <span class="text-xs font-medium text-slate-700">by {{ $review->beautician->name }}</span>
                        </div>
                        @endif
                    </header>

                    <!-- Rating -->
                    <div class="flex items-center gap-1 text-yellow-500">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300/60 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                    </div>

                    <!-- Comment -->
                    @if($review->comment)
                    <p class="text-slate-800 leading-relaxed text-sm/6 font-medium">
                        "{{ $review->comment }}"
                    </p>
                    @endif

                    <!-- Photo Thumbnail -->
                    @if($review->photo)
                    <figure class="rounded-xl overflow-hidden border border-white/40 shadow-sm relative group/img cursor-pointer mt-auto">
                        <img src="{{ \App\Support\ImageHelper::url($review->photo) }}" alt="Treatment result" class="w-full h-48 object-cover transform group-hover/img:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/10 group-hover/img:bg-transparent transition-colors duration-300"></div>
                        <div class="absolute bottom-3 right-3 bg-white/60 backdrop-blur-md rounded-full p-2 shadow-sm opacity-0 group-hover/img:opacity-100 transition-opacity duration-300">
                            <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                    </figure>
                    @endif

                    <!-- Admin Reply Block -->
                    @if($review->admin_reply)
                    <div class="mt-2 p-4 rounded-xl bg-white/40 border border-white/60 relative shadow-inner backdrop-blur-sm">
                        <!-- Reply Badge -->
                        <div class="absolute -top-3 left-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-md">
                            Yalia Beauty Reply
                        </div>
                        <div class="flex items-start gap-3 mt-2">
                            <svg class="w-5 h-5 text-pink-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            <p class="text-sm/6 text-slate-800">
                                {{ $review->admin_reply }}
                            </p>
                        </div>
                    </div>
                    @endif
                </article>
            @empty
                <!-- Fallback/Dummy Dummy when no reviews passed (For Preview/Testing) -->
                <!-- CARD 1 -->
                <article class="group bg-white/30 backdrop-blur-md border border-white/40 p-6 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] hover:-translate-y-2 hover:bg-white/40 hover:shadow-[0_16px_40px_0_rgba(31,38,135,0.1)] transition-all duration-300 flex flex-col gap-6">
                    <header class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/150?img=44" alt="Customer Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm">
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-slate-800 text-base leading-tight">Sarah Jenkins</h3>
                                <time datetime="2026-08-25" class="text-xs text-slate-500 mt-0.5">3 days ago</time>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/50 px-3 py-1.5 rounded-full border border-white/30 shadow-sm backdrop-blur-md">
                            <img src="https://i.pravatar.cc/150?img=47" alt="Beautician Avatar" class="w-5 h-5 rounded-full object-cover">
                            <span class="text-xs font-medium text-slate-700">by Anna</span>
                        </div>
                    </header>
                    <div class="flex items-center gap-1 text-yellow-500">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-800 leading-relaxed text-sm/6 font-medium">
                        "I absolutely loved the Signature Glow Facial treatment. Anna was very professional, gentle, and explained every step clearly. My skin feels so refreshed, hydrated, and glowing. Will definitely come back for another session next month!"
                    </p>
                </article>

                <!-- CARD 2 WITH PHOTO -->
                <article class="group bg-white/30 backdrop-blur-md border border-white/40 p-6 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] hover:-translate-y-2 hover:bg-white/40 hover:shadow-[0_16px_40px_0_rgba(31,38,135,0.1)] transition-all duration-300 flex flex-col gap-6">
                    <header class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/150?img=5" alt="Customer Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm">
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-slate-800 text-base leading-tight">Elena Gomez</h3>
                                <time datetime="2026-08-20" class="text-xs text-slate-500 mt-0.5">1 week ago</time>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/50 px-3 py-1.5 rounded-full border border-white/30 shadow-sm backdrop-blur-md">
                            <img src="https://i.pravatar.cc/150?img=32" alt="Beautician Avatar" class="w-5 h-5 rounded-full object-cover">
                            <span class="text-xs font-medium text-slate-700">by Chloe</span>
                        </div>
                    </header>
                    <div class="flex items-center gap-1 text-yellow-500">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 text-gray-300/60 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-800 leading-relaxed text-sm/6 font-medium">
                        "Got my nails done for a wedding, and the result is stunning! The nail art is so detailed and neat. Highly recommend this place."
                    </p>
                    <figure class="rounded-xl overflow-hidden border border-white/40 shadow-sm relative group/img cursor-pointer mt-auto">
                        <img src="https://images.unsplash.com/photo-1519014816548-bf5fe059e98b?auto=format&fit=crop&w=600&q=80" alt="Nail treatment result" class="w-full h-48 object-cover transform group-hover/img:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/10 group-hover/img:bg-transparent transition-colors duration-300"></div>
                        <div class="absolute bottom-3 right-3 bg-white/60 backdrop-blur-md rounded-full p-2 shadow-sm opacity-0 group-hover/img:opacity-100 transition-opacity duration-300">
                            <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                    </figure>
                </article>

                <!-- CARD 3 WITH ADMIN REPLY -->
                <article class="group bg-white/30 backdrop-blur-md border border-white/40 p-6 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] hover:-translate-y-2 hover:bg-white/40 hover:shadow-[0_16px_40px_0_rgba(31,38,135,0.1)] transition-all duration-300 flex flex-col gap-6 lg:mt-8">
                    <header class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Customer Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm">
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-slate-800 text-base leading-tight">Michael T.</h3>
                                <time datetime="2026-08-15" class="text-xs text-slate-500 mt-0.5">2 weeks ago</time>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/50 px-3 py-1.5 rounded-full border border-white/30 shadow-sm backdrop-blur-md">
                            <img src="https://i.pravatar.cc/150?img=68" alt="Beautician Avatar" class="w-5 h-5 rounded-full object-cover">
                            <span class="text-xs font-medium text-slate-700">by David</span>
                        </div>
                    </header>
                    <div class="flex items-center gap-1 text-yellow-500">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 text-gray-300/60 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5 text-gray-300/60 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-800 leading-relaxed text-sm/6 font-medium">
                        "The massage was great and very relaxing, but the room was a bit too cold for my liking which distracted me a little. Otherwise, a perfect experience."
                    </p>
                    <div class="mt-2 p-4 rounded-xl bg-white/40 border border-white/60 relative shadow-inner backdrop-blur-sm">
                        <div class="absolute -top-3 left-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-md">
                            Yalia Beauty Reply
                        </div>
                        <div class="flex items-start gap-3 mt-2">
                            <svg class="w-5 h-5 text-pink-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            <p class="text-sm/6 text-slate-800">
                                Thank you for your feedback, Michael! We deeply apologize for the temperature discomfort. We've noted this and will ensure the room is warmer and perfectly adjusted for your next visit. We hope to see you again soon!
                            </p>
                        </div>
                    </div>
                </article>
            @endforelse

        </div>
    </div>
</section>
