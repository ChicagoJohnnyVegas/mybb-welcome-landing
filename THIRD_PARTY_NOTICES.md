# Third-Party Notices

Welcome Landing ships the following pinned client-side libraries locally. Visitors do not need a CDN connection to load them.

## Bootstrap 5.3.8

- Files: `landing/css/bootstrap.css`, `bootstrap.min.css`, `bootstrap.rtl.min.css`, their source maps, and `landing/js/bootstrap.js`, `bootstrap.min.js` and their source maps.
- Copyright 2011-2025 The Bootstrap Authors.
- License: MIT; full notice in `landing/LICENSE-bootstrap.txt` and copyright headers in the distributed files.
- Source: [Bootstrap v5.3.8](https://github.com/twbs/bootstrap/tree/v5.3.8/dist).
- These are the standard non-bundle builds. Modal and Collapse do not require Popper. Custom dropdowns, tooltips or popovers may require additional dependencies; they are not part of the supplied page.

## jQuery 3.7.1

- File: `landing/js/jquery.js` (full-featured minified build, including AJAX).
- Copyright OpenJS Foundation and other contributors.
- License: MIT; full notice in `landing/LICENSE-jquery.txt`.
- Source: [official jQuery 3.7.1 distribution](https://code.jquery.com/jquery-3.7.1.min.js).
- Retained for MyBB CAPTCHA helpers and the landing script. Bootstrap uses its native API with its jQuery bridge disabled to avoid replacing MyBB's modal plugin.
- jQuery 3.x receives critical security patches and bug fixes; 4.x is the current branch. The 3.x choice is a deliberate MyBB compatibility boundary, not a claim that it is the latest major release. See [jQuery support policy](https://jquery.com/support/).

## Removed Legacy Assets

Version 1.32 no longer ships Bootstrap 3 theme styles/maps, Glyphicons fonts or waitForImages. When upgrading from v1.21, review custom templates and remove obsolete references before retiring their deployed files. Overlay uploads do not delete old server files automatically; follow the exact cleanup list in UPGRADE.md.

## Project License

Welcome Landing is MIT licensed; see `LICENSE`. The customized Big Picture stylesheet retains its original Start Bootstrap Apache 2.0 header. Upstream source and license references are preserved in the distributed assets.
