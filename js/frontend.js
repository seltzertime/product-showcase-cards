jQuery(document).ready(function($) {
    'use strict';

    /**
     * Build a mobile <select> dropdown that mirrors category tabs.
     * Hidden on desktop via CSS; revealed at narrow widths to replace
     * the horizontally-scrolling tab row with a single native picker.
     */
    function buildMobileSelect($showcase) {
        var $tabs = $showcase.find('.psc-category-tab');
        if (!$tabs.length) return;
        if ($showcase.find('.psc-mobile-select-wrapper').length) return;

        var $wrapper = $('<div class="psc-mobile-select-wrapper" aria-hidden="false"></div>');
        var $select = $('<select class="psc-mobile-select" aria-label="Filter by category"></select>');

        $tabs.each(function() {
            var $tab = $(this);
            var $opt = $('<option></option>')
                .val($tab.data('category'))
                .text($tab.text().trim());
            if ($tab.hasClass('psc-tab-active')) $opt.prop('selected', true);
            $select.append($opt);
        });

        $select.on('change', function() {
            var val = $(this).val();
            var $target = $showcase.find('.psc-category-tab').filter(function() {
                return $(this).data('category') === val;
            }).first();
            if ($target.length) $target.trigger('click');
        });

        $wrapper.append($select);
        $showcase.find('.psc-category-tabs').after($wrapper);
    }

    /**
     * Recalculate Boxes borders after filtering
     * Since :nth-child counts hidden elements, we need JS to fix borders
     * when category tabs show/hide items in Boxes style.
     * When all items are visible, clear inline styles so CSS :nth-child takes over.
     */
    function recalcBoxesBorders($showcase) {
        // Only applies to Boxes style
        if (!$showcase.hasClass('psc-style-boxes')) return;

        var $allItems = $showcase.find('.psc-item');
        var $visibleItems = $allItems.filter(':visible');
        var $hiddenItems = $allItems.filter(':hidden');

        // If all items are visible, clear inline border styles and let CSS handle it
        if ($hiddenItems.length === 0) {
            $allItems.css({
                'border-top': '',
                'border-left': '',
                'border-right': '',
                'border-bottom': '',
                'border': ''
            });
            return;
        }

        // Get current column count based on viewport.
        // Must match the CSS breakpoints in frontend.css:
        //   <=768px  -> 2 columns (was 1; we changed it for cleaner mobile layout)
        //   <=1024px -> 2 columns
        //   else     -> grid's data-items-per-row
        var cols = parseInt($showcase.attr('data-items-per-row')) || 4;
        var viewportWidth = $(window).width();
        if (viewportWidth <= 1024) {
            cols = 2;
        }

        // Get border style from CSS custom properties on the wrapper
        var wrapperStyle = $showcase[0].style;
        var borderColor = wrapperStyle.getPropertyValue('--psc-border-color').trim() || '#000';
        var borderWidth = wrapperStyle.getPropertyValue('--psc-border-width').trim() || '1px';
        var borderVal = borderWidth + ' solid ' + borderColor;

        // Apply borders to visible items based on their visual position
        $visibleItems.each(function(index) {
            var $item = $(this);

            // All items get right + bottom
            $item.css('border-right', borderVal);
            $item.css('border-bottom', borderVal);

            // First row gets top
            if (index < cols) {
                $item.css('border-top', borderVal);
            } else {
                $item.css('border-top', 'none');
            }

            // First column gets left
            if (index % cols === 0) {
                $item.css('border-left', borderVal);
            } else {
                $item.css('border-left', 'none');
            }
        });

        // Hidden items: remove all borders
        $hiddenItems.css({
            'border': 'none'
        });
    }

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

        // Recalculate Boxes borders after filtering
        recalcBoxesBorders($showcase);

        // Keep the mobile dropdown in sync with the active tab
        var $select = $showcase.find('.psc-mobile-select');
        if ($select.length && $select.val() !== category) {
            $select.val(category);
        }

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
     * Recalculate Boxes borders on window resize (column count may change)
     */
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            $('.psc-showcase.psc-style-boxes').each(function() {
                recalcBoxesBorders($(this));
            });
        }, 150);
    });

    /**
     * Hide empty categories on page load
     */
    $('.psc-showcase').each(function() {
        const $showcase = $(this);
        const $tabs = $showcase.find('.psc-category-tab');
        const $items = $showcase.find('.psc-item');

        // Build the mobile dropdown (hidden on desktop via CSS).
        buildMobileSelect($showcase);

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

        // If first tab is hidden, activate the first visible tab.
        // Otherwise, run the filter for the active tab on load — without this,
        // items render unfiltered in DOM order until a tab is clicked, which
        // means non-"All" first tabs would still show every item on page load.
        const $firstTab = $tabs.first();
        if ($firstTab.is(':hidden')) {
            const $firstVisibleTab = $tabs.filter(':visible').first();
            if ($firstVisibleTab.length) {
                $firstVisibleTab.trigger('click');
            }
        } else {
            const $activeTab = $tabs.filter('.psc-tab-active').first();
            if ($activeTab.length) {
                filterItemsByCategory($activeTab);
            }
        }
    });
});
