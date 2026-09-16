import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import React from 'react';
import { createRoot } from 'react-dom/client';
import UserTreatmentFilter from './components/UserTreatmentFilter.jsx';

Alpine.plugin(focus);
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    // Mount User UserTreatmentFilter React component
    const userFilterContainer = document.getElementById('react-user-treatment-filter');
    if (userFilterContainer) {
        const categories = JSON.parse(userFilterContainer.getAttribute('data-categories') || '[]');
        const initialCategory = userFilterContainer.getAttribute('data-initial-category') || 'all';
        const initialSearch = userFilterContainer.getAttribute('data-initial-search') || '';
        const actionUrl = userFilterContainer.getAttribute('data-action-url') || '/treatments';

        const root = createRoot(userFilterContainer);
        root.render(
            React.createElement(
                React.StrictMode,
                null,
                React.createElement(UserTreatmentFilter, {
                    categories,
                    initialCategory,
                    initialSearch,
                    actionUrl,
                })
            )
        );
    }
});


