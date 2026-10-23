# Welcome Landing

Welcome Landing is a MyBB 1.8.x plugin that replaces the stock guest login entry point with a standalone, branded landing page. It provides a full-screen rotating background, modal login form, optional guest redirects, lost-password/CAPTCHA compatibility and Admin CP settings for common operational behavior.

The landing page is intentionally standalone. It does not use the normal MyBB header and footer by default because the purpose of the plugin is to provide a distinct entry experience for private or member-only communities.

Login and recovery no longer wait for background images to load. The background alone fades in over two seconds after loading (without animation when reduced motion is requested). With the required scripts available, login uses the normal modal; otherwise the form remains available inline. Native MyBB validation and CAPTCHA requirements still apply.

## Features

- Standalone welcome page at `misc.php?action=welcome`.
- Modal login form using normal MyBB authentication and cookies.
- Optional redirect from `index.php` for guests.
- Optional guest gatekeeper for protected forum pages.
- Safe redirect handling for guest deep links.
- Generic MyBB login route redirect for `member.php?action=login`.
- Lost-password and CAPTCHA route compatibility.
- Full-screen background images with Shuffle, Random, Sequential and Daily selection modes.
- Automatic background-image cache refresh after ordinary same-name image replacements.
- Read-only Admin CP image diagnostics and a manual image link.
- Admin CP settings group for operational options.
- Language files for public copy and Admin CP copy.
- Plugin-managed Global Templates for landing markup.

## Requirements

- MyBB 1.8.x.
- A browser-accessible `/landing/` asset directory under the MyBB document root.
- FTP, SFTP or hosting-panel access to upload files to the MyBB document root.

## Updating vs. Fresh Install

Already using Welcome Landing? Follow the [upgrade guide for v1.36](UPGRADE.md) **before uploading any files**. It covers the three-file update from v1.32, protecting customizations, reactivation and testing, with separate additional steps for v1.21. **Do not uninstall to upgrade.**

