# Upgrade To v1.36

This guide covers updating **Welcome Landing v1.32 to v1.36**. Still using **v1.21**? Follow the same steps with the [additional v1.21 requirements](#additional-requirements-for-v121). Installing for the first time? Use [Fresh Installation](README.md#fresh-installation).

Follow these steps in order. **Do not uninstall the plugin.** Prepare backups and customizations before uploading anything.

1. [Back up](#1-back-up).
2. [Prepare your customizations](#2-prepare-your-customizations).
3. [Deactivate and upload](#3-deactivate-and-upload).
4. [Reactivate and review](#4-reactivate-and-review).
5. [Test the update](#5-test-the-update).

The update from v1.32 to v1.36 replaces **three files**, adds image-selection settings and updates recognized page-shell templates. **No files or folders need deleting when upgrading from v1.32.** Test on a development copy before updating your live forum.

## 1. Back Up

- Back up the deployed `inc/plugins/welcome_landing.php`, including any direct code or configuration edits.
- Back up both plugin language files: `inc/languages/english/welcome_landing.lang.php` and `inc/languages/english/admin/welcome_landing.lang.php`. Include edits made through MyBB's language editor and any translations in other language directories. The public English language file is unchanged from v1.32 and need not be uploaded for that upgrade.
- Back up the deployed `landing/` directory, including customized scripts, styles and images. Include any configured asset locations outside that directory. The changed script is `landing/js/welcome-landing.js`.
- Back up the database with **data and structure**, including plugin settings and all `welcome_landing_*` Global Templates and theme-specific copies. A full database backup captures these; it does not back up uploaded files.
- Record the installed version and current plugin settings. Keep backups outside the folders being updated and retain them until verification is complete.

## 2. Prepare Your Customizations

Extract the package into a separate preparation folder.

**Upgrading from v1.32? You only need to merge files if BOTH are true:**

1. You have made customizations, either manually or through MyBB's language editor.
2. Those customizations affect at least one of the three files being replaced: `inc/plugins/welcome_landing.php`, `inc/languages/english/admin/welcome_landing.lang.php` or `landing/js/welcome-landing.js`.

**If you have not customized any of those three files, they are ready to upload as supplied.** Changes to your public language file, CSS or background images do not require merging because those files are not replaced in this update. Changing ordinary ACP setting values is not a file customization.

For affected files only, use the new file as the base and carry your customizations into it **before uploading**:

| Area | Preparation for an upgrade from v1.32 |
| --- | --- |
| Admin language | Carry your wording into the new admin language file while keeping the new image-mode descriptions, diagnostics and save-button labels. Add corresponding translations to your other admin languages as needed. |
| JavaScript | Merge edits to `landing/js/welcome-landing.js` into the new script. Retain the image selector, background loading and login behavior. CSS and bundled libraries are unchanged from v1.32. |
| Plugin PHP | Compare edits to constants, allowed image types, guest allowances or other logic. Reapply only necessary changes to the new PHP; use existing ACP settings where possible. |

**Separate template check:** if you customized `welcome_landing_page_shell` in the database, including a theme-specific copy, review its image-selection code against the new default before activation. This is separate from the three file uploads: activation updates recognized template blocks, but heavily customized blocks and theme-specific overrides may need manual alignment. Unmodified templates need no manual preparation. Custom selection code must use `welcomeLandingSelectImage` with `{$imageSelectionJson}` to support all modes; preserve surrounding markup and login/CAPTCHA fields rather than replacing the entire template.

Keep your public language file, CSS and image collection in place. Existing setting values are preserved; the new mode defaults to Shuffle. Activation refreshes labels/descriptions and mode choices, so direct database label customizations should be carried into the admin language file if you want to retain them. Backups alone do not merge customizations.

**Coming from v1.21?** Also complete the [additional customization preparation](#additional-requirements-for-v121) before proceeding.

## 3. Deactivate And Upload

With backups and prepared files ready, go to `Configuration > Plugins` in Admin CP and **deactivate Welcome Landing before uploading replacements**. Deactivation keeps settings and templates. **Do not uninstall.**

The welcome page and plugin guest redirects are unavailable while deactivated. MyBB's own permissions still apply. If your private forum relies on those redirects to keep guests on the welcome page, temporarily close the board using MyBB's Board Online / Offline setting first. Keep your administrator session open.

For **v1.32**, upload only these prepared files from the package's `Upload/` folder to the matching locations beneath your MyBB forum root:

```text
inc/plugins/welcome_landing.php
inc/languages/english/admin/welcome_landing.lang.php
landing/js/welcome-landing.js
```

Leave the plugin deactivated until all uploads finish. No public-language, CSS, dependency or image replacement is required for v1.32. Do not copy the whole `Upload/` directory over your customized installation when only these three files are needed.

For **v1.21**, use the [expanded upload list](#additional-requirements-for-v121) instead.

## 4. Reactivate And Review

In `Configuration > Plugins`, confirm **Welcome Landing 1.36** and click **Activate**. Reactivation is required for this upgrade even though the new PHP is already present: it adds the image-selection setting and updates recognized template code.

- Review your settings and custom wording under `Configuration > Settings > Welcome Landing`.
- Choose **Image selection mode**. **Shuffle** is the new default; choose **Random** to keep v1.32's independent-random behavior. Sequential follows filename order; Daily uses the forum timezone and daylight-saving setting.
- Review the migrated Global Template `welcome_landing_page_shell` and reconcile any customized or theme-specific copies with the new image-selection code. Activation does not overwrite whole customized templates or automatically migrate theme-specific copies.
- Check **Background Image Status** beneath the settings. The image count and paths should match your collection. **Open image (new tab)** checks a sample manually; a readable filesystem folder does not prove the URL works. Save and stay returns to updated diagnostics; Save and exit returns to the settings list.
- **Hard-refresh the welcome/login page before testing** to load the new JavaScript. Use your browser's reload-without-cache command or clear its cached files for the site. Background-image cache refresh does not refresh JavaScript.

Settings paths are relative to the forum root: for a forum at `https://example.com/forum`, use `/landing`, not `/forum/landing`. Keep MyBB's Board URL correct. If custom templates still select images with their own random code, choosing another mode in ACP will not change that behavior until the template is reconciled.

## 5. Test The Update

If you closed the board, confirm the plugin is active and the initial review is complete before reopening for guest testing. Use a test account and a recoverable email address, not the only administrator account for failed-login checks.

- Confirm the welcome page displays your wording and backgrounds on desktop and mobile. Login, About and password-recovery access should remain usable.
- Verify a successful login and an invalid-password response. Check a protected deep link returns to the intended page after login, and logout restores the guest experience. Existing CAPTCHA and account restrictions remain MyBB-owned.
- Verify password recovery and CAPTCHA refresh on your existing configuration. When checking complete recovery, confirm email delivery, reset completion and login with the new password; simply opening the recovery page is only a smoke test.
- Check Shuffle over a full collection cycle, Random with repeats allowed, and Sequential in filename order. With Daily, browsers should show the same image for the current forum day. If you do not observe a day boundary, do not count that transition as tested. Restore your preferred mode afterward.
- For image refresh, back up one matching image and replace it under the same name with a visibly different image whose modification time or size changes. Confirm the new version appears when that image is selected, without clearing the browser cache. Random modes may not select it immediately. Restore the original image afterward.
- Confirm Background Image Status reflects saved settings. Check Open image and both save buttons. A missing fallback can produce a notice even when matching backgrounds work; correct unexpected notices rather than deleting your collection.
- Confirm your customized files, existing settings and template content remain intact.

Record pass, fail or not tested. Do not remove templates, change permissions, uninstall or create a different installation layout just for ordinary smoke testing. No cleanup is required from v1.32. For v1.21, perform the specific cleanup below only after the upgrade passes.

## Additional Requirements For v1.21

Skip this section when upgrading from v1.32. It supplements the steps above for a **direct v1.21-to-v1.36 update**; an intermediate installation is not required.

### Before Uploading

The older release also requires new public language, CSS, JavaScript dependencies and login templates. Prepare these customizations in addition to the main checklist:

- Merge customized public text into the new `inc/languages/english/welcome_landing.lang.php` while retaining new username/password, close-button and CAPTCHA labels. Update translations as needed.
- Adapt customized Bootstrap 3 markup/styles/scripts to Bootstrap 5.3.8 and jQuery 3.7.1. Do not restore old dependencies over the new files. The familiar appearance is retained as closely as practical, not guaranteed pixel-identical.
- Review all customized `welcome_landing_*` templates against the new defaults, not only the page shell. Preserve custom content while adopting native MyBB login submission, CAPTCHA slots, script ordering and accessibility features. Theme-specific overrides need manual reconciliation.
- Back up any custom fonts, logos and asset locations outside `landing/`. Identify references to the obsolete files listed below before considering removal.

### Upload List

After completing preparation and deactivating as described in step 3, upload these prepared paths instead of the three-file v1.32 list:

```text
inc/plugins/welcome_landing.php
inc/languages/english/welcome_landing.lang.php
inc/languages/english/admin/welcome_landing.lang.php
landing/css/
landing/js/
landing/LICENSE-bootstrap.txt
landing/LICENSE-jquery.txt
```

Preserve custom files within these directories and merge same-name changes before replacement. Keep your background collection; bundled images are optional for an existing installation. Continue with reactivation and testing in steps 4 and 5. Native MyBB login remains available when a customized welcome form cannot host a required CAPTCHA.

### Cleanup After Successful Testing

Cleanup is manual; the plugin does not delete uploaded files. Check your custom templates/styles/scripts for references and resolve them before removing only these obsolete files, if present:

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

Delete `landing/fonts` **only if empty**, after verifying the full selected path. Never recursively delete a nonempty folder. Do not delete `landing/`, `landing/img/`, unrelated fonts, custom assets or their directories. Hard-refresh and recheck backgrounds, Login, About and mobile navigation afterward.

## Rollback

If needed, deactivate without uninstalling. Restore the backed-up files for the version you started with, together with the affected database templates/settings and their original template-set ownership. Avoid overwriting unrelated forum data when restoring a database backup. Reactivate the restored version, hard-refresh and check login/recovery.

For v1.32, restore the previous plugin PHP, admin language and `welcome-landing.js`, plus the affected templates/settings. For v1.21, also restore its public language and prior assets. Restoring PHP alone does not reverse template migrations; file and database backups belong together.

## Compatibility And Testing Limits

Runtime testing uses MyBB 1.8.40 / PHP 8.2.30 with Default image CAPTCHA at the website root. Subdirectory installations are intended to work but remain unverified at runtime. Generic-login redirection with the guest gatekeeper disabled also remains unverified.

Other CAPTCHA providers, arbitrary customized themes/templates, legacy-template conflicts, theme-specific lifecycle behavior, missing-template fallback, alternate template-comment settings and uninstall retain the limitations described in [README.md](README.md#compatibility-notes). Human screen-reader testing and an actual RTL forum installation remain unperformed. Local source/model/browser fixtures do not execute MyBB authentication or prove every server configuration.

Image diagnostics check saved configuration and the server filesystem, not HTTP delivery or image contents. Filesystem-permission failures, alternative ACP themes and interactions with other plugins are not exhaustively tested. Shuffle/Sequential persistence depends on browser storage; multiple tabs are not coordinated. Image-cache markers cannot detect replacements preserving both modification time and size, or force caches that ignore those markers to refresh.

## Uninstall Versus Deactivate

Deactivate turns the plugin off without removing plugin-owned settings or templates. Use it during upgrades and rollback.

Uninstall removes exact plugin setting names and template titles in global/legacy master sets. Theme-specific copies and unrelated settings are retained. A settings group containing unrelated settings is also retained, which can leave the plugin identified as installed pending manual cleanup. Uploaded files are not removed. **Do not uninstall to upgrade or roll back.**
