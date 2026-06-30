<?php
/**
 * Plugin Name: Product Showcase Cards
 * Plugin URI: https://cordesprinting.com
 * Description: A flexible card-based showcase system for displaying products, services, or content with images, titles, descriptions, and links.
 * Version: 2.1.0
 * Author: Cliff Cordes
 * Author URI: https://cordesprinting.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: product-showcase-cards
 * Requires at least: 5.8
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PSC_VERSION', '2.1.0');
define('PSC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PSC_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class Product_Showcase_Cards {

    // Default settings constants
    const DEFAULT_ITEMS_PER_ROW = 3;
    const DEFAULT_BG_COLOR = '#d4e5a3';
    const DEFAULT_TEXT_COLOR = '#2E2E2E';
    const DEFAULT_HOVER_EFFECT = 'lift';
    const DEFAULT_TITLE_FONT_WEIGHT = '700';
    const DEFAULT_BODY_FONT_WEIGHT = '400';
    const DEFAULT_TAB_COLOR_INACTIVE = '#666666';
    const DEFAULT_TAB_COLOR_HOVER = '#333333';
    const DEFAULT_TAB_COLOR_ACTIVE = '#333333';
    const DEFAULT_TAB_UNDERLINE_HOVER = '#cccccc';
    const DEFAULT_TAB_UNDERLINE_ACTIVE = '#333333';
    const DEFAULT_READ_MORE_TEXT = 'Read more';
    const DEFAULT_DISPLAY_STYLE = 'cards';
    const DEFAULT_BORDER_COLOR = '#000000';
    const DEFAULT_BORDER_WIDTH = 1;
    const DEFAULT_BOXES_BG_COLOR = '#ffffff';
    const DEFAULT_BOXES_TEXT_COLOR = '#2E2E2E';

    /**
     * Constructor
     */
    public function __construct() {
        // Register Custom Post Type
        add_action('init', array($this, 'register_post_type'));

        // Add Meta Boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Enqueue frontend scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));

        // Register shortcode
        add_shortcode('product_showcase', array($this, 'render_shortcode'));

        // Add shortcode column to admin list
        add_filter('manage_product_showcase_posts_columns', array($this, 'add_shortcode_column'));
        add_action('manage_product_showcase_posts_custom_column', array($this, 'render_shortcode_column'), 10, 2);
    }

    /**
     * Register Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => 'Product Showcases',
            'singular_name'         => 'Product Showcase',
            'menu_name'             => 'Showcases',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Showcase',
            'edit_item'             => 'Edit Showcase',
            'new_item'              => 'New Showcase',
            'view_item'             => 'View Showcase',
            'search_items'          => 'Search Showcases',
            'not_found'             => 'No showcases found',
            'not_found_in_trash'    => 'No showcases found in trash',
            'all_items'             => 'All Showcases',
        );

        $args = array(
            'labels'                => $labels,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_icon'             => 'dashicons-images-alt2',
            'supports'              => array('title'),
            'has_archive'           => false,
            'rewrite'               => false,
            'capability_type'       => 'post',
        );

        register_post_type('product_showcase', $args);
    }

    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'psc_showcase_settings',
            'Showcase Settings',
            array($this, 'render_settings_meta_box'),
            'product_showcase',
            'normal',
            'high'
        );

        add_meta_box(
            'psc_showcase_items',
            'Showcase Items',
            array($this, 'render_items_meta_box'),
            'product_showcase',
            'normal',
            'high'
        );

        add_meta_box(
            'psc_shortcode',
            'Shortcode',
            array($this, 'render_shortcode_meta_box'),
            'product_showcase',
            'side',
            'high'
        );
    }

    /**
     * Render Settings Meta Box
     */
    public function render_settings_meta_box($post) {
        wp_nonce_field('psc_save_meta_boxes', 'psc_meta_box_nonce');

        $items_per_row = get_post_meta($post->ID, '_psc_items_per_row', true) ?: self::DEFAULT_ITEMS_PER_ROW;
        $bg_color = get_post_meta($post->ID, '_psc_bg_color', true) ?: self::DEFAULT_BG_COLOR;
        $text_color = get_post_meta($post->ID, '_psc_text_color', true) ?: self::DEFAULT_TEXT_COLOR;
        $hover_effect = get_post_meta($post->ID, '_psc_hover_effect', true) ?: self::DEFAULT_HOVER_EFFECT;
        $title_font = get_post_meta($post->ID, '_psc_title_font', true) ?: '';
        $title_font_weight = get_post_meta($post->ID, '_psc_title_font_weight', true) ?: self::DEFAULT_TITLE_FONT_WEIGHT;
        $title_font_size = get_post_meta($post->ID, '_psc_title_font_size', true) ?: '';
        $body_font = get_post_meta($post->ID, '_psc_body_font', true) ?: '';
        $body_font_weight = get_post_meta($post->ID, '_psc_body_font_weight', true) ?: self::DEFAULT_BODY_FONT_WEIGHT;
        $body_font_size = get_post_meta($post->ID, '_psc_body_font_size', true) ?: '';
        $enable_category_tabs = get_post_meta($post->ID, '_psc_enable_category_tabs', true);
        $enable_all_tab = get_post_meta($post->ID, '_psc_enable_all_tab', true);
        $tab_color_inactive = get_post_meta($post->ID, '_psc_tab_color_inactive', true) ?: self::DEFAULT_TAB_COLOR_INACTIVE;
        $tab_color_hover = get_post_meta($post->ID, '_psc_tab_color_hover', true) ?: self::DEFAULT_TAB_COLOR_HOVER;
        $tab_color_active = get_post_meta($post->ID, '_psc_tab_color_active', true) ?: self::DEFAULT_TAB_COLOR_ACTIVE;
        $tab_underline_hover = get_post_meta($post->ID, '_psc_tab_underline_hover', true) ?: self::DEFAULT_TAB_UNDERLINE_HOVER;
        $tab_underline_active = get_post_meta($post->ID, '_psc_tab_underline_active', true) ?: self::DEFAULT_TAB_UNDERLINE_ACTIVE;
        $tab_alignment = get_post_meta($post->ID, '_psc_tab_alignment', true) ?: 'left';
        $display_categories = get_post_meta($post->ID, '_psc_display_categories', true) ?: '';
        $masonry_layout = get_post_meta($post->ID, '_psc_masonry_layout', true);
        $global_bg_color = get_post_meta($post->ID, '_psc_global_bg_color', true) ?: '';
        $global_text_color = get_post_meta($post->ID, '_psc_global_text_color', true) ?: '';
        $display_style = get_post_meta($post->ID, '_psc_display_style', true) ?: self::DEFAULT_DISPLAY_STYLE;
        $border_color = get_post_meta($post->ID, '_psc_border_color', true) ?: self::DEFAULT_BORDER_COLOR;
        $border_width = get_post_meta($post->ID, '_psc_border_width', true) ?: self::DEFAULT_BORDER_WIDTH;
        ?>
        <div class="psc-settings-container">
            <!-- Display Style -->
            <h3 class="psc-section-header">Display Style</h3>
            <div class="psc-settings-grid">
                <div class="psc-setting-row">
                    <label for="psc_display_style">Style:</label>
                    <select name="psc_display_style" id="psc_display_style">
                        <option value="cards" <?php selected($display_style, 'cards'); ?>>Cards</option>
                        <option value="boxes" <?php selected($display_style, 'boxes'); ?>>Boxes</option>
                    </select>
                    <p class="description" style="margin-top: 5px;">Cards: Rounded corners with colored content areas. Boxes: Clean bordered grid with edge-to-edge images.</p>
                </div>
            </div>

            <!-- Layout & Structure -->
            <h3 class="psc-section-header">Layout & Structure</h3>
            <div class="psc-settings-grid">
                <div class="psc-setting-row">
                    <label for="psc_items_per_row">Items Per Row:</label>
                    <select name="psc_items_per_row" id="psc_items_per_row">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php selected($items_per_row, $i); ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_enable_category_tabs">
                        <input type="checkbox" name="psc_enable_category_tabs" id="psc_enable_category_tabs" value="1" <?php checked($enable_category_tabs, '1'); ?>>
                        Enable Category Tabs
                    </label>
                    <p class="description" style="margin-top: 5px;">Show category tabs for filtering items</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_enable_all_tab">
                        <input type="checkbox" name="psc_enable_all_tab" id="psc_enable_all_tab" value="1" <?php checked($enable_all_tab, '1'); ?>>
                        Enable "All" Tab
                    </label>
                    <p class="description" style="margin-top: 5px;">When on, an "All" tab is added automatically as the default and shows every item. When off, the first listed category is the default and items only appear in categories they're assigned to.</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_display_categories">Display Categories:</label>
                    <input type="text" name="psc_display_categories" id="psc_display_categories" value="<?php echo esc_attr($display_categories); ?>" placeholder="Most Popular, Glass Printing, Cartons" style="max-width: 400px;">
                    <p class="description" style="margin-top: 5px;">Comma-separated list of categories to show as tabs (do not include "All" here — use the toggle above)</p>
                </div>

                <div class="psc-setting-row" data-psc-cards-only="1">
                    <label for="psc_masonry_layout">
                        <input type="checkbox" name="psc_masonry_layout" id="psc_masonry_layout" value="1" <?php checked($masonry_layout, '1'); ?>>
                        Masonry Layout (Variable Heights)
                    </label>
                    <p class="description" style="margin-top: 5px;">Cards will have natural heights based on their content instead of matching the tallest card in each row</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_global_bg_color">Global Card Background Color:</label>
                    <input type="text" name="psc_global_bg_color" id="psc_global_bg_color" value="<?php echo esc_attr($global_bg_color); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Overrides individual card background colors for all cards in this showcase. Leave blank to use per-card colors.</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_global_text_color">Global Card Text Color:</label>
                    <input type="text" name="psc_global_text_color" id="psc_global_text_color" value="<?php echo esc_attr($global_text_color); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Overrides individual card text colors for all cards in this showcase. Leave blank to use per-card colors.</p>
                </div>
            </div>

            <!-- Typography -->
            <h3 class="psc-section-header">Typography</h3>
            <div class="psc-settings-grid">
                <div class="psc-setting-row">
                    <label for="psc_title_font">Title Font:</label>
                    <input type="text" name="psc_title_font" id="psc_title_font" value="<?php echo esc_attr($title_font); ?>" placeholder="'GT Pressura', sans-serif" style="max-width: 400px;">
                    <p class="description" style="margin-top: 5px;">CSS font-family value (e.g., 'GT Pressura', sans-serif)</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_title_font_weight">Title Font Weight:</label>
                    <select name="psc_title_font_weight" id="psc_title_font_weight">
                        <option value="300" <?php selected($title_font_weight, '300'); ?>>Light (300)</option>
                        <option value="400" <?php selected($title_font_weight, '400'); ?>>Regular (400)</option>
                        <option value="500" <?php selected($title_font_weight, '500'); ?>>Medium (500)</option>
                        <option value="600" <?php selected($title_font_weight, '600'); ?>>Semi-Bold (600)</option>
                        <option value="700" <?php selected($title_font_weight, '700'); ?>>Bold (700)</option>
                        <option value="800" <?php selected($title_font_weight, '800'); ?>>Extra Bold (800)</option>
                    </select>
                    <p class="description" style="margin-top: 5px;">Font weight for card titles, tabs, and "Read more" text</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_title_font_size">Title Font Size:</label>
                    <input type="number" name="psc_title_font_size" id="psc_title_font_size" value="<?php echo esc_attr($title_font_size); ?>" placeholder="e.g., 18" min="8" max="120" style="max-width: 100px;"> px
                    <p class="description" style="margin-top: 5px;">Font size in pixels for card titles (leave blank for default)</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_body_font">Body Font:</label>
                    <input type="text" name="psc_body_font" id="psc_body_font" value="<?php echo esc_attr($body_font); ?>" placeholder="'Sofia Pro Rounded Regular', sans-serif" style="max-width: 400px;">
                    <p class="description" style="margin-top: 5px;">Used for descriptions</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_body_font_weight">Body Font Weight:</label>
                    <select name="psc_body_font_weight" id="psc_body_font_weight">
                        <option value="300" <?php selected($body_font_weight, '300'); ?>>Light (300)</option>
                        <option value="400" <?php selected($body_font_weight, '400'); ?>>Regular (400)</option>
                        <option value="500" <?php selected($body_font_weight, '500'); ?>>Medium (500)</option>
                        <option value="600" <?php selected($body_font_weight, '600'); ?>>Semi-Bold (600)</option>
                        <option value="700" <?php selected($body_font_weight, '700'); ?>>Bold (700)</option>
                        <option value="800" <?php selected($body_font_weight, '800'); ?>>Extra Bold (800)</option>
                    </select>
                    <p class="description" style="margin-top: 5px;">Font weight for card descriptions</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_body_font_size">Body Font Size:</label>
                    <input type="number" name="psc_body_font_size" id="psc_body_font_size" value="<?php echo esc_attr($body_font_size); ?>" placeholder="e.g., 14" min="8" max="120" style="max-width: 100px;"> px
                    <p class="description" style="margin-top: 5px;">Font size in pixels for card descriptions (leave blank for default)</p>
                </div>
            </div>

            <!-- Category Tab Colors -->
            <h3 class="psc-section-header">Category Tab Colors</h3>
            <div class="psc-settings-grid">
                <div class="psc-setting-row">
                    <label for="psc_tab_color_inactive">Inactive Tab Color:</label>
                    <input type="text" name="psc_tab_color_inactive" id="psc_tab_color_inactive" value="<?php echo esc_attr($tab_color_inactive); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Color for unselected tabs</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_tab_color_hover">Hover Tab Color:</label>
                    <input type="text" name="psc_tab_color_hover" id="psc_tab_color_hover" value="<?php echo esc_attr($tab_color_hover); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Color when hovering over tabs</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_tab_color_active">Active Tab Color:</label>
                    <input type="text" name="psc_tab_color_active" id="psc_tab_color_active" value="<?php echo esc_attr($tab_color_active); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Color for the currently selected tab</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_tab_underline_hover">Hover Underline Color:</label>
                    <input type="text" name="psc_tab_underline_hover" id="psc_tab_underline_hover" value="<?php echo esc_attr($tab_underline_hover); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Underline color when hovering over tabs</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_tab_underline_active">Active Underline Color:</label>
                    <input type="text" name="psc_tab_underline_active" id="psc_tab_underline_active" value="<?php echo esc_attr($tab_underline_active); ?>" class="psc-color-picker">
                    <p class="description" style="margin-top: 5px;">Underline color for the currently selected tab</p>
                </div>

                <div class="psc-setting-row">
                    <label for="psc_tab_alignment">Tab Alignment:</label>
                    <select name="psc_tab_alignment" id="psc_tab_alignment">
                        <option value="left" <?php selected($tab_alignment, 'left'); ?>>Left</option>
                        <option value="center" <?php selected($tab_alignment, 'center'); ?>>Center</option>
                        <option value="right" <?php selected($tab_alignment, 'right'); ?>>Right</option>
                    </select>
                    <p class="description" style="margin-top: 5px;">Horizontal alignment of category tabs</p>
                </div>
            </div>

            <!-- Effects (Cards Only) -->
            <div data-psc-cards-only="1">
                <h3 class="psc-section-header">Effects</h3>
                <div class="psc-settings-grid">
                    <div class="psc-setting-row">
                        <label for="psc_hover_effect">Hover Effect:</label>
                        <select name="psc_hover_effect" id="psc_hover_effect">
                            <option value="none" <?php selected($hover_effect, 'none'); ?>>None</option>
                            <option value="lift" <?php selected($hover_effect, 'lift'); ?>>Subtle Lift</option>
                            <option value="zoom" <?php selected($hover_effect, 'zoom'); ?>>Image Zoom</option>
                            <option value="lift-zoom" <?php selected($hover_effect, 'lift-zoom'); ?>>Lift + Zoom</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Border Settings (Boxes Only) -->
            <div id="psc-boxes-settings" style="<?php echo $display_style !== 'boxes' ? 'display:none;' : ''; ?>">
                <h3 class="psc-section-header">Border Settings</h3>
                <div class="psc-settings-grid">
                    <div class="psc-setting-row">
                        <label for="psc_border_color">Border Color:</label>
                        <input type="text" name="psc_border_color" id="psc_border_color" value="<?php echo esc_attr($border_color); ?>" class="psc-color-picker">
                        <p class="description" style="margin-top: 5px;">Color of the grid borders</p>
                    </div>

                    <div class="psc-setting-row">
                        <label for="psc_border_width">Border Width (px):</label>
                        <input type="number" name="psc_border_width" id="psc_border_width" value="<?php echo esc_attr($border_width); ?>" min="1" max="10" style="max-width: 80px;">
                        <p class="description" style="margin-top: 5px;">Width in pixels (1-10)</p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Items Meta Box
     */
    public function render_items_meta_box($post) {
        $items = get_post_meta($post->ID, '_psc_items', true) ?: array();
        ?>
        <div id="psc-items-container">
            <?php
            if (!empty($items)) {
                foreach ($items as $index => $item) {
                    $this->render_item_row($index, $item);
                }
            }
            ?>
        </div>

        <button type="button" class="button button-primary" id="psc-add-item">Add Item</button>

        <script type="text/html" id="psc-item-template">
            <?php $this->render_item_row('{{INDEX}}', array()); ?>
        </script>
        <?php
    }

    /**
     * Render Individual Item Row
     */
    private function render_item_row($index, $item = array()) {
        $image_id = isset($item['image_id']) ? $item['image_id'] : '';
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $title = isset($item['title']) ? $item['title'] : '';
        $label = isset($item['label']) ? $item['label'] : '';
        $description = isset($item['description']) ? $item['description'] : '';
        $url = isset($item['url']) ? $item['url'] : '';
        $bg_color = isset($item['bg_color']) ? $item['bg_color'] : self::DEFAULT_BG_COLOR;
        $text_color = isset($item['text_color']) ? $item['text_color'] : self::DEFAULT_TEXT_COLOR;
        $read_more_text = isset($item['read_more_text']) ? $item['read_more_text'] : self::DEFAULT_READ_MORE_TEXT;
        $categories = isset($item['categories']) ? $item['categories'] : '';
        ?>
        <div class="psc-item-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="psc-item-header">
                <div class="psc-item-number">
                    <span class="dashicons dashicons-menu" style="cursor: move; color: #999;"></span>
                    <span class="psc-item-label">
                        <?php if (!empty($label)): ?>
                            <?php echo esc_html($label); ?>
                        <?php else: ?>
                            Item <span class="psc-item-num"><?php echo is_numeric($index) ? ($index + 1) : ''; ?></span>
                        <?php endif; ?>
                    </span>
                    <button type="button" class="psc-toggle-item" title="Collapse/Expand">
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                    </button>
                </div>
                <button type="button" class="button psc-remove-item" title="Remove Item">
                    <span class="dashicons dashicons-trash"></span> Remove
                </button>
            </div>

            <div class="psc-item-body">
                <div class="psc-item-handle">
                    <!-- Drag handle moved to header -->
                </div>

                <div class="psc-item-content">
                <div class="psc-item-field">
                    <label>Item Label (Optional):</label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="e.g., Acrylic Printing, Glass Services" class="psc-item-label-input">
                    <p class="description" style="margin-top: 5px; font-size: 12px;">Custom name for this item in the admin panel. If left blank, will show "Item 1", "Item 2", etc.</p>
                </div>

                <div class="psc-item-field psc-item-image psc-field-required">
                    <label>Image:<span class="psc-required-indicator">*</span></label>
                    <div class="psc-image-preview">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="psc_items[<?php echo esc_attr($index); ?>][image_id]" class="psc-image-id" value="<?php echo esc_attr($image_id); ?>">
                    <button type="button" class="button psc-select-image">Select Image</button>
                    <?php if ($image_url): ?>
                        <button type="button" class="button psc-remove-image">Remove Image</button>
                    <?php endif; ?>
                    <p class="description" style="margin-top: 5px; font-size: 12px;">Required - Item will not be saved without an image</p>
                </div>

                <div class="psc-item-field psc-field-required">
                    <label>Title:<span class="psc-required-indicator">*</span></label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][title]" value="<?php echo esc_attr($title); ?>" placeholder="Item Title" required>
                    <p class="description" style="margin-top: 5px; font-size: 12px;">Required - Item will not be saved without a title</p>
                </div>

                <div class="psc-item-field">
                    <label>Description:</label>
                    <textarea name="psc_items[<?php echo esc_attr($index); ?>][description]" rows="3" placeholder="This is the item description. It should be able to accommodate 2 lines of text."><?php echo esc_textarea($description); ?></textarea>
                </div>

                <div class="psc-item-field">
                    <label>Read More URL:</label>
                    <input type="url" name="psc_items[<?php echo esc_attr($index); ?>][url]" value="<?php echo esc_url($url); ?>" placeholder="https://example.com/page">
                </div>

                <div class="psc-item-field">
                    <label>Background Color:</label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][bg_color]" value="<?php echo esc_attr($bg_color); ?>" class="psc-color-picker psc-item-color-picker">
                </div>

                <div class="psc-item-field">
                    <label>Text Color:</label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][text_color]" value="<?php echo esc_attr($text_color); ?>" class="psc-color-picker psc-item-color-picker">
                </div>

                <div class="psc-item-field" data-psc-cards-only="1">
                    <label>Read More Text:</label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][read_more_text]" value="<?php echo esc_attr($read_more_text); ?>" placeholder="Read more">
                </div>

                <div class="psc-item-field">
                    <label>Categories:</label>
                    <input type="text" name="psc_items[<?php echo esc_attr($index); ?>][categories]" value="<?php echo esc_attr($categories); ?>" placeholder="Most Popular, Glass Printing">
                    <p class="description" style="margin-top: 5px; font-size: 12px;">Comma-separated (e.g., "Most Popular, Glass Printing")</p>
                </div>
            </div>

            <div class="psc-item-actions">
                <!-- Actions moved to header -->
            </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Shortcode Meta Box
     */
    public function render_shortcode_meta_box($post) {
        if ($post->post_status === 'publish') {
            $shortcode = '[product_showcase id="' . $post->ID . '"]';
            ?>
            <p>Copy this shortcode and paste it into any page or post:</p>
            <div class="psc-shortcode-wrapper">
                <input type="text" readonly value='<?php echo esc_attr($shortcode); ?>' id="psc-shortcode-input">
                <button type="button" class="psc-copy-button" data-clipboard-target="#psc-shortcode-input">
                    <span class="dashicons dashicons-admin-page"></span>
                    <span class="button-text">Copy</span>
                </button>
            </div>
            <p class="description">You can also use it in PHP templates:</p>
            <code style="display: block; padding: 10px; background: #f5f5f5; word-wrap: break-word;">
                &lt;?php echo do_shortcode('<?php echo esc_js($shortcode); ?>'); ?&gt;
            </code>
            <?php
        } else {
            ?>
            <p>Publish this showcase to generate a shortcode.</p>
            <?php
        }
    }

    /**
     * Sanitize font family to prevent CSS injection
     *
     * @param string $font Font family string
     * @return string Sanitized font family
     */
    private function sanitize_font_family($font) {
        // Remove dangerous CSS characters that could enable injection
        $font = str_replace(array('{', '}', ';', '(', ')', '[', ']', '<', '>', '/', '\\'), '', $font);
        // Allow letters, numbers, spaces, single/double quotes, commas, hyphens, periods
        $font = preg_replace('/[^\w\s\'",.\-]/u', '', $font);
        return trim($font);
    }

    /**
     * Save Meta Boxes
     */
    public function save_meta_boxes($post_id) {
        // Check nonce
        if (!isset($_POST['psc_meta_box_nonce']) || !wp_verify_nonce($_POST['psc_meta_box_nonce'], 'psc_save_meta_boxes')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Verify post type
        if (get_post_type($post_id) !== 'product_showcase') {
            return;
        }

        // Save settings
        if (isset($_POST['psc_items_per_row'])) {
            update_post_meta($post_id, '_psc_items_per_row', sanitize_text_field($_POST['psc_items_per_row']));
        }

        if (isset($_POST['psc_bg_color'])) {
            update_post_meta($post_id, '_psc_bg_color', sanitize_hex_color($_POST['psc_bg_color']));
        }

        if (isset($_POST['psc_text_color'])) {
            update_post_meta($post_id, '_psc_text_color', sanitize_hex_color($_POST['psc_text_color']));
        }

        if (isset($_POST['psc_hover_effect'])) {
            update_post_meta($post_id, '_psc_hover_effect', sanitize_text_field($_POST['psc_hover_effect']));
        }

        // Save display style
        if (isset($_POST['psc_display_style'])) {
            $style = sanitize_text_field($_POST['psc_display_style']);
            if (in_array($style, array('cards', 'boxes'))) {
                update_post_meta($post_id, '_psc_display_style', $style);
            }
        }

        // Save border settings (Boxes style)
        if (isset($_POST['psc_border_color'])) {
            $color = sanitize_hex_color($_POST['psc_border_color']);
            update_post_meta($post_id, '_psc_border_color', $color ?: self::DEFAULT_BORDER_COLOR);
        }

        if (isset($_POST['psc_border_width'])) {
            $width = absint($_POST['psc_border_width']);
            $width = max(1, min(10, $width));
            update_post_meta($post_id, '_psc_border_width', $width);
        }

        if (isset($_POST['psc_title_font'])) {
            update_post_meta($post_id, '_psc_title_font', $this->sanitize_font_family($_POST['psc_title_font']));
        }

        if (isset($_POST['psc_title_font_weight'])) {
            update_post_meta($post_id, '_psc_title_font_weight', sanitize_text_field($_POST['psc_title_font_weight']));
        }

        if (isset($_POST['psc_title_font_size'])) {
            $size = intval($_POST['psc_title_font_size']);
            update_post_meta($post_id, '_psc_title_font_size', $size > 0 ? $size : '');
        }

        if (isset($_POST['psc_body_font'])) {
            update_post_meta($post_id, '_psc_body_font', $this->sanitize_font_family($_POST['psc_body_font']));
        }

        if (isset($_POST['psc_body_font_weight'])) {
            update_post_meta($post_id, '_psc_body_font_weight', sanitize_text_field($_POST['psc_body_font_weight']));
        }

        if (isset($_POST['psc_body_font_size'])) {
            $size = intval($_POST['psc_body_font_size']);
            update_post_meta($post_id, '_psc_body_font_size', $size > 0 ? $size : '');
        }

        // Save tab colors
        if (isset($_POST['psc_tab_color_inactive'])) {
            update_post_meta($post_id, '_psc_tab_color_inactive', sanitize_hex_color($_POST['psc_tab_color_inactive']));
        }

        if (isset($_POST['psc_tab_color_hover'])) {
            update_post_meta($post_id, '_psc_tab_color_hover', sanitize_hex_color($_POST['psc_tab_color_hover']));
        }

        if (isset($_POST['psc_tab_color_active'])) {
            update_post_meta($post_id, '_psc_tab_color_active', sanitize_hex_color($_POST['psc_tab_color_active']));
        }

        if (isset($_POST['psc_tab_underline_hover'])) {
            update_post_meta($post_id, '_psc_tab_underline_hover', sanitize_hex_color($_POST['psc_tab_underline_hover']));
        }

        if (isset($_POST['psc_tab_underline_active'])) {
            update_post_meta($post_id, '_psc_tab_underline_active', sanitize_hex_color($_POST['psc_tab_underline_active']));
        }

        if (isset($_POST['psc_tab_alignment'])) {
            $alignment = sanitize_text_field($_POST['psc_tab_alignment']);
            // Validate alignment value
            if (in_array($alignment, array('left', 'center', 'right'))) {
                update_post_meta($post_id, '_psc_tab_alignment', $alignment);
            }
        }

        // Save layout settings
        if (isset($_POST['psc_masonry_layout'])) {
            update_post_meta($post_id, '_psc_masonry_layout', '1');
        } else {
            delete_post_meta($post_id, '_psc_masonry_layout');
        }

        if (isset($_POST['psc_global_bg_color'])) {
            $color = sanitize_hex_color($_POST['psc_global_bg_color']);
            if ($color) {
                update_post_meta($post_id, '_psc_global_bg_color', $color);
            } else {
                delete_post_meta($post_id, '_psc_global_bg_color');
            }
        }

        if (isset($_POST['psc_global_text_color'])) {
            $color = sanitize_hex_color($_POST['psc_global_text_color']);
            if ($color) {
                update_post_meta($post_id, '_psc_global_text_color', $color);
            } else {
                delete_post_meta($post_id, '_psc_global_text_color');
            }
        }

        // Save category settings
        if (isset($_POST['psc_enable_category_tabs'])) {
            update_post_meta($post_id, '_psc_enable_category_tabs', '1');
        } else {
            delete_post_meta($post_id, '_psc_enable_category_tabs');
        }

        if (isset($_POST['psc_enable_all_tab'])) {
            update_post_meta($post_id, '_psc_enable_all_tab', '1');
        } else {
            delete_post_meta($post_id, '_psc_enable_all_tab');
        }

        if (isset($_POST['psc_display_categories'])) {
            update_post_meta($post_id, '_psc_display_categories', sanitize_text_field($_POST['psc_display_categories']));
        }

        // Save items
        if (isset($_POST['psc_items'])) {
            $items = array();
            foreach ($_POST['psc_items'] as $item) {
                // Skip items without required fields (image and title)
                if (empty($item['image_id']) || empty($item['title'])) {
                    continue;
                }

                $items[] = array(
                    'image_id' => absint($item['image_id']),
                    'title' => sanitize_text_field($item['title']),
                    'label' => sanitize_text_field($item['label']),
                    'description' => sanitize_textarea_field($item['description']),
                    'url' => esc_url_raw($item['url']),
                    'bg_color' => sanitize_hex_color($item['bg_color']),
                    'text_color' => sanitize_hex_color($item['text_color']),
                    'read_more_text' => sanitize_text_field($item['read_more_text']),
                    'categories' => sanitize_text_field($item['categories']),
                );
            }
            update_post_meta($post_id, '_psc_items', $items);
        } else {
            delete_post_meta($post_id, '_psc_items');
        }
    }

    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;

        if ($post_type !== 'product_showcase') {
            return;
        }

        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue admin styles
        wp_enqueue_style('psc-admin-css', PSC_PLUGIN_URL . 'css/admin.css', array(), PSC_VERSION);

        // Enqueue admin scripts
        wp_enqueue_script('psc-admin-js', PSC_PLUGIN_URL . 'js/admin.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker'), PSC_VERSION, true);
    }

    /**
     * Enqueue Frontend Assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('psc-frontend-css', PSC_PLUGIN_URL . 'css/frontend.css', array(), PSC_VERSION);
        wp_enqueue_script('psc-frontend-js', PSC_PLUGIN_URL . 'js/frontend.js', array('jquery'), PSC_VERSION, true);
    }

    /**
     * Render Shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $post_id = intval($atts['id']);

        if (!$post_id || get_post_type($post_id) !== 'product_showcase') {
            return '<p>Invalid showcase ID.</p>';
        }

        // Get showcase data
        $items = get_post_meta($post_id, '_psc_items', true) ?: array();
        $items_per_row = get_post_meta($post_id, '_psc_items_per_row', true) ?: self::DEFAULT_ITEMS_PER_ROW;
        $bg_color = get_post_meta($post_id, '_psc_bg_color', true) ?: self::DEFAULT_BG_COLOR;
        $text_color = get_post_meta($post_id, '_psc_text_color', true) ?: self::DEFAULT_TEXT_COLOR;
        $hover_effect = get_post_meta($post_id, '_psc_hover_effect', true) ?: self::DEFAULT_HOVER_EFFECT;
        $title_font = get_post_meta($post_id, '_psc_title_font', true) ?: '';
        $title_font_weight = get_post_meta($post_id, '_psc_title_font_weight', true) ?: self::DEFAULT_TITLE_FONT_WEIGHT;
        $title_font_size = get_post_meta($post_id, '_psc_title_font_size', true) ?: '';
        $body_font = get_post_meta($post_id, '_psc_body_font', true) ?: '';
        $body_font_weight = get_post_meta($post_id, '_psc_body_font_weight', true) ?: self::DEFAULT_BODY_FONT_WEIGHT;
        $body_font_size = get_post_meta($post_id, '_psc_body_font_size', true) ?: '';
        $enable_category_tabs = get_post_meta($post_id, '_psc_enable_category_tabs', true);
        $enable_all_tab = get_post_meta($post_id, '_psc_enable_all_tab', true);
        $tab_color_inactive = get_post_meta($post_id, '_psc_tab_color_inactive', true) ?: self::DEFAULT_TAB_COLOR_INACTIVE;
        $tab_color_hover = get_post_meta($post_id, '_psc_tab_color_hover', true) ?: self::DEFAULT_TAB_COLOR_HOVER;
        $tab_color_active = get_post_meta($post_id, '_psc_tab_color_active', true) ?: self::DEFAULT_TAB_COLOR_ACTIVE;
        $tab_underline_hover = get_post_meta($post_id, '_psc_tab_underline_hover', true) ?: self::DEFAULT_TAB_UNDERLINE_HOVER;
        $tab_underline_active = get_post_meta($post_id, '_psc_tab_underline_active', true) ?: self::DEFAULT_TAB_UNDERLINE_ACTIVE;
        $tab_alignment = get_post_meta($post_id, '_psc_tab_alignment', true) ?: 'left';
        $display_categories = get_post_meta($post_id, '_psc_display_categories', true) ?: '';
        $masonry_layout = get_post_meta($post_id, '_psc_masonry_layout', true);
        $global_bg_color = get_post_meta($post_id, '_psc_global_bg_color', true) ?: '';
        $global_text_color = get_post_meta($post_id, '_psc_global_text_color', true) ?: '';
        $display_style = get_post_meta($post_id, '_psc_display_style', true) ?: self::DEFAULT_DISPLAY_STYLE;
        $border_color = get_post_meta($post_id, '_psc_border_color', true) ?: self::DEFAULT_BORDER_COLOR;
        $border_width = get_post_meta($post_id, '_psc_border_width', true) ?: self::DEFAULT_BORDER_WIDTH;
        $is_boxes = ($display_style === 'boxes');

        if (empty($items)) {
            return '<p>No items to display.</p>';
        }

        // Parse display categories if tabs are enabled
        $tabs = array();
        if ($enable_category_tabs && !empty($display_categories)) {
            $tabs = array_map('trim', explode(',', $display_categories));
            // Drop any user-typed "All" entries — the "All" tab is controlled by the toggle
            $tabs = array_values(array_filter($tabs, function($t) {
                return strtolower($t) !== 'all' && $t !== '';
            }));
            // Prepend a real "All" tab when the toggle is on
            if ($enable_all_tab) {
                array_unshift($tabs, 'All');
            }
        }

        // Generate unique ID for this showcase instance
        $showcase_id = 'psc-showcase-' . $post_id . '-' . uniqid();

        // Build output
        ob_start();
        ?>
        <?php if (!empty($title_font) || !empty($title_font_weight) || !empty($title_font_size) || !empty($body_font) || !empty($body_font_weight) || !empty($body_font_size) || !empty($tab_color_inactive) || !empty($tab_color_hover) || !empty($tab_color_active) || !empty($tab_underline_hover) || !empty($tab_underline_active) || $is_boxes): ?>
        <style>
            <?php if (!empty($title_font) || !empty($title_font_weight) || !empty($title_font_size)): ?>
            #<?php echo $showcase_id; ?> .psc-item-title {
                <?php if (!empty($title_font)): ?>
                font-family: <?php echo wp_strip_all_tags($title_font); ?> !important;
                <?php endif; ?>
                <?php if (!empty($title_font_weight)): ?>
                font-weight: <?php echo intval($title_font_weight); ?> !important;
                <?php endif; ?>
                <?php if (!empty($title_font_size)): ?>
                font-size: <?php echo intval($title_font_size); ?>px !important;
                <?php endif; ?>
            }
            <?php endif; ?>
            <?php if (!empty($title_font) || !empty($title_font_weight)): ?>
            #<?php echo $showcase_id; ?> .psc-read-more,
            #<?php echo $showcase_id; ?> .psc-category-tab {
                <?php if (!empty($title_font)): ?>
                font-family: <?php echo wp_strip_all_tags($title_font); ?> !important;
                <?php endif; ?>
                <?php if (!empty($title_font_weight)): ?>
                font-weight: <?php echo intval($title_font_weight); ?> !important;
                <?php endif; ?>
            }
            <?php endif; ?>
            <?php if (!empty($body_font) || !empty($body_font_weight) || !empty($body_font_size)): ?>
            #<?php echo $showcase_id; ?> .psc-item-description {
                <?php if (!empty($body_font)): ?>
                font-family: <?php echo wp_strip_all_tags($body_font); ?>;
                <?php endif; ?>
                <?php if (!empty($body_font_weight)): ?>
                font-weight: <?php echo intval($body_font_weight); ?> !important;
                <?php endif; ?>
                <?php if (!empty($body_font_size)): ?>
                font-size: <?php echo intval($body_font_size); ?>px !important;
                <?php endif; ?>
            }
            <?php endif; ?>
            /* Tab Colors */
            #<?php echo $showcase_id; ?> .psc-category-tab {
                color: <?php echo esc_attr($tab_color_inactive); ?> !important;
            }
            #<?php echo $showcase_id; ?> .psc-category-tab:hover {
                color: <?php echo esc_attr($tab_color_hover); ?> !important;
                border-bottom-color: <?php echo esc_attr($tab_underline_hover); ?> !important;
            }
            #<?php echo $showcase_id; ?> .psc-category-tab.psc-tab-active {
                color: <?php echo esc_attr($tab_color_active); ?> !important;
                border-bottom-color: <?php echo esc_attr($tab_underline_active); ?> !important;
            }
            <?php if ($is_boxes): ?>
            /* Boxes: All items get right + bottom borders */
            #<?php echo $showcase_id; ?> .psc-item {
                border-right: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
                border-bottom: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
            }
            /* Boxes: First row items get top border */
            #<?php echo $showcase_id; ?> .psc-item:nth-child(-n+<?php echo intval($items_per_row); ?>) {
                border-top: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
            }
            /* Boxes: First column items get left border */
            #<?php echo $showcase_id; ?> .psc-item:nth-child(<?php echo intval($items_per_row); ?>n+1) {
                border-left: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
            }
            /* Boxes: Tablet (2-col) - override first-row and first-col */
            @media screen and (max-width: 1024px) {
                #<?php echo $showcase_id; ?> .psc-item {
                    border-top: none;
                    border-left: none;
                    border-right: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
                    border-bottom: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
                }
                #<?php echo $showcase_id; ?> .psc-item:nth-child(-n+2) {
                    border-top: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
                }
                #<?php echo $showcase_id; ?> .psc-item:nth-child(2n+1) {
                    border-left: <?php echo intval($border_width); ?>px solid <?php echo esc_attr($border_color); ?>;
                }
            }
            <?php endif; ?>
        </style>
        <?php endif; ?>
        <?php
        ?>
        <div id="<?php echo $showcase_id; ?>" class="psc-showcase psc-style-<?php echo esc_attr($display_style); ?><?php echo !$is_boxes ? ' psc-hover-' . esc_attr($hover_effect) : ''; ?><?php echo ($masonry_layout && !$is_boxes) ? ' psc-layout-masonry' : ''; ?>" data-items-per-row="<?php echo esc_attr($items_per_row); ?>"<?php if ($is_boxes): ?> style="--psc-border-color: <?php echo esc_attr($border_color); ?>; --psc-border-width: <?php echo intval($border_width); ?>px;"<?php endif; ?>>
            <?php if (!empty($tabs)): ?>
            <div class="psc-category-tabs psc-align-<?php echo esc_attr($tab_alignment); ?>" role="tablist" aria-label="Product categories">
                <?php foreach ($tabs as $index => $tab): ?>
                    <button
                        class="psc-category-tab<?php echo $index === 0 ? ' psc-tab-active' : ''; ?>"
                        data-category="<?php echo esc_attr($tab); ?>"
                        role="tab"
                        aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                        aria-label="Filter by <?php echo esc_attr($tab); ?>"
                        tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
                    >
                        <?php echo esc_html($tab); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="psc-showcase-grid" data-columns="<?php echo esc_attr($items_per_row); ?>" style="grid-template-columns: repeat(<?php echo esc_attr($items_per_row); ?>, 1fr);<?php echo $is_boxes ? ' gap: 0;' : ''; ?>">
                <?php foreach ($items as $item): ?>
                    <?php
                    $image_id = !empty($item['image_id']) ? $item['image_id'] : 0;
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
                    if (!$image_url) continue;

                    // Get responsive image attributes
                    $image_srcset = $image_id ? wp_get_attachment_image_srcset($image_id, 'large') : '';
                    $image_sizes = $image_id ? wp_get_attachment_image_sizes($image_id, 'large') : '';

                    // Get item-specific colors and text, or use defaults
                    // Global colors override individual card colors
                    if ($is_boxes) {
                        // Boxes: default to white bg / dark text
                        $item_bg_color = !empty($global_bg_color) ? $global_bg_color : (!empty($item['bg_color']) ? $item['bg_color'] : self::DEFAULT_BOXES_BG_COLOR);
                        $item_text_color = !empty($global_text_color) ? $global_text_color : (!empty($item['text_color']) ? $item['text_color'] : self::DEFAULT_BOXES_TEXT_COLOR);
                    } else {
                        // Cards: default to green bg / dark text
                        $item_bg_color = !empty($global_bg_color) ? $global_bg_color : (!empty($item['bg_color']) ? $item['bg_color'] : $bg_color);
                        $item_text_color = !empty($global_text_color) ? $global_text_color : (!empty($item['text_color']) ? $item['text_color'] : $text_color);
                    }
                    $item_read_more = !empty($item['read_more_text']) ? $item['read_more_text'] : self::DEFAULT_READ_MORE_TEXT;
                    $item_categories = !empty($item['categories']) ? $item['categories'] : '';
                    ?>
                    <div class="psc-item" data-categories="<?php echo esc_attr($item_categories); ?>">
                        <a href="<?php echo esc_url($item['url']); ?>" class="psc-item-link">
                            <div class="psc-item-image">
                                <img
                                    src="<?php echo esc_url($image_url); ?>"
                                    <?php if ($image_srcset): ?>
                                    srcset="<?php echo esc_attr($image_srcset); ?>"
                                    <?php endif; ?>
                                    <?php if ($image_sizes): ?>
                                    sizes="<?php echo esc_attr($image_sizes); ?>"
                                    <?php endif; ?>
                                    alt="<?php echo esc_attr($item['title']); ?>"
                                    loading="lazy"
                                >
                            </div>
                            <div class="psc-item-content" style="background-color: <?php echo esc_attr($item_bg_color); ?>; color: <?php echo esc_attr($item_text_color); ?>;">
                                <h3 class="psc-item-title"><?php echo esc_html($item['title']); ?></h3>
                                <?php if (!empty($item['description'])): ?>
                                <p class="psc-item-description"><?php echo esc_html($item['description']); ?></p>
                                <?php endif; ?>
                                <?php if (!$is_boxes): ?>
                                <span class="psc-read-more"><?php echo esc_html($item_read_more); ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Add Shortcode Column to Admin List
     */
    public function add_shortcode_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['shortcode'] = 'Shortcode';
            }
        }
        return $new_columns;
    }

    /**
     * Render Shortcode Column Content
     */
    public function render_shortcode_column($column, $post_id) {
        if ($column === 'shortcode') {
            $post = get_post($post_id);
            if ($post->post_status === 'publish') {
                echo '<code>[product_showcase id="' . $post_id . '"]</code>';
            } else {
                echo '<span style="color: #999;">Publish to generate</span>';
            }
        }
    }
}

// Initialize plugin
new Product_Showcase_Cards();
