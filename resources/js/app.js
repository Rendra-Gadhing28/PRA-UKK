import Alpine from 'alpinejs';
import React from 'react';
import { createRoot } from 'react-dom/client';
import UserTreatmentFilter from './components/UserTreatmentFilter.jsx';

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
