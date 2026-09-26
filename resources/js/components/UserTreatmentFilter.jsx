import React, { useState, useRef, useEffect } from 'react';
import FaIcon from './FaIcon.jsx';

export default function UserTreatmentFilter({
    categories = [],
    initialCategory = 'all',
    initialSearch = '',
    actionUrl = '/treatments',
    userFavoritesCount = 0
}) {
    const [category, setCategory] = useState(initialCategory);
    const [search, setSearch] = useState(initialSearch);
    const [isOpen, setIsOpen] = useState(false);
    const [isRefreshing, setIsRefreshing] = useState(false);
    const dropdownRef = useRef(null);

    // Close dropdown on click outside
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setIsOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleCategorySelect = (newCat) => {
        setCategory(newCat);
        setIsOpen(false);
        const url = new URL(window.location.origin + actionUrl);
        if (newCat && newCat !== 'all') {
            url.searchParams.set('category', newCat);
        }
        if (search.trim()) {
            url.searchParams.set('search', search.trim());
        }
        window.location.href = url.toString();
    };

    const handleReset = (e) => {
        e.preventDefault();
        setCategory('all');
        setSearch('');
        window.location.href = actionUrl;
    };

    const handleRefresh = (e) => {
        e.preventDefault();
        setIsRefreshing(true);
        window.location.reload();
    };

    const selectedCategoryObj = categories.find(c => c.slug === category);
    const isFiltered = category !== 'all' || search.trim() !== '';

    return (
        <div className="w-full max-w-4xl mx-auto mt-6 space-y-4">
            <form method="GET" action={actionUrl} className="space-y-4">
                
                {/* Hidden Input for Form Submit */}
                <input type="hidden" name="category" value={category} />

                {/* Main Filter Bar */}
                <div className="bg-white/95 backdrop-blur-md rounded-2xl p-3.5 sm:p-4 shadow-sm border border-[#F4DDE1] flex flex-col sm:flex-row gap-2.5 items-center">
                    
                    {/* Search Field */}
                    <div className="relative flex-1 w-full">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary">
                            <FaIcon icon="fa-magnifying-glass" className="w-3.5 h-3.5" />
                        </div>
                        <input
                            type="text"
                            name="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari perawatan (contoh: Facial, Hair Spa)..."
                            className="w-full pl-10 pr-8 py-2.5 text-xs sm:text-sm rounded-xl border border-rose-200 bg-[#fff8f9] focus:border-primary focus:ring-primary text-gray-800 transition-all"
                        />
                        {search && (
                            <button
                                type="button"
                                onClick={() => setSearch('')}
                                className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-rose-600 cursor-pointer"
                                title="Hapus teks"
                            >
                                <FaIcon icon="fa-xmark" className="w-3.5 h-3.5" />
                            </button>
                        )}
                    </div>

                    {/* CUSTOM REACT CATEGORY DROPDOWN */}
                    <div className="relative w-full sm:w-56 shrink-0" ref={dropdownRef}>
                        <button
                            type="button"
                            onClick={() => setIsOpen(!isOpen)}
                            className="w-full flex items-center justify-between px-3.5 py-2.5 text-xs sm:text-sm font-medium rounded-xl border border-rose-200 bg-[#fff8f9] hover:border-primary focus:border-primary text-gray-800 transition-all text-left shadow-xs cursor-pointer"
                        >
                            <div className="flex items-center gap-2 truncate">
                                <span className="w-5 h-5 rounded-md bg-[#FFF0F2] text-primary flex items-center justify-center shrink-0">
                                    <FaIcon
                                        icon={category === 'all' ? 'fa-layer-group' : (category === 'favorites' ? 'fa-heart' : (selectedCategoryObj?.icon || 'fa-tag'))}
                                        className="w-3 h-3"
                                    />
                                </span>
                                <span className="font-semibold text-gray-900 truncate text-xs sm:text-sm">
                                    {category === 'all' ? 'Semua Layanan' : (category === 'favorites' ? 'Favorit Saya' : selectedCategoryObj?.name)}
                                </span>
                            </div>

                            <FaIcon
                                icon="fa-chevron-down"
                                className={`w-3 h-3 text-gray-400 transition-transform duration-200 shrink-0 ${isOpen ? 'rotate-180 text-primary' : ''}`}
                            />
                        </button>

                        {/* Dropdown Menu Popup */}
                        {isOpen && (
                            <div className="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#F4DDE1] py-2 z-50 animate-fadeIn">
                                <div className="px-3.5 pb-1.5 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Kategori Layanan
                                </div>

                                <div className="max-h-56 overflow-y-auto py-1">
                                    <button
                                        type="button"
                                        onClick={() => handleCategorySelect('all')}
                                        className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors cursor-pointer ${
                                            category === 'all'
                                                ? 'bg-[#FFF0F2] font-bold text-primary'
                                                : 'text-gray-700 hover:bg-rose-50/50 hover:text-primary'
                                        }`}
                                    >
                                        <div className="flex items-center gap-2">
                                            <span className="w-5 h-5 rounded-md bg-[#FFF0F2] text-primary flex items-center justify-center">
                                                <FaIcon icon="fa-layer-group" className="w-3 h-3" />
                                            </span>
                                            <span>Semua Layanan</span>
                                        </div>
                                        {category === 'all' && (
                                            <FaIcon icon="fa-check" className="w-3 h-3 text-primary" />
                                        )}
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => handleCategorySelect('favorites')}
                                        className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors cursor-pointer ${
                                            category === 'favorites'
                                                ? 'bg-rose-50 font-bold text-rose-600'
                                                : 'text-gray-700 hover:bg-rose-50/50 hover:text-rose-600'
                                        }`}
                                    >
                                        <div className="flex items-center gap-2">
                                            <span className="w-5 h-5 rounded-md bg-rose-100 text-rose-600 flex items-center justify-center">
                                                <FaIcon icon="fa-heart" className="w-3 h-3" />
                                            </span>
                                            <span>Favorit Saya ({userFavoritesCount})</span>
                                        </div>
                                        {category === 'favorites' && (
                                            <FaIcon icon="fa-check" className="w-3 h-3 text-rose-600" />
                                        )}
                                    </button>

                                    {categories.map((cat) => {
                                        const isSelected = category === cat.slug;
                                        return (
                                            <button
                                                key={cat.id || cat.slug}
                                                type="button"
                                                onClick={() => handleCategorySelect(cat.slug)}
                                                className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors cursor-pointer ${
                                                    isSelected
                                                        ? 'bg-[#FFF0F2] font-bold text-primary'
                                                        : 'text-gray-700 hover:bg-rose-50/50 hover:text-primary'
                                                }`}
                                            >
                                                <div className="flex items-center gap-2">
                                                    <span className="w-5 h-5 rounded-md bg-rose-50 text-primary flex items-center justify-center">
                                                        <FaIcon icon={cat.icon || 'fa-tag'} className="w-3 h-3" />
                                                    </span>
                                                    <span>{cat.name}</span>
                                                </div>
                                                {isSelected && (
                                                    <FaIcon icon="fa-check" className="w-3 h-3 text-primary" />
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Action Buttons: Cari + Refresh */}
                    <div className="flex items-center gap-2 w-full sm:w-auto shrink-0">
                        {/* Submit Button */}
                        <button
                            type="submit"
                            className="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary via-[#C82D53] to-secondary text-white font-bold text-xs sm:text-sm shadow-sm hover:shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <FaIcon icon="fa-magnifying-glass" className="w-3.5 h-3.5" />
                            <span>Cari</span>
                        </button>

                        {/* Reset Button (If Filtered) */}
                        {isFiltered && (
                            <button
                                type="button"
                                onClick={handleReset}
                                className="px-3.5 py-2.5 rounded-xl bg-rose-50 text-rose-600 font-semibold text-xs hover:bg-rose-100 transition-all flex items-center justify-center gap-1 shrink-0 border border-rose-200 cursor-pointer"
                                title="Reset Filter"
                            >
                                <FaIcon icon="fa-rotate-left" className="w-3.5 h-3.5" />
                                <span className="hidden sm:inline">Reset</span>
                            </button>
                        )}

                        {/* Dedicated Refresh Button in Search Bar */}
                        <button
                            type="button"
                            onClick={handleRefresh}
                            className="p-2.5 rounded-xl border border-rose-200 bg-[#fff8f9] text-primary hover:bg-[#FFF0F2] hover:border-primary transition-all flex items-center justify-center shrink-0 shadow-xs active:scale-95 cursor-pointer"
                            title="Segarkan / Refresh Katalog Treatment"
                        >
                            <FaIcon icon="fa-arrows-rotate" className={`w-3.5 h-3.5 ${isRefreshing ? 'animate-spin' : ''}`} />
                        </button>
                    </div>

                </div>

                {/* Custom Category Quick Filter Bar + Right-Aligned Refresh Button */}
                <div className="flex items-center justify-between gap-2 flex-wrap pt-1">
                    <div className="flex items-center gap-1.5 flex-wrap">
                        <span className="text-xs font-semibold text-[#9b6374] mr-1">Kategori Layanan:</span>
                        
                        <button
                            type="button"
                            onClick={() => handleCategorySelect('all')}
                            className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 ${
                                category === 'all'
                                    ? 'bg-primary text-white shadow-sm'
                                    : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-primary border border-[#F4DDE1]'
                            }`}
                        >
                            <FaIcon icon="fa-layer-group" className="w-3 h-3" />
                            <span>Semua</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => handleCategorySelect('favorites')}
                            className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 ${
                                category === 'favorites'
                                    ? 'bg-rose-600 text-white shadow-sm'
                                    : 'bg-[#FFF0F2] text-rose-700 hover:bg-rose-100 border border-[#F4DDE1]'
                            }`}
                        >
                            <FaIcon icon="fa-heart" className="w-3 h-3" />
                            <span>Favorit Saya ({userFavoritesCount})</span>
                        </button>

                        {categories.map((cat) => {
                            const isSelected = category === cat.slug;
                            return (
                                <button
                                    key={cat.id || cat.slug}
                                    type="button"
                                    onClick={() => handleCategorySelect(cat.slug)}
                                    className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 ${
                                        isSelected
                                            ? 'bg-primary text-white shadow-sm'
                                            : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-primary border border-[#F4DDE1]'
                                    }`}
                                >
                                    <FaIcon icon={cat.icon || 'fa-tag'} className="w-3 h-3" />
                                    <span>{cat.name}</span>
                                </button>
                            );
                        })}
                    </div>

                    {/* Right-aligned Refresh Pill Button */}
                    <button
                        type="button"
                        onClick={handleRefresh}
                        className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white text-[#5C1439] border border-[#F4DDE1] hover:border-primary hover:text-primary hover:bg-[#FFF0F2] transition-all shadow-xs cursor-pointer active:scale-95 shrink-0 ml-auto"
                        title="Segarkan / Reload Katalog"
                    >
                        <FaIcon icon="fa-arrows-rotate" className={`w-3 h-3 ${isRefreshing ? 'animate-spin' : ''}`} />
                        <span>Refresh</span>
                    </button>
                </div>

            </form>
        </div>
    );
}