Installing for the first time? Follow [Fresh Installation](#fresh-installation) below instead.

## Package Layout

The package contains these paths, matching locations beneath the MyBB document root:

- `inc/plugins/welcome_landing.php`
- `inc/languages/english/welcome_landing.lang.php`
- `inc/languages/english/admin/welcome_landing.lang.php`
- `landing/css/...`
- `landing/js/...`
- `landing/img/landing/...`
- `landing/LICENSE-bootstrap.txt` and `landing/LICENSE-jquery.txt`

The `landing/` directory belongs at the MyBB document root, beside `inc/`, not inside `inc/`.

## Fresh Installation

Use this section only when Welcome Landing is not already installed. Do not delete existing site folders; a fresh install requires no legacy cleanup.

1. Copy the contents of `Upload/` to your MyBB document root.
2. In MyBB Admin CP, go to `Configuration > Plugins`.
3. Find `Welcome Landing` and click `Install & Activate`.
4. Go to `Configuration > Settings > Welcome Landing`.
5. Review the settings and adjust paths or redirects for your site.
6. Visit `misc.php?action=welcome` as a guest to confirm the landing page loads.

## Configuration

Welcome Landing creates a `Welcome Landing` settings group in MyBB Admin CP.

Key settings:

- `Enable index guest redirect`: redirects guests from `index.php` to the landing page.
- `Enable guest gatekeeper`: redirects guests from protected forum pages to the landing page and preserves the requested path when safe.
- `Redirect generic MyBB login`: redirects guest GET requests for `member.php?action=login` to the landing page, independently of the gatekeeper and index-redirect settings. Safe forum-relative `url` destinations are preserved.
- `Logged-in redirect path`: where logged-in users go after visiting the landing page directly.
- `Asset URL path`: URL path for bundled CSS and JavaScript, normally `/landing`.
- `Image URL path`: URL path for rotating background images, normally `/landing/img/landing`.
- `Image filesystem path`: filesystem path relative to the forum root for background images, normally `/landing/img/landing`.
- `Image filename prefix`: only image filenames beginning with this prefix are used. The bundled default is `WL`.
- `Image selection mode`: chooses Shuffle (the default), Random, Sequential or Daily. See [Image Selection Modes](#image-selection-modes).
- `Fallback image filename`: image used when no matching rotating images are found. The bundled default is `WL1.jpg`.
- `Show forgot-password link`: shows or hides the lost-password link.
- `Show about modal`: shows or hides the About link and modal.

Redirect and asset/image URL settings are relative to the forum root, even though they begin with `/`. Use `/index.php` and `/landing`, not full URLs or paths that repeat the forum subdirectory. For a forum at `https://example.com/forum`, these settings produce `https://example.com/forum/index.php` and `https://example.com/forum/landing`. The image filesystem path remains relative to the forum installation directory.

The welcome page's optional `url` parameter also uses a forum-relative path, encoded once as a query parameter. Guest deep links captured by the gatekeeper are converted automatically from website-root request paths. Encoded destination query values are preserved; unsafe destinations fall back to the configured logged-in path.

## Customization Model

Welcome Landing is designed to be customized without editing MyBB core.

- Settings control operational behavior such as redirects, paths and feature toggles.
- Public language files control labels, button text and About modal copy. Native login errors use MyBB's language strings.
- Admin language files control plugin metadata and Admin CP setting text.
- Global Templates control landing-page markup.
- Assets control CSS, JavaScript and background images; no fonts are bundled.

### Public Text

Edit public-facing text in:

- `inc/languages/english/welcome_landing.lang.php`

This includes the page title, buttons, placeholders and About modal body. Login validation errors are supplied by MyBB.

### Admin CP Text

Edit Admin CP-facing text in:

- `inc/languages/english/admin/welcome_landing.lang.php`

This includes the plugin description, setting names/descriptions, image diagnostics and save-button labels.

### Templates

Welcome Landing installs plugin-managed templates as MyBB Global Templates. These can be edited from Admin CP under `Templates & Style > Templates > Global Templates`.

The default templates use Bootstrap 5.3.8 and jQuery 3.7.1 while preserving the standalone presentation. They provide programmatic input labels, named modals/close buttons and keyboard focus handling. Document language/direction follows MyBB's active language metadata, using Bootstrap's RTL stylesheet when appropriate. Preserve these features when customizing markup.

Login uses a modal when the required scripts initialize successfully, with an inline form available if JavaScript is disabled or initialization fails or stalls. Login/recovery do not depend on background-image loading.

The plugin creates missing Global Templates on install or activation and applies targeted, repeatable migrations to recognized markup; it does not replace entire customized bodies. Theme-specific templates are left unchanged and may need manual alignment with updated defaults.

An existing Global Template takes precedence over a legacy plugin template in the master set (`sid = -2`). When no global exists, a single legacy copy is moved to Global Templates. Ambiguous duplicate copies are preserved for manual review, not merged or deleted.

At render time, missing templates use built-in defaults. Existing templates, including intentionally empty, whitespace-only or comment-only templates, are respected. An intentionally empty page-shell template can therefore produce a blank landing page; empty is not treated as missing.

Custom Bootstrap 3 templates/scripts require review before upgrading from v1.21. Preserve your customizations while adapting them to the current defaults; appearance is not guaranteed pixel-identical. Customized page-shell templates also need the current image-selection code to support all four modes. See [UPGRADE.md](UPGRADE.md) for backups, exact uploads, reactivation, template review, any applicable cleanup and rollback. [CHANGELOG.md](CHANGELOG.md) summarizes each public-release update.

Testing limits remain: other CAPTCHA providers and arbitrary custom templates are not verified by the Default CAPTCHA browser fixtures. Human screen-reader testing and a real RTL MyBB installation have not been performed. Legacy-template conflicts, theme-specific lifecycle behavior, uninstall, missing-template fallback and alternate template-comment settings have not been runtime-tested on MyBB; source/model checks and ordinary activation smoke tests do not establish those cases. Use a separately prepared disposable installation for such testing.

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

### Image Selection Modes

Choose a mode under `Configuration > Settings > Welcome Landing`:

- **Shuffle:** shows every matching image once before starting another shuffled cycle. This is the default. The last image of one cycle can also be the first of the next.
- **Random:** picks independently on every page load; repeats are possible. This matches the background-selection behavior of v1.32.
- **Sequential:** follows filename order, then starts again.
- **Daily:** shows everyone the same image for the day, using the forum timezone and daylight-saving setting rather than each visitor's timezone.

Shuffle and Sequential remember progress in that browser profile for that forum. A hard refresh advances the cycle; clearing site storage resets it. If browser storage is unavailable, images still display, but progress cannot be reliably remembered. Multiple tabs are not coordinated. Changing the image collection or switching between cycling modes can reset progress; replacing an image under the same filename does not. Daily selection can change if the collection changes.

### Replacing Background Images

When an image's modification time or file size changes, Welcome Landing changes its image URL marker so browsers can request the replacement. The filename can stay the same. This applies to discovered images and the fallback image, without reading or hashing image contents.

A replacement that preserves both modification time and size cannot be detected this way. Missing file metadata leaves the original URL unchanged. Caches that ignore URL query parameters or serve stale page HTML can also delay an update. This feature does not refresh cached CSS or JavaScript.

### Background Image Status

The **Background Image Status** table appears beneath the Welcome Landing settings form. It shows the saved filesystem folder, URL path, filename prefix, matching-image count, fallback availability and selection mode. Notices appear in bold red.

The filesystem path tells PHP where to find files; the URL path tells a visitor's browser where to request them. Both normally use `/landing/img/landing`, but they serve different purposes. Filesystem checks cannot prove that the URL works or that an image file is valid. **Browser access** remains **Not checked**; use **Open image (new tab)** to verify a sample yourself. If no matching image is found, the link uses the fallback when that file is available.

**Save and stay** saves through MyBB and returns to refreshed diagnostics. **Save and exit** saves and returns to the settings list; it does not log you out. The table reads saved database values, not unsaved edits. It runs when you view the settings page, not continuously or on a timer. No files are changed or repaired by the diagnostics.

## Guest Redirect Behavior

The `Enable index guest redirect`, `Enable guest gatekeeper` and `Logged-in redirect path` settings affect different situations.

- `Enable index guest redirect` controls whether guests visiting the forum index are sent to the landing page.
- `Enable guest gatekeeper` controls whether guests following protected forum links are sent to the landing page first.
- `Logged-in redirect path` controls where a logged-in user is sent when they visit the landing page directly or when no safe deep-link target is available.

For private forums, the gatekeeper is usually the setting that preserves a guest's intended destination after login.

## Uninstall Behavior

Deactivate turns the plugin off without removing plugin-owned settings or templates.

Uninstall removes exact plugin setting names and template titles in the global/legacy master sets. Theme-specific template copies and unrelated settings are retained. A settings group containing unrelated settings is also retained, which can leave the plugin identified as installed pending manual cleanup. See `UPGRADE.md` for details. Uninstall does not remove uploaded files, including files under `landing/`, and has not been runtime-tested on MyBB.

Back up customized templates, language files and assets before uninstalling if you may want to reuse them.

## Troubleshooting

- If Admin CP shows an old version, confirm the uploaded server file is the current file.
- If plugin changes do not appear to run, clear or restart any server-side PHP cache.
- If the landing page has no background image, review Background Image Status in ACP and use Open image to check browser delivery. A valid filesystem path does not establish a valid URL path.
- If old background images still appear after replacement, check that the file's modification time or size changed and that any proxy/CDN respects the URL marker. Renaming the image is another option; update the fallback setting if it names that file.
- If the selected image mode seems to be ignored, hard-refresh the welcome page and review customized page-shell templates against the current defaults after following the upgrade guide.
- If CAPTCHA images break, inspect the browser Network response for redirects or HTML returned from `captcha.php`.
- If login redirects look wrong, confirm `Logged-in redirect path` is forum-relative, such as `/index.php`, and MyBB's Board URL includes the forum subdirectory when applicable.
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

Runtime testing to date has used MyBB 1.8.40 with PHP 8.2.30, with the forum installed at the website root. Subdirectory installations (for example, `example.com/forum/`) are intended to work but have not been runtime-tested. Test on a development copy before deploying to production; source-level path checks are not a substitute for MyBB runtime testing.

MyBB remains responsible for authentication, session/cookie handling, account restrictions and password recovery. Welcome Landing integrates with those mechanisms; it is not a replacement permission system or a guarantee of compatibility with every authentication plugin. Testing used MyBB Default image CAPTCHA. Other CAPTCHA providers, arbitrary custom templates/themes and unusual cookie/rewrite configurations remain unverified.

Missing-template fallback and alternate template-comment settings have source/model coverage but have not been runtime-tested. Legacy-template conflicts, theme-specific lifecycle behavior and uninstall are likewise unverified on MyBB. Human screen-reader testing and a real RTL installation remain unperformed; automated accessibility checks do not establish full conformance. Existing-configuration smoke tests do not establish these other scenarios.

The plugin depends on standard MyBB guest routes for login, lost-password recovery and CAPTCHA generation. Sites with aggressive custom rewrites, unusual cookie domains or other login/redirect plugins should test the guest flow carefully on a development copy before production use.

Generic-login redirection is intended to work independently of the guest gatekeeper. Behavior with the gatekeeper disabled has not been runtime-tested. Test this configuration on a development copy before production use; source/model checks do not establish MyBB runtime behavior.

Do not edit MyBB core files for this plugin.
