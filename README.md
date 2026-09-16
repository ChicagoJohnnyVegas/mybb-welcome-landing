# Welcome Landing

Welcome Landing is a MyBB 1.8.x plugin that replaces the stock guest login entry point with a standalone, branded landing page. It provides a full-screen user-specified random background, modal login form, optional guest redirects, lost-password/CAPTCHA compatibility and Admin CP settings for common operational behavior.

The landing page is intentionally standalone. It does not use the normal MyBB header and footer by default because the purpose of the plugin is to provide a distinct entry experience for private or member-only communities.

## Features

- Standalone welcome page at `misc.php?action=welcome`.
- Modal login form using normal MyBB authentication and cookies.
- Optional redirect from `index.php` for guests.
- Optional guest gatekeeper for protected forum pages.
- Safe redirect handling for guest deep links.
- Generic MyBB login route redirect for `member.php?action=login`.
- Lost-password and CAPTCHA route compatibility.
- Rotating full-screen background images.
- Admin CP settings group for operational options.
- Language files for public copy and Admin CP copy.
- Plugin-managed Global Templates for landing markup.

## Requirements

- MyBB 1.8.x.
- A browser-accessible `/landing/` asset directory under the MyBB document root.
- FTP, SFTP or hosting-panel access to upload files to the MyBB document root.

## Package Layout

Upload these paths to the matching locations beneath the MyBB document root:

- `inc/plugins/welcome_landing.php`
- `inc/languages/english/welcome_landing.lang.php`
- `inc/languages/english/admin/welcome_landing.lang.php`
- `landing/css/...`
- `landing/js/...`
- `landing/img/landing/...`
- `landing/fonts/...`

The `landing/` directory belongs at the MyBB document root, beside `inc/`, not inside `inc/`.

## Installation

1. Copy the contents of `Upload/` to your MyBB document root.
2. In MyBB Admin CP, go to `Configuration > Plugins`.
3. Find `Welcome Landing` and click `Install & Activate`.
4. Go to `Configuration > Settings > Welcome Landing`.
5. Review the settings and adjust paths or redirects for your site.
6. Visit `misc.php?action=welcome` as a guest to confirm the landing page loads.

## Updating

1. Back up any customized language files, Global Templates and landing assets.
2. Copy the new `Upload/` contents over the existing files, but do not overwrite `landing/img/landing/` if you have already installed your own background images.
3. In Admin CP, confirm the plugin shows the expected version.
4. If MyBB marks the plugin inactive after an update, activate it again.
5. Visit the `Welcome Landing` settings group and confirm your existing setting values were preserved.
6. Test the landing page, login flow, lost-password page and CAPTCHA image.

Package uploads may overwrite language files and bundled assets. If you customize text through language files or replace bundled images, keep your own backup before updating. See `UPGRADE.md` for a fuller update checklist.

## Configuration

Welcome Landing creates a `Welcome Landing` settings group in MyBB Admin CP.

Key settings:

- `Enable index guest redirect`: redirects guests from `index.php` to the landing page.
- `Enable guest gatekeeper`: redirects guests from protected forum pages to the landing page and preserves the requested path when safe.
- `Redirect generic MyBB login`: redirects `member.php?action=login` to the landing page for guests.
- `Logged-in redirect path`: where logged-in users go after visiting the landing page directly.
- `Asset URL path`: URL path for bundled CSS and JavaScript, normally `/landing`.
- `Image URL path`: URL path for rotating background images, normally `/landing/img/landing`.
- `Image filesystem path`: filesystem path relative to the forum root for background images, normally `/landing/img/landing`.
- `Image filename prefix`: only image filenames beginning with this prefix are used. The bundled default is `WL`.
- `Fallback image filename`: image used when no matching rotating images are found. The bundled default is `WL1.jpg`.
- `Show forgot-password link`: shows or hides the lost-password link.
- `Show about modal`: shows or hides the About link and modal.

Use site-relative paths such as `/index.php` for redirect settings. Do not use full external URLs.

## Customization Model

Welcome Landing is designed to be customized without editing MyBB core.

