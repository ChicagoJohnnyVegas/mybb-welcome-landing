# Upgrade From v1.21 To v1.32

This is the complete guide to updating **Welcome Landing v1.21 to v1.32**. Installing for the first time? Use [Fresh Installation](README.md#fresh-installation) instead.

Follow these steps in order. **Do not uninstall the plugin.** Back up and prepare your customizations before uploading anything.

1. [Back up](#1-back-up).
2. [Prepare your customizations](#2-prepare-your-customizations).
3. [Deactivate and upload](#3-deactivate-and-upload).
4. [Reactivate and review](#4-reactivate-and-review).
5. [Test the update](#5-test-the-update).
6. [Remove old files](#6-remove-old-files).

A v1.21-to-v1.32 in-place upgrade was completed successfully on the maintainer's production forum. A hard refresh of the login page was needed to load the updated files. Test on a development copy before updating your live forum. See [testing limits](#compatibility-and-testing-limits) and [rollback](#rollback) for details.

## 1. Back Up

- Back up the deployed `inc/plugins/welcome_landing.php`, including direct code or configuration edits.
- Back up `inc/languages/english/welcome_landing.lang.php` and `inc/languages/english/admin/welcome_landing.lang.php`, including edits made through MyBB's language editor. Preserve plugin translations in other language directories.
- Back up the entire deployed `landing/` directory: custom CSS, JavaScript, dependencies, logos, fonts and backgrounds can all be affected. Include configured/custom asset locations outside this directory.
- Back up the database with **data and structure**. Include plugin settings and all `welcome_landing_*` Global Templates and theme-specific copies, with bodies and template-set ownership. A full database backup captures these; uploaded files need their own backup.
- Record the installed version and configured paths, image prefix/fallback and redirect/link settings. Keep backups outside the directories being overlaid or cleaned up, and retain them until verification is complete.

## 2. Prepare Your Customizations

Extract the new package into a separate preparation directory. Compare it with deployed files and backed-up database templates. Use the new release as the base, not the old customized files.

| Area | Required preparation |
| --- | --- |
| Both language files | Carry customized values into the new files while retaining new keys, including username/password, modal-close and CAPTCHA labels. Other-language files are not overlaid by this English package but may need new keys translated. |
| CSS and JavaScript | Identify edits in styles, enhancement scripts and vendor files. Adapt customizations to Bootstrap 5/current startup behavior; do not restore old Bootstrap or jQuery files over the new versions. |
| Backgrounds and custom assets | Preserve content, filenames and references. Exclude bundled images you do not want: matching filenames can overwrite your images, and new matching filenames can join the rotation. Confirm the configured prefix and fallback still resolve. Preserve custom logos/fonts and asset locations outside the package. |
| Plugin PHP | Compare edits to constants, allowed image types, guest allowances or other logic. Use ACP settings where available; reapply only necessary, reviewed changes to the new PHP. Do not replace new security/login logic with old customized code. |
| Templates | Plan reconciliation before activation. Uploading files does not replace database templates; reactivation transforms recognized markup. Custom Bootstrap 3 markup may need manual adaptation. Theme-specific overrides are retained rather than migrated automatically. Preserve native login/CAPTCHA fields, script ordering and current startup/accessibility behavior. |
| Settings | Existing setting values are preserved. Activation refreshes titles, descriptions and ordering, so direct database label edits can be replaced. Move customized wording into the prepared admin language file where appropriate. |

The new templates use Bootstrap 5.3.8 and jQuery 3.7.1. Bootstrap's native modal API is used with its jQuery bridge disabled so MyBB's CAPTCHA helpers retain their own modal implementation. Custom scripts/markup require review; arbitrary Bootstrap 3 customizations are not automatically compatible.

Backups alone do not merge customizations. Resolve conflicts on a development copy before uploading. Prepare file merges and a template reconciliation plan first, then review the resulting database templates after reactivation. Never restore old template bodies wholesale as an upgrade step.

## 3. Deactivate And Upload

With your backups and prepared customizations ready, go to `Configuration > Plugins` in Admin CP and **deactivate Welcome Landing before uploading any replacement files**. Deactivation keeps your settings and templates. **Do not uninstall.**

While the plugin is deactivated, its welcome page and guest redirects are unavailable. MyBB's own permissions still apply. If your private forum relies on those redirects to keep guests on the welcome page, temporarily close the board using MyBB's Board Online / Offline setting before deactivating. Keep your administrator session open so you can finish the update.

Leave the plugin deactivated until all uploads finish. Upload these files and directories from the package's `Upload/` folder to their matching locations beneath your MyBB forum root:

```text
inc/plugins/welcome_landing.php
inc/languages/english/welcome_landing.lang.php
inc/languages/english/admin/welcome_landing.lang.php
landing/css/
landing/js/
landing/LICENSE-bootstrap.txt
landing/LICENSE-jquery.txt
```

Include the current Bootstrap/jQuery dependencies, Bootstrap RTL stylesheet and enhancement script; replacing only PHP is not sufficient for this upgrade. Preserve custom files within these directories and merge edits to same-name files before replacement. Source maps may be included as supplied.

For `landing/img/landing/`, preserve your existing custom image set. Upload bundled images only if you deliberately want them, checking filename collisions and the configured prefix/fallback first. Do not delete or replace custom image directories as part of the code upload.

## 4. Reactivate And Review

After all uploads finish, go to `Configuration > Plugins` in Admin CP and confirm **Welcome Landing 1.32**. Click **Activate** to turn the plugin back on and update its templates. **Do not uninstall.**

Review the resulting Global Templates and adjust any theme-specific copies to work with the new version, using your backup to preserve customizations. Check your custom wording and the values under `Configuration > Settings > Welcome Landing`.

**Hard-refresh the welcome/login page before testing.** Your browser may still have the old styles and scripts cached, so an ordinary refresh may not be enough. Use your browser's reload-without-cache command or clear its cached files for the site, then reload the page.

Activation creates missing Global Templates and applies targeted, repeatable migrations to recognized markup without replacing entire customized bodies. Existing globals take precedence over legacy master-set copies; ambiguous duplicates remain for manual review. Theme-specific overrides are not automatically migrated.

The login form now submits to MyBB's native controller and requires the current CAPTCHA slots and script ordering. If custom templates cannot host a required CAPTCHA, the native MyBB login form remains available rather than hiding the challenge.

Missing templates use built-in defaults during rendering. Existing empty, whitespace-only or comment-only templates are respected; an intentionally empty shell can therefore produce a blank page.

Confirm MyBB's Board URL and the plugin paths. Redirect and asset/image URL settings remain forum-relative: use `/index.php` and `/landing`, not paths that repeat a forum subdirectory. Existing root-installation settings need no change. Custom welcome links must supply a forum-relative `url` value encoded once as a query parameter.

## 5. Test The Update

If you temporarily closed the board, confirm the plugin is active and the initial review above is complete before reopening it for guest testing. A closed board can prevent you from testing the normal guest experience.

Use a test account and recoverable email address; do not trigger lockouts on the only administrator account. Expected outcomes, with guests logged out unless stated otherwise:

- The welcome page loads and refreshes correctly with your copy, backgrounds and assets.
- Login and About open/close correctly on desktop/mobile; navigation, keyboard focus, Tab/Shift+Tab, Escape and focus return work.
- Valid credentials establish a session and return to the intended safe destination. Invalid credentials show MyBB errors with usable error focus.
- Required Default CAPTCHA is displayed/enforced and refresh works. Failed-attempt policy, account restrictions and persistent login/logout remain MyBB-owned.
- Complete password recovery: incorrect CAPTCHA is rejected; a valid submission sends email; the reset completes; subsequent login works; a consumed reset link is rejected.
- Enabled generic-login redirection works. Protected guest deep links retain their safe destination through failed and successful login; encoded queries survive and unsafe destinations fall back safely.
- Logged-in visitors are not trapped by guest gatekeeper redirects.
- The background alone fades after loading; login remains usable during the fade. Reduced-motion preferences disable the background animation.
- With JavaScript disabled or modal scripts blocked, inline login and recovery remain usable. Stalled or missing images never hide entry controls; stalled initialization exposes the inline fallback.
- The plugin name in Admin CP links to the GitHub repository; existing ACP settings and customized language/templates remain as intended.

Record pass, fail or not tested. Opening the recovery page alone does not verify recovery completion. Do not remove templates, change language/settings, create another installation layout or uninstall merely to satisfy ordinary smoke testing. Such compatibility tests need a separately prepared, backed-up environment.

## 6. Remove Old Files

Do this only after the update passes testing. Keep your backups until testing and cleanup are complete.

Cleanup is manual: upload, activation and uninstall do not delete server files. Keep your prior backups and check custom templates/styles/scripts for references before deleting anything. Resolve those references first.

Remove only these obsolete files, if present:

```text
landing/css/bootstrap-theme.css
landing/css/bootstrap-theme.min.css
landing/css/bootstrap-theme.css.map
landing/js/jquery.waitforimages.min.js
landing/fonts/glyphicons-halflings-regular.eot
landing/fonts/glyphicons-halflings-regular.svg
landing/fonts/glyphicons-halflings-regular.ttf
landing/fonts/glyphicons-halflings-regular.woff
```

Delete `landing/fonts` **only if empty**. Verify the full selected path before confirming deletion. Never recursively delete a nonempty folder. Do not delete `landing/`, `landing/img/`, unrelated fonts, custom assets or their directories. After cleanup, hard-refresh and recheck backgrounds, Login, About and mobile navigation. Apply the same sequence to production only after its upgrade passes testing.

## Rollback

If rollback is necessary, deactivate the plugin without uninstalling. Restore your backed-up **v1.21** plugin, language files and prior assets, together with the affected database templates/settings and their original template-set ownership. Avoid overwriting unrelated forum data when restoring. Reactivate the restored version, hard-refresh and check login/recovery.

Restoring PHP alone does not reverse database template migrations. Keep file and database backups together; a database backup does not restore uploaded assets.

## Compatibility And Testing Limits

Runtime testing used MyBB 1.8.40 / PHP 8.2.30 with Default image CAPTCHA at the website root. Subdirectory installations are intended to work but remain untested at runtime. Generic-login redirection is intended to operate independently of the gatekeeper, but the gatekeeper-disabled combination remains untested.

Missing-template fallback, alternate template-comment settings, legacy-template conflicts, theme-specific lifecycle behavior and uninstall have source/model checks but have not been runtime-tested on MyBB. Other CAPTCHA providers and arbitrary custom templates are unverified. Human screen-reader testing and a real RTL installation have not been performed; automated audits do not establish full accessibility conformance. The maintainer successfully upgraded production from v1.21 to v1.32; this does not verify every custom configuration.

Existing-configuration smoke tests and local browser fixtures do not establish these other cases. Local fixtures use mocked responses and do not execute PHP or authenticate MyBB users. Preserve these limitations when assessing production readiness.

## Uninstall Versus Deactivate

Deactivate turns the plugin off without removing plugin-owned settings or templates. It is the operation used during this upgrade.

Uninstall removes exact plugin setting names and template titles in global/legacy master sets. Theme-specific copies and unrelated settings are retained. A settings group containing unrelated settings is also retained, which can leave the plugin identified as installed pending manual cleanup. Uploaded files are not removed. **Do not uninstall to upgrade or roll back.**
