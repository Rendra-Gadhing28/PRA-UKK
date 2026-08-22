@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        {{-- Header --}}
        <div class="border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Skeleton</h1>
            <p class="mt-2 text-lg text-slate-600">Use to show a placeholder while content is loading.</p>
        </div>

        {{-- Base Demo Preview --}}
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-800">Preview</h2>
            <div class="p-8 rounded-xl border border-slate-200 bg-white shadow-sm flex items-center justify-center min-h-[160px]">
                <div class="flex items-center space-x-4">
                    <x-skeleton class="h-12 w-12 rounded-full" />
                    <div class="space-y-2">
                        <x-skeleton class="h-4 w-[250px]" />
                        <x-skeleton class="h-4 w-[200px]" />
                    </div>
                </div>
            </div>
        </section>

        {{-- Installation --}}
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-800">Installation</h2>
            <div x-data="{ tab: 'blade' }" class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                <div class="flex border-b border-slate-200 bg-slate-50/80 px-4 pt-3">
                    <button @click="tab = 'blade'" :class="tab === 'blade' ? 'border-primary text-primary font-medium border-b-2' : 'text-slate-600 hover:text-slate-900'" class="px-4 py-2 text-sm focus:outline-none transition-colors">
                        Blade Component
                    </button>
                    <button @click="tab = 'manual'" :class="tab === 'manual' ? 'border-primary text-primary font-medium border-b-2' : 'text-slate-600 hover:text-slate-900'" class="px-4 py-2 text-sm focus:outline-none transition-colors">
                        Manual CSS
                    </button>
                </div>
                <div class="p-6">
                    <div x-show="tab === 'blade'" class="space-y-3">
                        <p class="text-sm text-slate-600">Copy Blade components into <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-800 font-mono">resources/views/components/skeleton/</code></p>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;!-- Base Component Usage --&gt;
&lt;x-skeleton class="h-5 w-[120px] rounded-full" /&gt;</code></pre>
                    </div>
                    <div x-show="tab === 'manual'" x-cloak class="space-y-3">
                        <p class="text-sm text-slate-600">Add the CSS utilities to your <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-800 font-mono">resources/css/app.css</code> file:</p>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>.skeleton {
    @apply animate-pulse bg-slate-200/80 dark:bg-slate-700/80 rounded-md;
}</code></pre>
                    </div>
                </div>
            </div>
        </section>

        {{-- Usage --}}
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-800">Usage</h2>
            <div class="rounded-xl border border-slate-200 bg-slate-900 p-4 text-slate-100 font-mono text-sm overflow-x-auto shadow-sm">
                <code>&lt;x-skeleton class="h-[20px] w-[100px] rounded-full" /&gt;</code>
            </div>
        </section>

        {{-- Avatar Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">Avatar</h2>
                <p class="text-sm text-slate-500">Avatar circular skeleton loader with optional secondary text lines.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm space-y-6">
                <div class="flex items-center gap-6">
                    <x-skeleton.avatar size="sm" />
                    <x-skeleton.avatar size="md" />
                    <x-skeleton.avatar size="lg" />
                    <x-skeleton.avatar size="xl" />
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <x-skeleton.avatar withText="true" />
                </div>
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.avatar size="md" /&gt;
&lt;x-skeleton.avatar withText="true" /&gt;</code></pre>
        </section>

        {{-- Card Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">Card</h2>
                <p class="text-sm text-slate-500">Placeholder card skeleton layout for articles, products, or service items.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm max-w-sm">
                <x-skeleton.card />
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.card class="max-w-sm" /&gt;</code></pre>
        </section>

        {{-- Text Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">Text</h2>
                <p class="text-sm text-slate-500">Multiline text line loading skeleton with organic varied line widths.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm">
                <x-skeleton.text lines="4" />
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.text lines="4" /&gt;</code></pre>
        </section>

        {{-- Form Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">Form</h2>
                <p class="text-sm text-slate-500">Form field skeleton with input box placeholders and action buttons.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm">
                <x-skeleton.form fields="3" />
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.form fields="3" /&gt;</code></pre>
        </section>

        {{-- Table Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">Table</h2>
                <p class="text-sm text-slate-500">Data table rows loading placeholder with customized row & column counts.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm">
                <x-skeleton.table rows="4" cols="4" />
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.table rows="4" cols="4" /&gt;</code></pre>
        </section>

        {{-- RTL Variant --}}
        <section class="space-y-4">
            <div class="border-b border-slate-200 pb-2">
                <h2 class="text-xl font-semibold text-slate-800">RTL</h2>
                <p class="text-sm text-slate-500">Right-to-left layout skeleton support for multi-language applications.</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm">
                <x-skeleton.rtl direction="rtl" />
            </div>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg text-sm font-mono overflow-x-auto"><code>&lt;x-skeleton.rtl direction="rtl" /&gt;</code></pre>
        </section>

    </div>
</div>
@endsection
