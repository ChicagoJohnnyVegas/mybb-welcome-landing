# Changelog

All notable changes to Welcome Landing will be documented in this file.

## 1.21 - Login Attempt Hotfix

- Fixed an HTTP 500 on successful modal login caused by writing failed-login state to the wrong MyBB table.
- Aligned modal login attempt tracking with MyBB's `users.loginattempts` field and `loginattempts` cookie.
- Kept successful login session updates limited to MyBB's normal `sessions.uid` field.

## 1.20 - Security Hardening

- Added MyBB failed-login attempt checks to the custom modal login flow.
- Recorded failed modal login attempts in MyBB user login-attempt state and the `loginattempts` cookie.
- Cleared failed-login attempt state after successful modal login.
- Hardened safe redirect handling by stripping control characters from redirect targets before validation.

## 1.19 - Public Distribution Preparation

- Converted packaged defaults from private-site copy and imagery to generic public defaults.
- Added three bundled generic `WL*.jpg` landing background images.
- Added language-file backed public copy and Admin CP copy.
- Added plugin-managed Global Templates for the landing page shell, modals and link fragments.
- Added Admin CP settings for redirect behavior, asset paths, image paths, image prefix/fallback and optional links/modals.
- Added safer guest route handling for login, lost-password recovery, password reset, CAPTCHA, logout and landing assets.
- Added safe site-relative redirect handling for guest deep links.
- Added MyBB plugin lifecycle functions for install, activation, deactivation and uninstall.
- Added non-destructive template creation and migration behavior.
- Added public README, MIT license, third-party notices and release packaging dry run.

## Notes

This changelog begins with the public-distribution preparation baseline. Earlier private development history is intentionally not treated as public release history.