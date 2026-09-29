<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('logo/yalia-logos.svg') }}" type="image/svg+xml">
        <link rel="alternate icon" href="{{ asset('logo/yalia-logos-trnsprnt.png') }}" type="image/png">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <title>{{ config('app.name', 'Yalia Beauty') }} — Admin Executive</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Chart.js Global -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased bg-[#fdf5f6] text-gray-900">
        {{-- SPA Top Progress Loader --}}
        <div id="admin-spa-loader" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#b01f44] via-[#f45472] to-[#f4b942] z-[99999] transition-all duration-300 pointer-events-none opacity-0 -translate-y-full"></div>

        <div class="min-h-screen relative">
            
            {{-- Admin Dedicated Sidebar (Persistent) --}}
            @include('layouts.sidebar')

            {{-- Main Content Area with Desktop Sidebar Offset --}}
            <div id="admin-page-container" class="md:ml-64 flex flex-col min-h-screen">
                
                {{-- Header Slot (if provided) --}}
                <div id="admin-header-container">
                    @if (isset($header))
                        <header id="admin-header" class="bg-white/80 backdrop-blur-md border-b border-rose-100 px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </header>
                    @endif
                </div>

                {{-- Page Content --}}
                <main id="admin-main" class="flex-1">
                    {{ $slot }}
                </main>

                {{-- Toast Notifications --}}
                <x-toast />
            </div>

        </div>
        
        <!-- Script Admin Polling untuk Auto Reminder WhatsApp -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Jalankan setiap 60 detik (60000 ms)
                setInterval(function() {
                    fetch('/api/trigger-reminders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).catch(err => console.error('Reminder polling failed', err));
                }, 60000);
            });
        </script>

        <!-- Admin SPA Navigation Engine (Prevents Sidebar Re-render) -->
        <script>
            (function() {
                const loader = document.getElementById('admin-spa-loader');
                let isNavigating = false;

                function showLoader() {
                    if (!loader) return;
                    loader.style.width = '30%';
                    loader.style.opacity = '1';
                    loader.style.transform = 'translateY(0)';
                    setTimeout(() => {
                        if (isNavigating) loader.style.width = '75%';
                    }, 150);
                }

                function hideLoader() {
                    if (!loader) return;
                    loader.style.width = '100%';
                    setTimeout(() => {
                        loader.style.opacity = '0';
                        loader.style.transform = 'translateY(-100%)';
                        setTimeout(() => {
                            loader.style.width = '0%';
                        }, 300);
                    }, 150);
                }

                function updateSidebarActiveState(targetPath) {
                    const navLinks = document.querySelectorAll('aside nav a[href]');
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (!href || href === '#' || (href.includes('/dashboard') && !href.includes('/admin'))) return;

                        let linkPath;
                        try {
                            linkPath = new URL(href, window.location.origin).pathname;
                        } catch (e) {
                            return;
                        }

                        let isActive = false;
                        if (linkPath === '/admin' || linkPath === '/admin/dashboard') {
                            isActive = (targetPath === '/admin' || targetPath === '/admin/dashboard');
                        } else if (linkPath.startsWith('/admin/')) {
                            const segment = linkPath.split('/')[2];
                            isActive = targetPath.startsWith('/admin/' + segment);
                        }

                        const dot = link.querySelector('.bg-amber-300');
                        if (isActive) {
                            link.style.background = 'linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%)';
                            link.style.color = '#ffffff';
                            link.classList.remove('admin-nav-idle');
                            link.classList.add('admin-nav-active');
                            if (!dot) {
                                const span = document.createElement('span');
                                span.className = 'w-1.5 h-1.5 rounded-full bg-amber-300';
                                link.appendChild(span);
                            }
                        } else {
                            link.style.background = '';
                            link.style.color = '';
                            link.classList.remove('admin-nav-active');
                            link.classList.add('admin-nav-idle');
                            if (dot) {
                                dot.remove();
                            }
                        }
                    });
                }

                async function navigateTo(url, push = true) {
                    if (isNavigating) return;
                    isNavigating = true;
                    showLoader();

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            window.location.href = url;
                            return;
                        }

                        const htmlText = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');

                        // Update Document Title
                        if (doc.title) {
                            document.title = doc.title;
                        }

                        // Update Header Container
                        const newHeaderContainer = doc.getElementById('admin-header-container');
                        const currentHeaderContainer = document.getElementById('admin-header-container');
                        if (newHeaderContainer && currentHeaderContainer) {
                            currentHeaderContainer.innerHTML = newHeaderContainer.innerHTML;
                        }

                        // Update Main Content
                        const newMain = doc.getElementById('admin-main');
                        const currentMain = document.getElementById('admin-main');
                        if (newMain && currentMain) {
                            currentMain.innerHTML = newMain.innerHTML;

                            // Execute inline & external scripts inside newMain sequentially
                            const scripts = Array.from(currentMain.querySelectorAll('script'));
                            for (const oldScript of scripts) {
                                const newScript = document.createElement('script');
                                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                if (oldScript.src) {
                                    await new Promise((resolve) => {
                                        newScript.onload = resolve;
                                        newScript.onerror = resolve;
                                        oldScript.parentNode.replaceChild(newScript, oldScript);
                                    });
                                } else {
                                    newScript.textContent = oldScript.textContent;
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                }
                            }

                            // Re-init Alpine.js on the dynamic tree
                            if (window.Alpine) {
                                window.Alpine.initTree(document.getElementById('admin-page-container'));
                            }

                            // Dispatch event indicating new page content is loaded
                            document.dispatchEvent(new CustomEvent('admin:page-loaded', { detail: { url } }));
                        } else {
                            window.location.href = url;
                            return;
                        }

                        if (push) {
                            history.pushState({ url }, '', url);
                        }

                        const targetPath = new URL(url, window.location.origin).pathname;
                        updateSidebarActiveState(targetPath);
                        window.scrollTo({ top: 0, behavior: 'smooth' });

                    } catch (err) {
                        console.error('SPA Navigation error:', err);
                        window.location.href = url;
                    } finally {
                        isNavigating = false;
                        hideLoader();
                    }
                }

                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (!link) return;

                    const href = link.getAttribute('href');
                    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                    // Skip downloads, target="_blank", or non-admin links
                    if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
                    if (link.getAttribute('data-spa-ignore') !== null) return;

                    let urlObj;
                    try {
                        urlObj = new URL(href, window.location.origin);
                    } catch (err) {
                        return;
                    }

                    // Only intercept internal /admin routes, excluding export downloads
                    if (urlObj.origin === window.location.origin && urlObj.pathname.startsWith('/admin')) {
                        if (urlObj.pathname.includes('/export/')) return; // Allow normal file download
                        e.preventDefault();
                        navigateTo(urlObj.href, true);
                    }
                });

                window.addEventListener('popstate', function(e) {
                    if (window.location.pathname.startsWith('/admin')) {
                        navigateTo(window.location.href, false);
                    }
                });
            })();
        </script>
    </body>
</html>