- Settings control operational behavior such as redirects, paths and feature toggles.
- Public language files control labels, button text, login messages and About modal copy.
- Admin language files control plugin metadata and Admin CP setting text.
- Global Templates control landing-page markup.
- Assets control CSS, JavaScript, fonts and background images.

### Public Text

Edit public-facing text in:

- `inc/languages/english/welcome_landing.lang.php`

This includes the page title, buttons, placeholders, login errors and About modal body.

### Admin CP Text

Edit Admin CP-facing text in:

- `inc/languages/english/admin/welcome_landing.lang.php`

This includes the plugin description and setting names/descriptions.

### Templates

Welcome Landing installs plugin-managed templates as MyBB Global Templates. These can be edited from Admin CP under `Templates & Style > Templates > Global Templates`.

The plugin creates missing templates on install or activation, but it should not overwrite customized template bodies during ordinary activation.

### Background Images

Welcome Landing displays rotating images as full-screen CSS backgrounds using `background-size: cover`. Images are centered and cropped as needed to fill the visitor's browser window without distortion.

Recommended image guidance:

- Use landscape images with a `16:9` aspect ratio.
- Preferred dimensions are `1920x1080`.
- Minimum practical dimensions are `1600x900`.
- `2560x1440` is a good high-detail option if file size remains reasonable.
- Use JPG or WebP for photographic backgrounds.
- Keep each image ideally under `500 KB` and preferably under `1 MB`.
- Keep important visual content near the center because screen edges may be cropped on narrow or unusually wide viewports.
- Avoid important text in background images unless it is centered and nonessential.

Image filenames must begin with the configured `Image filename prefix` setting and use an allowed image extension such as `.jpg`, `.jpeg`, `.png`, `.gif`, `.webp` or `.bmp`.

The bundled distribution images use:

- Prefix: `WL`
- Fallback image: `WL1.jpg`

## Guest Redirect Behavior

The `Enable index guest redirect`, `Enable guest gatekeeper` and `Logged-in redirect path` settings affect different situations.

- `Enable index guest redirect` controls whether guests visiting the forum index are sent to the landing page.
- `Enable guest gatekeeper` controls whether guests following protected forum links are sent to the landing page first.
- `Logged-in redirect path` controls where a logged-in user is sent when they visit the landing page directly or when no safe deep-link target is available.

For private forums, the gatekeeper is usually the setting that preserves a guest's intended destination after login.

## Uninstall Behavior

Deactivate turns the plugin off without removing plugin-owned settings or templates.

Uninstall removes plugin-owned settings and plugin-owned templates. It does not remove uploaded files from the filesystem, including files under `landing/`.

Back up customized templates, language files and assets before uninstalling if you may want to reuse them.

## Troubleshooting

- If Admin CP shows an old version, confirm the uploaded server file is the current file.
- If plugin changes do not appear to run, clear or restart any server-side PHP cache.
- If the landing page has no background image, confirm the image path settings, image filename prefix and fallback filename.
- If old background images still appear after replacement, test the image URL in a private browser window or rename the replacement image to avoid browser cache.
- If CAPTCHA images break, inspect the browser Network response for redirects or HTML returned from `captcha.php`.
- If login redirects look wrong, confirm `Logged-in redirect path` is a site-relative path such as `/index.php`.
- If a guest deep link does not return to the expected page after login, confirm `Enable guest gatekeeper` is enabled.
- If templates do not appear in Admin CP, deactivate and reactivate the plugin to recreate missing plugin templates.

## Support

Welcome Landing is provided as-is. Please test on a development copy of your forum before installing or updating on production.

The plugin was originally written to solve a specific private-forum login need and is shared as open-source software. Future changes are expected to be limited to bug fixes or functionality the maintainer chooses to add.

## License

Welcome Landing is released under the MIT License. See `LICENSE` for details.

See `CHANGELOG.md` for release history and `THIRD_PARTY_NOTICES.md` for bundled third-party library notices.

## Compatibility Notes

Welcome Landing targets MyBB 1.8.x and declares compatibility with `18*`.

The plugin depends on standard MyBB guest routes for login, lost-password recovery and CAPTCHA generation. Sites with aggressive custom rewrites, unusual cookie domains or other login/redirect plugins should test the guest flow carefully on a development copy before production use.

Do not edit MyBB core files for this plugin.
