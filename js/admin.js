jQuery(document).ready(function($) {
    'use strict';

    // Initialize color pickers
    $('.psc-color-picker').wpColorPicker();

    // Item counter for unique indexes
    let itemCounter = $('#psc-items-container .psc-item-row').length;

    /**
     * Add Item
     */
    $('#psc-add-item').on('click', function(e) {
        e.preventDefault();

        // Get template
        let template = $('#psc-item-template').html();

        // Replace placeholder with unique index
        template = template.replace(/\{\{INDEX\}\}/g, itemCounter);

        // Append to container
        $('#psc-items-container').append(template);

        // Initialize color pickers for the new item
        $('#psc-items-container .psc-item-row:last-child .psc-item-color-picker').wpColorPicker();

        // Increment counter
        itemCounter++;
    });

    /**
     * Remove Item
     */
    $(document).on('click', '.psc-remove-item', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to remove this item?')) {
            $(this).closest('.psc-item-row').fadeOut(300, function() {
                $(this).remove();
            });
        }
    });

    /**
     * Toggle Item Collapse/Expand
     */
    $(document).on('click', '.psc-toggle-item', function(e) {
        e.preventDefault();
        $(this).closest('.psc-item-row').toggleClass('collapsed');
    });

    /**
     * Update item label display when label input changes
     */
    $(document).on('input', '.psc-item-label-input', function() {
        const newLabel = $(this).val().trim();
        const itemRow = $(this).closest('.psc-item-row');
        const labelDisplay = itemRow.find('.psc-item-label');
        const itemNum = itemRow.find('.psc-item-num');

        if (newLabel) {
            labelDisplay.text(newLabel);
        } else {
            // Reset to "Item X" format
            const index = itemRow.index() + 1;
            labelDisplay.html('Item <span class="psc-item-num">' + index + '</span>');
        }
    });

    /**
     * Select Image
     */
    $(document).on('click', '.psc-select-image', function(e) {
        e.preventDefault();

        const button = $(this);
        const itemRow = button.closest('.psc-item-row');
        const imagePreview = itemRow.find('.psc-image-preview');
        const imageIdField = itemRow.find('.psc-image-id');

        // Create media frame
        const frame = wp.media({
            title: 'Select or Upload Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // When image is selected
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();

            // Set image preview (safely)
            const img = $('<img>').attr({
                'src': attachment.url,
                'alt': ''
            });
            imagePreview.html(img);

            // Set image ID
            imageIdField.val(attachment.id);

            // Show remove button if not already visible
            if (!itemRow.find('.psc-remove-image').length) {
                button.after('<button type="button" class="button psc-remove-image">Remove Image</button>');
            }
        });

        // Open media frame
        frame.open();
    });

    /**
     * Remove Image
     */
    $(document).on('click', '.psc-remove-image', function(e) {
        e.preventDefault();

        const button = $(this);
        const itemRow = button.closest('.psc-item-row');
        const imagePreview = itemRow.find('.psc-image-preview');
        const imageIdField = itemRow.find('.psc-image-id');

        if (confirm('Are you sure you want to remove this image?')) {
            imagePreview.html('');
            imageIdField.val('');
            button.remove();
        }
    });

    /**
     * Make items sortable
     */
    $('#psc-items-container').sortable({
        handle: '.psc-item-header',
        placeholder: 'psc-item-placeholder',
        cursor: 'move',
        opacity: 0.6,
        update: function(event, ui) {
            // Update indexes after sorting
            updateItemIndexes();
        }
    });

    /**
     * Update item indexes after sorting
     */
    function updateItemIndexes() {
        $('#psc-items-container .psc-item-row').each(function(index) {
            const row = $(this);

            // Update data-index attribute
            row.attr('data-index', index);

            // Update item number display (only if using default "Item X" label)
            const labelInput = row.find('.psc-item-label-input');
            const customLabel = labelInput.val().trim();

            if (!customLabel) {
                // Only update number if no custom label
                row.find('.psc-item-num').text(index + 1);
            }

            // Update all input names
            row.find('input, textarea').each(function() {
                const input = $(this);
                const name = input.attr('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    input.attr('name', newName);
                }
            });
        });
    }

    /**
     * Copy shortcode to clipboard via button
     */
    $(document).on('click', '.psc-copy-button', function(e) {
        e.preventDefault();

        const button = $(this);
        const targetSelector = button.attr('data-clipboard-target');
        const input = $(targetSelector);
        const textToCopy = input.val();
        const originalText = button.find('.button-text').text();

        // Modern clipboard API with fallback
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(function() {
                showCopiedButtonFeedback(button, originalText);
            }).catch(function(err) {
                // Fallback to old method
                fallbackCopyButton(input, button, originalText);
            });
        } else {
            // Fallback for older browsers or non-HTTPS
            fallbackCopyButton(input, button, originalText);
        }
    });

    /**
     * Show copied feedback on button
     */
    function showCopiedButtonFeedback(button, originalText) {
        button.addClass('copied');
        button.find('.button-text').text('Copied!');
        button.find('.dashicons').removeClass('dashicons-admin-page').addClass('dashicons-yes');

        setTimeout(function() {
            button.removeClass('copied');
            button.find('.button-text').text(originalText);
            button.find('.dashicons').removeClass('dashicons-yes').addClass('dashicons-admin-page');
        }, 2000);
    }

    /**
     * Fallback copy method for button
     */
    function fallbackCopyButton(input, button, originalText) {
        input[0].select();
        try {
            document.execCommand('copy');
            showCopiedButtonFeedback(button, originalText);
        } catch (err) {
            console.error('Failed to copy:', err);
            button.find('.button-text').text('Error');
            setTimeout(function() {
                button.find('.button-text').text(originalText);
            }, 2000);
        }
    }
});
