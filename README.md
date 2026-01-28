# Product Showcase Cards

**Version:** 2.0.0
**Author:** Cliff Cordes
**License:** GPL v2 or later
**Requires WordPress:** 5.8+
**Tested up to:** 6.7
**Requires PHP:** 7.4+

A flexible, accessible, and beautifully designed card-based showcase system for WordPress. Perfect for displaying products, services, portfolio items, or any content with images, titles, descriptions, and links.

---

## 🎯 Key Features

### Core Functionality
- **Custom Post Type** - Dedicated showcase management system
- **Drag & Drop Ordering** - Easily reorder items with intuitive interface
- **Category Filtering** - Dynamic tabs for filtering items by category
- **Responsive Design** - Perfect display on all devices
- **Multiple Layouts** - 1-8 items per row

### Customization
- **Typography Control** - Custom fonts and weights for titles and body text
- **Color Customization** - Per-item background and text colors with showcase-wide defaults
- **Hover Effects** - Choose from Lift, Zoom, Lift+Zoom, or None
- **Tab Styling** - Fully customizable category tab colors and underlines

### Accessibility & Performance (New in 2.0!)
- ♿ **WCAG Compliant** - Full keyboard navigation and screen reader support
- ⚡ **Performance Optimized** - Lazy loading, responsive images with srcset
- 🎨 **Modern CSS** - Reduced motion support for accessibility
- 🔒 **Security Hardened** - Enhanced input sanitization and XSS protection

---

## 📦 Installation

### Via WordPress Admin
1. Download the plugin ZIP file
2. Go to WordPress Admin > Plugins > Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" then "Activate"

### Manual Installation
1. Upload the `product-showcase-cards` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress

---

## 🚀 Quick Start Guide

### 1. Create a Showcase
1. Navigate to **Showcases** in your WordPress admin
2. Click **Add New**
3. Give your showcase a title (for internal use only)

### 2. Configure Settings

**Layout & Structure:**
- **Items Per Row:** Choose 1-8 (default: 3)
- **Enable Category Tabs:** Toggle category filtering on/off
- **Display Categories:** Comma-separated list (e.g., "All, Most Popular, Glass Printing")

**Typography:**
- **Title Font:** Custom font family for titles (e.g., 'GT Pressura', sans-serif)
- **Title Font Weight:** 300-800 (default: 700 Bold)
- **Body Font:** Custom font family for descriptions (e.g., 'Sofia Pro', sans-serif)
- **Body Font Weight:** 300-800 (default: 400 Regular)

**Category Tab Colors:**
- **Inactive Tab Color:** Default #666666
- **Hover Tab Color:** Default #333333
- **Active Tab Color:** Default #333333
- **Hover Underline:** Default #cccccc
- **Active Underline:** Default #333333

**Effects:**
- **Hover Effect:** None, Lift, Zoom, or Lift+Zoom

### 3. Add Items

Click **Add Item** to create a new card.

