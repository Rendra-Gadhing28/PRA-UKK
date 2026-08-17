import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import * as SolidIcons from '@fortawesome/free-solid-svg-icons';

/**
 * React Component for dynamically rendering FontAwesome icons.
 * Handles strings like 'fas fa-face-smile', 'fa-eye', 'fa-sparkles', or icon names.
 */
export default function FaIcon({ icon, className = '', ...props }) {
    if (!icon) return null;

    // If icon is an emoji, render directly as text
    if (/[\u{1F300}-\u{1F9FF}]/u.test(icon)) {
        return <span className={className} {...props}>{icon}</span>;
    }

    // Standardize icon string e.g. "fas fa-face-smile" -> "face-smile" or "faFaceSmile"
    let cleanIconName = String(icon).trim();
    
    // Remove prefixes like "fas ", "far ", "fab ", "fa-"
    cleanIconName = cleanIconName.replace(/^fa[srb]?\s+/, '').replace(/^fa-/, '');

    // Convert camelCase or kebab-case to FontAwesome icon object name "faFaceSmile"
    const camelCaseName = 'fa' + cleanIconName
        .split('-')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');

    // Check if matching solid icon exists in @fortawesome/free-solid-svg-icons
    const foundIcon = SolidIcons[camelCaseName];

    if (foundIcon) {
        return <FontAwesomeIcon icon={foundIcon} className={className} {...props} />;
    }

    // Fallback: Render standard i element with CSS class
    const fullClass = icon.startsWith('fa') ? icon : `fas fa-${cleanIconName}`;
    return <i className={`${fullClass} ${className}`} {...props}></i>;
}
