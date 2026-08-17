import React, { useState, useRef, useEffect } from 'react';
import FaIcon from './FaIcon.jsx';

export default function UserTreatmentFilter({
    categories = [],
    initialCategory = 'all',
    initialSearch = '',
    actionUrl = '/treatments'
}) {
    const [category, setCategory] = useState(initialCategory);
    const [search, setSearch] = useState(initialSearch);
    const [isOpen, setIsOpen] = useState(false);
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

    const handleReset = (e) => {
        e.preventDefault();
        setCategory('all');
        setSearch('');
        window.location.href = actionUrl;
    };

    const selectedCategoryObj = categories.find(c => c.slug === category);
    const isFiltered = category !== 'all' || search.trim() !== '';

    return (
        <div className="w-full max-w-3xl mx-auto mt-6">
            <form method="GET" action={actionUrl} className="space-y-4">
                
                {/* Hidden Input for Form Submit */}
                <input type="hidden" name="category" value={category} />

                {/* Main Filter Bar */}
                <div className="bg-white/95 backdrop-blur-md rounded-2xl p-4 sm:p-5 shadow-lg border border-pink-100 flex flex-col sm:flex-row gap-3 items-center">
                    
                    {/* Search Field */}
                    <div className="relative flex-1 w-full">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#f45472]">
                            <FaIcon icon="fa-magnifying-glass" className="w-4 h-4" />
                        </div>
                        <input
                            type="text"
                            name="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari perawatan (contoh: Facial, Hair Spa)..."
                            className="w-full pl-10 pr-8 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#fff8f9] focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 transition-all"
                        />
                        {search && (
                            <button
                                type="button"
                                onClick={() => setSearch('')}
                                className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-rose-600"
                            >
                                <FaIcon icon="fa-xmark" className="w-3.5 h-3.5" />
                            </button>
                        )}
                    </div>

                    {/* CUSTOM REACT CATEGORY DROPDOWN (No native <select>) */}
                    <div className="relative w-full sm:w-60 shrink-0" ref={dropdownRef}>
                        <button
                            type="button"
                            onClick={() => setIsOpen(!isOpen)}
                            className="w-full flex items-center justify-between px-3.5 py-2.5 text-sm font-medium rounded-xl border border-rose-200 bg-[#fff8f9] hover:border-[#f45472] focus:border-[#f45472] text-gray-800 transition-all text-left shadow-sm"
                        >
                            <div className="flex items-center gap-2.5 truncate">
                                <span className="w-6 h-6 rounded-lg bg-rose-100 text-[#f45472] flex items-center justify-center shrink-0">
                                    <FaIcon
                                        icon={category === 'all' ? 'fa-layer-group' : (selectedCategoryObj?.icon || 'fa-tag')}
                                        className="w-3.5 h-3.5"
                                    />
                                </span>
                                <span className="font-semibold text-gray-900 truncate">
                                    {category === 'all' ? 'Semua Layanan' : selectedCategoryObj?.name}
                                </span>
                            </div>

                            <FaIcon
                                icon="fa-chevron-down"
                                className={`w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0 ${isOpen ? 'rotate-180 text-[#f45472]' : ''}`}
                            />
                        </button>

                        {/* Dropdown Menu Popup */}
                        {isOpen && (
                            <div className="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-rose-100 py-2 z-50 animate-fadeIn">
                                <div className="px-3.5 pb-1.5 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Kategori Layanan
                                </div>

                                <div className="max-h-56 overflow-y-auto py-1">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setCategory('all');
                                            setIsOpen(false);
                                        }}
                                        className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors ${
                                            category === 'all'
                                                ? 'bg-rose-50 font-bold text-[#f45472]'
                                                : 'text-gray-700 hover:bg-rose-50/50 hover:text-[#f45472]'
                                        }`}
                                    >
                                        <div className="flex items-center gap-2.5">
                                            <span className="w-6 h-6 rounded-lg bg-rose-100 text-[#f45472] flex items-center justify-center">
                                                <FaIcon icon="fa-spa" className="w-3 h-3" />
                                            </span>
                                            <span>Semua Layanan</span>
                                        </div>
                                        {category === 'all' && (
                                            <FaIcon icon="fa-check" className="w-3 h-3 text-[#f45472]" />
                                        )}
                                    </button>

                                    {categories.map((cat) => {
                                        const isSelected = category === cat.slug;
                                        return (
                                            <button
                                                key={cat.id || cat.slug}
                                                type="button"
                                                onClick={() => {
                                                    setCategory(cat.slug);
                                                    setIsOpen(false);
                                                }}
                                                className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors ${
                                                    isSelected
                                                        ? 'bg-rose-50 font-bold text-[#f45472]'
                                                        : 'text-gray-700 hover:bg-rose-50/50 hover:text-[#f45472]'
                                                }`}
                                            >
                                                <div className="flex items-center gap-2.5">
                                                    <span className="w-6 h-6 rounded-lg bg-rose-50 text-[#f45472] flex items-center justify-center">
                                                        <FaIcon icon={cat.icon || 'fa-tag'} className="w-3 h-3" />
                                                    </span>
                                                    <span>{cat.name}</span>
                                                </div>
                                                {isSelected && (
                                                    <FaIcon icon="fa-check" className="w-3 h-3 text-[#f45472]" />
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        className="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#f45472] to-[#d94060] text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 shrink-0"
                    >
                        <FaIcon icon="fa-magnifying-glass" className="w-3.5 h-3.5" />
                        <span>Cari</span>
                    </button>

                    {/* Reset Button */}
                    {isFiltered && (
                        <button
                            type="button"
                            onClick={handleReset}
                            className="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-rose-50 text-rose-600 font-semibold text-xs hover:bg-rose-100 transition-all flex items-center justify-center gap-1.5 shrink-0 border border-rose-200"
                        >
                            <FaIcon icon="fa-rotate-left" className="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>
                    )}
                </div>

                {/* Custom Category Quick Filter Bar */}
                <div className="flex items-center justify-center gap-2 flex-wrap pt-1">
                    <span className="text-xs font-semibold text-[#9b6374]">Kategori Layanan:</span>
                    
                    <button
                        type="button"
                        onClick={() => setCategory('all')}
                        className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 ${
                            category === 'all'
                                ? 'bg-[#f45472] text-white shadow-sm scale-105'
                                : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-[#f45472] border border-rose-100'
                        }`}
                    >
                        <FaIcon icon="fa-spa" className="w-3 h-3" />
                        <span>Semua</span>
                    </button>

                    {categories.map((cat) => {
                        const isSelected = category === cat.slug;
                        return (
                            <button
                                key={cat.id || cat.slug}
                                type="button"
                                onClick={() => setCategory(cat.slug)}
                                className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 ${
                                    isSelected
                                        ? 'bg-[#f45472] text-white shadow-sm scale-105'
                                        : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-[#f45472] border border-rose-100'
                                }`}
                            >
                                <FaIcon icon={cat.icon || 'fa-tag'} className="w-3 h-3" />
                                <span>{cat.name}</span>
                            </button>
                        );
                    })}
                </div>

            </form>
        </div>
    );
}
