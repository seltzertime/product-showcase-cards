jQuery(document).ready(function($) {
    'use strict';

    /**
     * Category Tab Filtering
     */
    function filterItemsByCategory($button) {
        const $showcase = $button.closest('.psc-showcase');
        const category = $button.data('category');
        const scrollY = window.scrollY;

        // Update active tab
        $showcase.find('.psc-category-tab')
            .removeClass('psc-tab-active')
            .attr('aria-selected', 'false')
            .attr('tabindex', '-1');

        $button
            .addClass('psc-tab-active')
            .attr('aria-selected', 'true')
            .attr('tabindex', '0');

        // Filter items
        const $items = $showcase.find('.psc-item');

        $items.each(function() {
            const $item = $(this);
            const itemCategories = $item.data('categories');

            // Convert to string and split into array
            const categoriesArray = itemCategories ?
                String(itemCategories).split(',').map(cat => cat.trim()) :
                [];

            // Check if "All" category or if item has the selected category
            if (category.toLowerCase() === 'all') {
                // Show all items
                $item.show();
            } else if (categoriesArray.includes(category)) {
                // Show items that have this exact category
                $item.show();
            } else {
                // Hide items that don't have this category
                $item.hide();
            }
        });

        // Restore scroll position to prevent page jump
        window.scrollTo(0, scrollY);
    }

    // Handle clicks
    $('.psc-category-tab').on('click', function() {
        filterItemsByCategory($(this));
    });

    // Handle keyboard navigation
    $('.psc-category-tab').on('keydown', function(e) {
        const $tabs = $(this).closest('.psc-category-tabs').find('.psc-category-tab:visible');
        const currentIndex = $tabs.index(this);
        let $newTab = null;

        switch(e.key) {
            case 'ArrowRight':
                e.preventDefault();
                $newTab = $tabs.eq((currentIndex + 1) % $tabs.length);
                break;
            case 'ArrowLeft':
                e.preventDefault();
                $newTab = $tabs.eq((currentIndex - 1 + $tabs.length) % $tabs.length);
                break;
            case 'Home':
                e.preventDefault();
                $newTab = $tabs.first();
                break;
            case 'End':
                e.preventDefault();
                $newTab = $tabs.last();
                break;
        }

        if ($newTab && $newTab.length) {
            $newTab.focus().trigger('click');
        }
    });

    /**
     * Hide empty categories on page load
     */
    $('.psc-showcase').each(function() {
        const $showcase = $(this);
        const $tabs = $showcase.find('.psc-category-tab');
        const $items = $showcase.find('.psc-item');

        // Check each tab
        $tabs.each(function() {
            const $tab = $(this);
            const category = $tab.data('category');
            let hasItems = false;

            // Special handling for "All" - always show if it exists
            if (category.toLowerCase() === 'all') {
                return; // Continue to next tab
            }

            // Check if any items have this category
            $items.each(function() {
                const itemCategories = $(this).data('categories');
                const categoriesArray = itemCategories ?
                    String(itemCategories).split(',').map(cat => cat.trim()) :
                    [];

                if (categoriesArray.includes(category)) {
                    hasItems = true;
                    return false; // Break the loop
                }
            });

            // Hide tab if no items have this category
            if (!hasItems) {
                $tab.hide();
            }
        });

        // If first tab is hidden, activate the first visible tab
        const $firstTab = $tabs.first();
        if ($firstTab.is(':hidden')) {
            const $firstVisibleTab = $tabs.filter(':visible').first();
            if ($firstVisibleTab.length) {
                $firstVisibleTab.trigger('click');
            }
        }
    });
});
