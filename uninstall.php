<?php
/**
 * Uninstall Product Showcase Cards Plugin
 *
 * Runs when the plugin is deleted via WP Admin → Plugins → Delete.
 *
 * IMPORTANT: This file intentionally does NOT delete showcase posts
 * (post type: product_showcase) or their meta. Users routinely delete
 * a plugin to upgrade it via re-upload; wiping their content on delete
 * is destructive and unexpected.
 *
 * If a clean-uninstall option is needed later, gate it behind an
 * explicit user setting ("Delete all data on uninstall") rather than
 * making it the default behavior.
 *
 * @package Product_Showcase_Cards
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// No-op. Showcase content is preserved.
