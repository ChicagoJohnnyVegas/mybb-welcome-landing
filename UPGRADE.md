# Upgrade Notes

Use these notes when updating an existing Welcome Landing installation.

## Before Updating

- Back up `inc/languages/english/welcome_landing.lang.php` if you customized public text through the language file or MyBB language editor.
- Back up `inc/languages/english/admin/welcome_landing.lang.php` if you customized Admin CP text.
- Back up customized Global Templates whose names begin with `welcome_landing_`.
- Back up `landing/img/landing/` if you replaced the bundled background images with your own.
- Test the update on a development copy of your forum before updating production.

## Uploading Files

Release packages use an `Upload/` folder. Copy the contents of `Upload/` to the root of your MyBB forum.

Do not overwrite `landing/img/landing/` if you already installed your own background images and want to keep them.

Package uploads may overwrite bundled language files and assets. Keep backups of local customizations before uploading.

## After Uploading

1. In Admin CP, go to `Configuration > Plugins`.
2. Confirm `Welcome Landing` shows the expected version.
3. If MyBB marks the plugin inactive after the update, activate it again.
4. Visit `Configuration > Settings > Welcome Landing` and confirm existing setting values were preserved.
5. If new plugin templates were added, activation should create missing templates without overwriting customized template bodies.

## Smoke Test

After updating, test as a guest:

- `misc.php?action=welcome` loads.
- Login modal opens.
- Valid login succeeds.
- Lost-password page loads.
- CAPTCHA image and CAPTCHA refresh work.
- `member.php?action=login` redirects to the landing page when that setting is enabled.
- Protected guest deep links return to the intended safe destination after login when the gatekeeper is enabled.
- Landing CSS, JavaScript and background images load.

## Uninstall Versus Deactivate

Deactivate turns the plugin off without removing plugin-owned settings or templates.

Uninstall removes plugin-owned settings and plugin-owned templates. It does not remove uploaded files from the filesystem, including files under `landing/`.