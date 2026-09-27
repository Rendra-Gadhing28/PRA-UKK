<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-3 h-8 bg-primary rounded-full inline-block"></span>
                    Manajemen Ulasan & Feedback Pelanggan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Pantau kepuasan pelanggan, ulasan treatment, rating beautician, dan balas feedback secara langsung.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.bookings.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-rose-200 bg-white hover:bg-rose-50 text-xs font-bold text-[#5C1439] shadow-sm transition-all">
                    <i class="fa-solid fa-calendar-check text-primary text-xs"></i>
                    <span>Daftar Reservasi</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ══ 1. EXECUTIVE STATS OVERVIEW ══ --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Stat 1: Rata-Rata Rating --}}
                <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 border border-amber-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Rating Rata-Rata</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <span class="text-2xl font-black text-gray-900">{{ number_format($avgRating, 1) }}</span>
                            <span class="text-xs font-semibold text-gray-500">/ 5.0</span>
                        </div>
                        <span class="text-xs text-amber-700 font-semibold block mt-0.5">Terapis: ⭐ {{ number_format($avgBeauticianRating, 1) }}</span>
                    </div>
                </div>

                {{-- Stat 2: Total Ulasan Masuk --}}
                <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#FFF0F2] text-primary border border-[#F4DDE1] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Total Ulasan</span>
                        <span class="text-2xl font-black text-gray-900 block mt-0.5">{{ number_format($totalReviews) }}</span>
                        <span class="text-xs text-emerald-600 font-semibold block mt-0.5">Semua Treatment</span>
                    </div>
                </div>

                {{-- Stat 3: Belum Dibalas Admin --}}
                <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-reply-all"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Perlu Dibalas</span>
                        <span class="text-2xl font-black text-rose-700 block mt-0.5">{{ number_format($unrepliedCount) }}</span>
                        <span class="text-xs text-gray-500 font-semibold block mt-0.5">Menunggu Tanggapan</span>
                    </div>
                </div>

                {{-- Stat 4: Top Rated Beautician --}}
                <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Top Beautician</span>
                        <span class="text-sm font-black text-gray-900 block mt-0.5 truncate">{{ $topBeautician?->name ?? 'Belum Ada' }}</span>
                        @if($topBeautician)
                            <span class="text-xs text-purple-700 font-bold block mt-0.5">
                                ⭐ {{ number_format((float)$topBeautician->reviews_avg_beautician_rating, 1) }} ({{ $topBeautician->reviews_count }} review)
                            </span>
                        @else
                            <span class="text-xs text-gray-400 block mt-0.5">0 Ulasan</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══ 2. FILTER & SEARCH SECTION ══ --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                {{-- Quick Tab Navigation --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide border-b border-rose-100">
                    @php
                        $tabs = [
                            ['key' => 'all',              'label' => 'Semua Ulasan',       'count' => $totalReviews,    'icon' => 'fa-list'],
                            ['key' => 'unreplied',        'label' => 'Belum Dibalas',      'count' => $unrepliedCount,  'icon' => 'fa-clock-rotate-left'],
                            ['key' => 'five_star',        'label' => 'Bintang 5 ⭐',       'count' => null,             'icon' => 'fa-star'],
                            ['key' => 'needs_attention',  'label' => 'Rating Rendah (≤3⭐)','count' => null,             'icon' => 'fa-triangle-exclamation'],
                            ['key' => 'with_photo',       'label' => 'Dengan Foto',        'count' => null,             'icon' => 'fa-camera'],
                            ['key' => 'unapproved',       'label' => 'Disembunyikan',      'count' => $unapprovedCount, 'icon' => 'fa-eye-slash'],
                        ];
                    @endphp

                    @foreach($tabs as $t)
                        <a href="{{ route('admin.reviews.index', array_merge(request()->query(), ['tab' => $t['key'], 'page' => 1])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                                  {{ $activeTab === $t['key'] ? 'bg-primary text-white shadow-sm' : 'bg-rose-50/60 text-[#5C1439] hover:bg-rose-100 hover:text-primary' }}">
                            <i class="fa-solid {{ $t['icon'] }} text-xs"></i>
                            <span>{{ $t['label'] }}</span>
                            @if(!is_null($t['count']) && $t['count'] > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-xs {{ $activeTab === $t['key'] ? 'bg-white/20 text-white' : 'bg-rose-200 text-rose-800' }}">
                                    {{ $t['count'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Search & Dropdowns Bar --}}
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end pt-1">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    {{-- Search Text --}}
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass text-primary text-xs"></i>
                            Cari Kata Kunci
                        </label>
                        <input type="text" name="search" value="{{ $currentSearch }}"
                               placeholder="Nama customer, treatment, komentar, kode booking..."
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-gray-800">
                    </div>

                    {{-- Filter Beautician --}}
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-nurse text-primary text-xs"></i>
                            Terapis / Beautician
                        </label>
                        <select name="beautician_id" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-gray-800">
                            <option value="">Semua Terapis</option>
                            @foreach($beauticiansList as $b)
                                <option value="{{ $b->id }}" {{ (string)$selectedBeautician === (string)$b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Option --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-down-short-wide text-primary text-xs"></i>
                            Urutan
                        </label>
                        <select name="sort" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-primary focus:ring-primary text-gray-800">
                            <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="rating_desc" {{ $currentSort === 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                            <option value="rating_asc" {{ $currentSort === 'rating_asc' ? 'selected' : '' }}>Rating Terendah</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-filter text-xs"></i>
                            <span>Terapkan</span>
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="py-2.5 px-3 rounded-xl bg-rose-100/70 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- ══ 3. REVIEWS FEED STREAM ══ --}}
            @if($reviews->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-rose-100 shadow-sm max-w-lg mx-auto space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-[#FFF0F2] text-primary flex items-center justify-center text-3xl mx-auto border border-[#F4DDE1]">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Belum Ada Ulasan Ditemukan</h3>
                    <p class="text-xs text-gray-500">Tidak ada ulasan yang sesuai dengan filter atau kata kunci pencarian saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($reviews as $r)
                        @php
                            $tags = is_string($r->beautician_tags) ? json_decode($r->beautician_tags, true) : $r->beautician_tags;
                        @endphp
                        <div class="bg-white rounded-3xl border border-rose-100 p-5 sm:p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4"
                             x-data="{ openReply: {{ empty($r->admin_reply) ? 'false' : 'false' }}, replyText: '{{ addslashes($r->admin_reply ?? '') }}', showPhotoModal: false }">

                            {{-- Card Header: User Avatar & Info + Rating Badges --}}
                            <div>
                                <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-rose-100">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-full overflow-hidden bg-rose-50 border border-rose-200 shrink-0">
                                            <img src="{{ $r->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($r->user?->name ?? 'User').'&background=f45472&color=fff' }}"
                                                 alt="{{ $r->user?->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <h4 class="font-bold text-sm text-gray-900 truncate">{{ $r->user?->name ?? 'Customer' }}</h4>
                                                @if($r->user?->membership_level)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200 shrink-0">
                                                        {{ $r->user->membership_level }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $r->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                                        </div>
                                    </div>

                                    {{-- Booking Code & Status Pill --}}
                                    <div class="text-right shrink-0">
                                        @if($r->booking)
                                            <a href="{{ route('admin.bookings.show', $r->booking) }}"
                                               class="font-mono text-xs font-bold text-primary bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md inline-block transition-colors"
                                               title="Buka Detail Reservasi">
                                                #{{ $r->booking->booking_code }}
                                            </a>
                                        @endif
                                        <div class="mt-1">
                                            @if($r->is_approved)
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    Publik
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50/80 text-[#5C1439] border border-rose-200">
                                                    Disembunyikan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Dual Rating Details --}}
                                <div class="grid grid-cols-2 gap-2 my-3 p-3 rounded-2xl bg-[#FFF8FA] border border-[#F4DDE1]">
                                    {{-- Treatment Rating --}}
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Rating Treatment</span>
                                        <div class="flex items-center gap-1 text-amber-400 text-xs mt-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $r->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                            <span class="font-bold text-xs text-gray-800 ml-1">{{ $r->rating }}.0</span>
                                        </div>
                                        <span class="text-xs text-primary font-bold truncate block mt-0.5">
                                            {{ $r->booking?->treatments->first()?->name ?? 'Layanan Salon' }}
                                        </span>
                                    </div>

                                    {{-- Beautician Rating --}}
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Rating Terapis</span>
                                        <div class="flex items-center gap-1 text-amber-400 text-xs mt-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= ($r->beautician_rating ?: $r->rating) ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                            <span class="font-bold text-xs text-gray-800 ml-1">{{ $r->beautician_rating ?: $r->rating }}.0</span>
                                        </div>
                                        <span class="text-xs text-purple-700 font-bold truncate block mt-0.5">
                                            {{ $r->beautician?->name ?? 'Terapis Salon' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Appreciation Tags --}}
                                @if(!empty($tags) && is_array($tags))
                                    <div class="flex flex-wrap gap-1 mb-2.5">
                                        @foreach($tags as $tg)
                                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-white border border-[#F4DDE1] text-[#5C1439]">
                                                ✨ {{ $tg }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Comment Text --}}
                                <div class="text-xs text-gray-800 leading-relaxed font-medium bg-white">
                                    @if($r->comment)
                                        <p class="italic">"{{ $r->comment }}"</p>
                                    @else
                                        <p class="text-gray-400 italic">(Pelanggan tidak meninggalkan ulasan teks)</p>
                                    @endif
                                </div>

                                {{-- Customer Photo Preview --}}
                                @if($r->photo)
                                    <div class="mt-3">
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Foto dari Customer:</span>
                                        <button type="button" @click="showPhotoModal = true" class="relative w-20 h-20 rounded-xl overflow-hidden border border-rose-200 group cursor-pointer">
                                            <img src="{{ \App\Support\ImageHelper::url($r->photo) }}" alt="Foto Review" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                            </div>
                                        </button>
                                    </div>

                                    {{-- Photo Zoom Modal --}}
                                    <div x-show="showPhotoModal" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
                                         @click.away="showPhotoModal = false">
                                        <div class="max-w-lg w-full bg-white rounded-3xl overflow-hidden p-2 relative shadow-2xl">
                                            <button type="button" @click="showPhotoModal = false" class="absolute top-4 right-4 bg-black/60 text-white rounded-full w-8 h-8 flex items-center justify-center z-10">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <img src="{{ \App\Support\ImageHelper::url($r->photo) }}" alt="Foto Review Full" class="w-full h-auto rounded-2xl max-h-[80vh] object-contain">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- ══ Admin Reply Section ══ --}}
                            <div class="pt-3 border-t border-rose-100 space-y-2">
                                @if($r->admin_reply)
                                    <div class="p-3 rounded-2xl bg-rose-50/70 border border-rose-200/80 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-primary flex items-center gap-1">
                                                <i class="fa-solid fa-reply text-xs"></i> Balasan Admin:
                                            </span>
                                            <button type="button" @click="openReply = !openReply" class="text-xs font-bold text-gray-500 hover:text-primary underline cursor-pointer">
                                                Edit Balasan
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-800 font-medium" x-show="!openReply">{{ $r->admin_reply }}</p>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between" x-show="!openReply">
                                        <span class="text-xs text-gray-400 font-medium italic">Belum ada balasan admin</span>
                                        <button type="button" @click="openReply = true"
                                                class="px-3 py-1 rounded-full bg-primary/10 hover:bg-primary text-primary hover:text-white text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-reply text-xs"></i>
                                            <span>Balas Ulasan</span>
                                        </button>
                                    </div>
                                @endif

                                {{-- Reply Form --}}
                                <div x-show="openReply" x-cloak class="space-y-2 pt-1">
                                    <form action="{{ route('admin.reviews.reply', $r) }}" method="POST">
                                        @csrf
                                        <textarea name="admin_reply" rows="2" required
                                                  class="w-full text-xs rounded-xl border-gray-200 focus:border-primary focus:ring-primary p-2.5 bg-white"
                                                  placeholder="Tulis balasan hangat dari admin...">{{ $r->admin_reply }}</textarea>
                                        <div class="flex items-center justify-end gap-2 mt-1.5">
                                            <button type="button" @click="openReply = false" class="px-3 py-1 rounded-full text-xs font-bold text-gray-500 hover:bg-gray-100">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-4 py-1.5 rounded-full bg-primary text-white text-xs font-bold hover:bg-primary-container transition-all shadow-xs cursor-pointer">
                                                Simpan Balasan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Footer Action Buttons: Toggle Approve & Delete --}}
                                <div class="flex items-center justify-between pt-2">
                                    <form action="{{ route('admin.reviews.toggle-approve', $r) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-bold {{ $r->is_approved ? 'text-amber-700 hover:text-amber-900' : 'text-emerald-700 hover:text-emerald-900' }} hover:underline cursor-pointer flex items-center gap-1">
                                            <i class="fa-solid {{ $r->is_approved ? 'fa-eye-slash' : 'fa-circle-check' }} text-xs"></i>
                                            <span>{{ $r->is_approved ? 'Sembunyikan dari Publik' : 'Setujui & Publikasikan' }}</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.reviews.destroy', $r) }}" method="POST"
                                          onsubmit="return confirm('Hapus ulasan ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1" title="Hapus Ulasan">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="pt-4 flex justify-center">
                    {{ $reviews->links() }}
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
