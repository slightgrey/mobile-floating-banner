# Mobile Floating Banner

A WordPress plugin that displays a floating pill-style call button fixed at the bottom of the page.

## Features

- Floating pill button with a circular phone icon on the left
- Clickable `tel:` link for one-tap calling on mobile
- Configurable background color, text color, icon circle color, and icon symbol color
- Content alignment: left, center, or right
- Device visibility controls: show on mobile, tablet, and/or desktop (mobile-only by default)
- Admin settings page under **Settings → Floating Banner**
- Live preview on the settings page reflecting saved colors and alignment

## Installation

1. Upload the `mobile-floating-banner` folder to `/wp-content/plugins/`
2. Activate the plugin via **Plugins** in the WordPress admin
3. Go to **Settings → Floating Banner** to configure

## Requirements

- WordPress 5.8+
- PHP 7.4+

## Settings

| Setting | Description |
|---|---|
| Display Text | Phone number or label shown on the pill |
| Phone Link | The `tel:` URL (e.g. `tel:+18005550100`) |
| Background Color | Pill background color |
| Pill Text Color | Color of the label text |
| Icon Circle Color | Background color of the circular icon |
| Icon Symbol Color | Color of the phone SVG icon |
| Content Alignment | Position the pill left, center, or right |
| Show on Mobile | Display on viewports < 768px (on by default) |
| Show on Tablet | Display on viewports 768px – 1024px |
| Show on Desktop | Display on viewports > 1024px |

## Breakpoints

| Device | Viewport |
|---|---|
| Mobile | < 768px |
| Tablet | 768px – 1024px |
| Desktop | > 1024px |

## License

GPL v2 or later — https://www.gnu.org/licenses/gpl-2.0.html
