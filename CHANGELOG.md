# Changelog

This file summarizes the changes from the public v1.21 release to v1.32.

## 1.32 - Maintenance Release

This update improves sign-in, password recovery and the welcome page while keeping its familiar standalone design.

### Improvements For Members

- **More consistent sign-in protection.** MyBB now handles sign-in directly, including image verification, limits on failed attempts and staying signed in. The welcome page still provides the login form and displays errors. When a customized page cannot display required verification, MyBB's standard login page is used instead.
- **Password recovery works through the guest restrictions.** Fixes a problem that could block members from submitting a forgotten-password request.
- **Better return to the page a member wanted.** Improves handling of forum links after login, including links with extra information in their address, while rejecting unsafe destinations.
- **No waiting for background pictures to sign in.** Login and password recovery stay available even when an image loads slowly or fails. If the usual login window cannot open, the form remains available directly on the page.
- **Easier keyboard use and clearer controls.** Improves form labels, keyboard navigation, error messages and contrast. Only the background fades in; animation is disabled for visitors who request reduced motion in their device settings.
- **Better language support.** The page follows the forum's language and text direction, including support for right-to-left layouts.

### Improvements For Forum Owners

- **More flexible guest settings.** Sending visitors from MyBB's standard login page to the welcome page no longer depends on the other guest-redirect settings.
- **More careful handling of customizations.** Better protects settings and theme-specific templates during activation and removal. If a page template is missing, the plugin can use its built-in version without treating a deliberately blank template as missing.
- **Updated page components.** Includes newer Bootstrap and jQuery libraries. Customized designs may need adjustments, and some old files can be removed after a successful upgrade.
- **Less repeated work behind the scenes.** Reduces repeated loading of page text and templates. This is not a claim of a measured speed increase on every forum.
- **An easier path to the project page.** Clicking Welcome Landing in Admin CP opens its GitHub repository.
- **Clearer upgrade instructions.** Explains what to back up, how to preserve customizations and exactly which old files to remove.

### Before You Upgrade

**Do not simply replace the plugin file.** This upgrade also changes language files, page styles, scripts and templates. Read [UPGRADE.md](UPGRADE.md) first: back up your installation and prepare your customizations before uploading. You will need to deactivate and reactivate the plugin, but **do not uninstall it**.

### Testing Limits

Testing used MyBB 1.8.40 and PHP 8.2.30 with MyBB's default image verification.

Not every setup has been tested. Examples include forums installed in a subfolder, some guest-setting combinations, other verification services, certain template/removal cases and customized themes. Screen-reader use and an actual right-to-left forum installation also remain untested. See [README.md](README.md) and [UPGRADE.md](UPGRADE.md) for the full limits, and test on a development copy before updating your live forum.