**Required Fields** (marked with red *):
- **Image*** - Select from media library (items without images won't be saved)
- **Title*** - Item headline (items without titles won't be saved)

**Optional Fields:**
- **Description** - Brief item description (3 lines max recommended)
- **Read More URL** - Link destination
- **Background Color** - Custom card background (overrides showcase default)
- **Text Color** - Custom text color (overrides showcase default)
- **Read More Text** - Custom link text (default: "Read more")
- **Categories** - Comma-separated list for filtering (e.g., "Most Popular, Glass Printing")

**Item Management Features:**
- **Numbered Items** - "Item 1", "Item 2", etc. for easy reference
- **Drag to Reorder** - Click the item header and drag to reorder
- **Remove Button** - Quick delete in the item header

### 4. Publish & Copy Shortcode
1. Click **Publish**
2. Find the shortcode in the sidebar: `[product_showcase id="123"]`
3. Click the blue **Copy** button for one-click copying
4. Paste into any page or post

---

## 💻 Usage Examples

### Basic Shortcode
```
[product_showcase id="123"]
```

### PHP Template
```php
<?php echo do_shortcode('[product_showcase id="123"]'); ?>
```

### Real-World Examples

**Product Grid (3 Across):**
```
Title: Acrylic Printing
Description: Crystal-clear prints on premium acrylic for stunning wall art
Categories: Most Popular, Printing Services
Read More URL: /services/acrylic-printing
```

**Service Showcase:**
```
Title: Graphic Design
Description: Professional design services for all your branding needs
Categories: Services, Design
Read More Text: Learn More →
```

**Portfolio Display:**
```
Title: Brand Identity Project
Description: Complete rebrand for tech startup including logo and guidelines
Categories: Portfolio, Branding
Background Color: #f5f5f5
Text Color: #1a1a1a
```

---

## 🎨 Category Filtering System

The plugin includes a powerful, accessible category filtering system.

### Setup Steps:

1. **Enable Category Tabs** - Check the box in Showcase Settings
2. **Define Tab Categories** - Enter: "All, Most Popular, Glass Printing, Cartons"
3. **Tag Items** - Add matching categories to each item

### How It Works:

- **"All" Category** - Shows all items
- **Specific Categories** - Shows only items with that exact category
- **Multi-Category Items** - Items appear in all their assigned categories
- **Empty Categories** - Automatically hidden if no items match
- **Case Sensitive** - Categories must match exactly

### Example Setup:

```
Display Categories: All, Most Popular, Glass Printing, Cartons

Item 1 - Categories: Most Popular, Glass Printing
Item 2 - Categories: Glass Printing
Item 3 - Categories: Most Popular, Cartons
Item 4 - Categories: Cartons

Result:
- "All" tab shows all 4 items
- "Most Popular" shows Items 1 & 3
- "Glass Printing" shows Items 1 & 2
- "Cartons" shows Items 3 & 4
```

### Keyboard Navigation:

- **Arrow Left/Right** - Navigate between tabs
- **Home** - Jump to first tab
- **End** - Jump to last tab
- **Enter/Space** - Activate selected tab
- **Tab** - Move to next focusable element

---

## 🛠️ Customization

### Custom Fonts

**Method 1: Admin Interface (Recommended)**

Enter font families in Showcase Settings:
```
Title Font: 'GT Pressura', sans-serif
Body Font: 'Sofia Pro Rounded Regular', sans-serif
```

Make sure fonts are loaded by your theme (via Google Fonts, Adobe Fonts, or custom CSS).

**Method 2: Custom CSS**

Leave font fields blank and add to your theme's stylesheet:

```css
.psc-item-title {
    font-family: 'GT Pressura', sans-serif;
}

.psc-item-description,
.psc-read-more {
    font-family: 'Sofia Pro Rounded Regular', sans-serif;
}
```

### Loading Google Fonts

Add to your theme's `functions.php`:

```php
function my_theme_enqueue_fonts() {
    wp_enqueue_style('google-fonts',
        'https://fonts.googleapis.com/css2?family=Your+Font:wght@300;400;700&display=swap'
    );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_fonts');
```

### Advanced CSS Customization

All styles use the `.psc-` prefix for easy overriding:

```css
/* Change card border radius */
.psc-item {
    border-radius: 30px;
}

/* Adjust spacing between cards */
.psc-showcase-grid {
    gap: 40px;
}

/* Customize hover shadow */
.psc-hover-lift .psc-item:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

/* Change image aspect ratio to 16:9 (widescreen) */
.psc-item-image {
    padding-bottom: 56.25%;
}

/* Adjust text truncation */
.psc-item-title {
    -webkit-line-clamp: 3; /* Show 3 lines instead of 2 */
}
```

---

## 📱 Responsive Behavior

- **Desktop (1024px+):** Shows configured items per row
- **Tablet (768px-1024px):** Auto-adjusts to 2 columns max
- **Mobile (below 768px):** Single column layout
- **Small Mobile (below 480px):** Optimized spacing and typography

---

## 🔧 Admin Interface Features

### Visual Improvements (New in 2.0!)
- **Item Numbering** - Easy reference with "Item 1", "Item 2", etc.
- **Visual Copy Button** - One-click shortcode copying with success feedback
- **Required Field Indicators** - Red asterisk (*) marks required fields
- **Improved Layout** - Cleaner, more organized settings interface
- **Better Visual Hierarchy** - Sectioned settings with clear headers

### Item Management
- **Drag Handle** - Click and drag the gray item header to reorder
- **Remove Button** - Red "Remove" button in each item header
- **Field Validation** - Items without image and title won't be saved
- **Auto-Numbering** - Numbers update automatically when reordered

---

## 🐛 Troubleshooting

### Images Not Displaying
1. Verify images exist in media library
2. Check file permissions (644 for files, 755 for directories)
3. Clear cache if using caching plugin
4. Check browser console (F12) for errors
5. Try regenerating thumbnails

### Shortcode Shows as Text
1. Ensure showcase is published (not draft)
2. Verify correct ID in shortcode
3. Check plugin is activated
4. Try re-saving the page

### Category Tabs Not Working
1. Verify "Enable Category Tabs" is checked
2. Ensure items have matching categories (case-sensitive)
3. Check JavaScript console for errors
4. Confirm jQuery is loaded

### Drag and Drop Issues
1. Clear browser cache
2. Disable conflicting plugins temporarily
3. Check for JavaScript errors in console
4. Try a different browser

### Copy Button Not Working
1. Check browser supports modern clipboard API
2. Verify HTTPS (required for clipboard API)
3. Falls back to legacy method automatically
4. Check JavaScript console for errors

---

## 🔒 Security Features (New in 2.0!)

- **Input Sanitization** - All user input properly sanitized
- **XSS Protection** - Safe HTML rendering throughout
- **Nonce Verification** - CSRF protection on all saves
- **Post Type Validation** - Prevents metadata injection
- **CSS Injection Prevention** - Font family sanitization
- **Field Validation** - Required fields enforced

---

## ⚡ Performance Features (New in 2.0!)

- **Lazy Loading** - Images load as they enter viewport
- **Responsive Images** - Automatic srcset generation for optimal delivery
- **Efficient CSS** - Minimal, well-organized stylesheets
- **Modern JavaScript** - Uses latest APIs with fallbacks
- **Caching Friendly** - Works with all major caching plugins

---

## ♿ Accessibility Features (New in 2.0!)

- **ARIA Labels** - Proper roles and labels throughout
- **Keyboard Navigation** - Full keyboard support for tabs
- **Screen Reader Support** - Semantic HTML and helpful labels
- **Focus Indicators** - Clear visual feedback for keyboard users
- **Reduced Motion** - Respects prefers-reduced-motion settings
- **Color Contrast** - Meets WCAG AA standards (when using defaults)

---

## 📚 Technical Details

### File Structure
```
product-showcase-cards/
├── product-showcase-cards.php  # Main plugin file
├── uninstall.php               # Cleanup on uninstall
├── css/
│   ├── admin.css              # Admin interface styles
│   └── frontend.css           # Public-facing styles
├── js/
│   ├── admin.js               # Admin functionality
│   └── frontend.js            # Category filtering
├── README.md                  # This file
└── CHANGELOG.md               # Version history
```

### Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)
- Internet Explorer: Not supported

### WordPress Compatibility
- **Minimum:** WordPress 5.8
- **Tested up to:** WordPress 6.7
- **PHP Required:** 7.4 or higher
- **Works with:** All major page builders (Elementor, Beaver Builder, Divi, WPBakery, SiteOrigin)

---

## 🆕 What's New in Version 2.0.0

This is a major release with significant improvements across security, user experience, accessibility, and performance.

### Security Enhancements
✅ Added post type validation to prevent metadata injection
✅ Fixed XSS vulnerability in admin image preview
✅ Enhanced font family sanitization to prevent CSS injection
✅ Improved input validation throughout

### User Experience Improvements
✅ Visual copy button with success feedback
✅ Required field indicators (red asterisk)
✅ Item numbering for easy reference
✅ Modern clipboard API with fallback support
✅ Cleaner admin interface with better organization

### Accessibility (WCAG 2.1 AA Compliant)
✅ ARIA labels and roles for screen readers
✅ Full keyboard navigation support
✅ Proper tab management and focus states
✅ Reduced motion support

### Performance Optimization
✅ Lazy loading for all images
✅ Responsive images with srcset
✅ Optimized CSS delivery
✅ Modern JavaScript APIs

### Code Quality
✅ Refactored default values into class constants
✅ Improved code organization and documentation
✅ Better error handling
✅ WordPress coding standards compliance

---

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for complete version history.

**Recent Versions:**

### 2.0.0 (Current)
- Major security, UX, and accessibility improvements
- Full details in CHANGELOG.md

### 1.1.0
- Added category filtering system
- Multi-category support
- Mobile-friendly tabs

### 1.0.0
- Initial release

---

## 🤝 Support

For issues, questions, or feature requests:
- **Email:** cliff@cordesprinting.com
- **Website:** https://cordesprinting.com

---

## 💡 Credits

**Developed by Cliff Cordes**
Cordes Printing - https://cordesprinting.com

**Version 2.0 Enhancements:**
Significant security, accessibility, and UX improvements via Claude Code

---

## 📄 License

This plugin is licensed under GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

Made with ❤️ for WordPress
