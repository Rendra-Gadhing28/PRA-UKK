import React, { useState, useRef, useEffect } from 'react';
import FaIcon from './FaIcon.jsx';

export default function TreatmentFilter({
    categories = [],
    initialCategory = 'all',
    initialSearch = '',
    actionUrl = '/admin/treatments'
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

    const selectedCategoryObj = categories.find(c => String(c.id) === String(category));
    const isFiltered = category !== 'all' || search.trim() !== '';

    return (
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 transition-all hover:shadow-md">
            <form method="GET" action={actionUrl} className="space-y-5">
                
                {/* Hidden Inputs for Form Submit */}
                <input type="hidden" name="category_id" value={category} />

                {/* Filter Header */}
                <div className="flex items-center justify-between border-b border-rose-50 pb-3">
                    <div className="flex items-center gap-2 text-rose-600 font-bold text-xs uppercase tracking-wider">
                        <FaIcon icon="fa-sliders" className="w-4 h-4" />
                        <span>Filter & Pencarian Treatment</span>
                    </div>

                    {isFiltered && (
                        <div className="flex items-center gap-2">
                            <span className="text-[11px] font-medium bg-rose-50 text-rose-600 px-2.5 py-0.5 rounded-full border border-rose-200 flex items-center gap-1">
                                <FaIcon icon="fa-circle-check" className="w-3 h-3" />
                                Filter Aktif
                            </span>
                            <button
                                type="button"
                                onClick={handleReset}
                                className="text-xs font-semibold text-gray-500 hover:text-rose-600 underline transition-colors"
                            >
                                Reset Filter
                            </button>
                        </div>
                    )}
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                    
                    {/* CUSTOM REACT CATEGORY SELECTOR (No native <select>) */}
                    <div className="sm:col-span-2 relative" ref={dropdownRef}>
                        <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <FaIcon icon="fa-layer-group" className="text-rose-500 text-xs" />
                            Kategori Treatment
                        </label>

                        {/* Custom Dropdown Button */}
                        <button
                            type="button"
                            onClick={() => setIsOpen(!isOpen)}
                            className="w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-medium rounded-xl border border-gray-200 bg-white hover:border-[#f45472] focus:border-[#f45472] focus:ring-2 focus:ring-rose-100 text-gray-800 transition-all text-left shadow-sm"
                        >
                            <div className="flex items-center gap-2 truncate">
                                <span className="w-6 h-6 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                                    <FaIcon
                                        icon={category === 'all' ? 'fa-spa' : (selectedCategoryObj?.icon || 'fa-tag')}
                                        className="w-3.5 h-3.5"
                                    />
                                </span>
                                <span className="font-semibold text-gray-900 truncate">
                                    {category === 'all' ? 'Semua Kategori' : selectedCategoryObj?.name}
                                </span>
                            </div>

                            <FaIcon
                                icon="fa-chevron-down"
                                className={`w-3 h-3 text-gray-400 transition-transform duration-200 shrink-0 ${isOpen ? 'rotate-180 text-rose-500' : ''}`}
                            />
                        </button>

                        {/* Custom Dropdown Menu Popup */}
                        {isOpen && (
                            <div className="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-rose-100 py-2 z-50 animate-fadeIn">
                                <div className="px-2 pb-1.5 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3">
                                    Pilih Kategori
                                </div>

                                <div className="max-h-56 overflow-y-auto py-1">
                                    {/* Option All */}
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setCategory('all');
                                            setIsOpen(false);
                                        }}
                                        className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors ${
                                            category === 'all'
                                                ? 'bg-rose-50 font-bold text-rose-600'
                                                : 'text-gray-700 hover:bg-rose-50/50 hover:text-rose-600'
                                        }`}
                                    >
                                        <div className="flex items-center gap-2.5">
                                            <span className="w-6 h-6 rounded-lg bg-rose-100/50 text-rose-500 flex items-center justify-center">
                                                <FaIcon icon="fa-spa" className="w-3 h-3" />
                                            </span>
                                            <span>Semua Kategori</span>
                                        </div>
                                        {category === 'all' && (
                                            <FaIcon icon="fa-check" className="w-3 h-3 text-rose-600" />
                                        )}
                                    </button>

                                    {/* Option List */}
                                    {categories.map((cat) => {
                                        const isSelected = String(category) === String(cat.id);
                                        return (
                                            <button
                                                key={cat.id}
                                                type="button"
                                                onClick={() => {
                                                    setCategory(String(cat.id));
                                                    setIsOpen(false);
                                                }}
                                                className={`w-full text-left px-3.5 py-2 text-xs flex items-center justify-between transition-colors ${
                                                    isSelected
                                                        ? 'bg-rose-50 font-bold text-rose-600'
                                                        : 'text-gray-700 hover:bg-rose-50/50 hover:text-rose-600'
                                                }`}
                                            >
                                                <div className="flex items-center gap-2.5">
                                                    <span className="w-6 h-6 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                                                        <FaIcon icon={cat.icon || 'fa-tag'} className="w-3 h-3" />
                                                    </span>
                                                    <span>{cat.name}</span>
                                                </div>
                                                {isSelected && (
                                                    <FaIcon icon="fa-check" className="w-3 h-3 text-rose-600" />
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Search Field */}
                    <div className="sm:col-span-3">
                        <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <FaIcon icon="fa-magnifying-glass" className="text-rose-500 text-xs" />
                            Cari Treatment
                        </label>

                        <div className="relative">
                            <input
                                type="text"
                                name="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari nama atau deskripsi treatment..."
                                className="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 shadow-sm"
                            />

                            <div className="absolute left-3 top-1/2 -translate-y-1/2 text-rose-400 pointer-events-none flex items-center">
                                <FaIcon icon="fa-magnifying-glass" className="w-3.5 h-3.5" />
                            </div>

                            {search && (
                                <button
                                    type="button"
                                    onClick={() => setSearch('')}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-600 transition-colors"
                                >
                                    <FaIcon icon="fa-xmark" className="w-3.5 h-3.5" />
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Buttons: Filter & Reset */}
                    <div className="flex items-center gap-2">
                        <button
                            type="submit"
                            className="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm flex items-center justify-center gap-1.5"
                        >
                            <FaIcon icon="fa-filter" className="w-3.5 h-3.5" />
                            <span>Terapkan</span>
                        </button>

                        <button
                            type="button"
                            onClick={handleReset}
                            className="py-2.5 px-3 rounded-xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center gap-1"
                            title="Reset Filter"
                        >
                            <FaIcon icon="fa-rotate-left" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                </div>

                {/* Custom Category Quick Filter Pills Bar */}
                <div className="pt-2 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    <span className="text-[11px] font-bold text-gray-400 shrink-0 uppercase tracking-wider">Kategori:</span>
                    
                    <button
                        type="button"
                        onClick={() => setCategory('all')}
                        className={`px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 border ${
                            category === 'all'
                                ? 'bg-rose-500 text-white border-rose-500 shadow-sm'
                                : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200'
                        }`}
                    >
                        <FaIcon icon="fa-spa" className="w-3 h-3" />
                        <span>Semua</span>
                    </button>

                    {categories.map((cat) => {
                        const isSelected = String(category) === String(cat.id);
                        return (
                            <button
                                key={cat.id}
                                type="button"
                                onClick={() => setCategory(String(cat.id))}
                                className={`px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 border ${
                                    isSelected
                                        ? 'bg-rose-500 text-white border-rose-500 shadow-sm'
                                        : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200'
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
