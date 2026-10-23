<?php
/**
 * Welcome Landing Admin CP language strings.
 */

$l['welcome_landing_plugin_name'] = 'Welcome Landing';
$l['welcome_landing_plugin_desc'] = 'Provides a custom landing page at {1} and optionally redirects guests from index.php and deep links. Intended for private forums.';

$l['welcome_landing_setting_group_title'] = 'Welcome Landing';
$l['welcome_landing_setting_group_desc'] = 'Settings for the Welcome Landing guest entry plugin.';

$l['welcome_landing_setting_enable_index_guest_redirect_title'] = 'Enable index guest redirect';
$l['welcome_landing_setting_enable_index_guest_redirect_desc'] = 'Redirect guests from index.php to the Welcome Landing page.';

$l['welcome_landing_setting_enable_guest_gatekeeper_title'] = 'Enable guest gatekeeper';
$l['welcome_landing_setting_enable_guest_gatekeeper_desc'] = 'Redirect guests from protected forum pages to the Welcome Landing page.';

$l['welcome_landing_setting_redirect_generic_login_title'] = 'Redirect generic MyBB login';
$l['welcome_landing_setting_redirect_generic_login_desc'] = 'Redirect member.php?action=login to the Welcome Landing page for guests.';

$l['welcome_landing_setting_logged_in_redirect_title'] = 'Logged-in redirect path';
$l['welcome_landing_setting_logged_in_redirect_desc'] = 'Path relative to the forum root, such as /index.php. Do not include the forum subdirectory or a full URL.';

$l['welcome_landing_setting_asset_path_title'] = 'Asset URL path';
$l['welcome_landing_setting_asset_path_desc'] = 'URL path relative to the forum root for CSS and JavaScript assets, normally /landing.';

$l['welcome_landing_setting_image_url_path_title'] = 'Image URL path';
$l['welcome_landing_setting_image_url_path_desc'] = 'URL path relative to the forum root for background images, normally /landing/img/landing.';

$l['welcome_landing_setting_image_dir_path_title'] = 'Image filesystem path';
$l['welcome_landing_setting_image_dir_path_desc'] = 'Filesystem path relative to the forum root for rotating Welcome Landing background images.';

$l['welcome_landing_setting_image_prefix_title'] = 'Image filename prefix';
$l['welcome_landing_setting_image_prefix_desc'] = 'Only files whose names begin with this prefix are used as rotating background images.';

$l['welcome_landing_setting_image_selection_mode_title'] = 'Image selection mode';
$l['welcome_landing_setting_image_selection_mode_desc'] = '<ul><li>Shuffle - Shows every image once before reshuffling. Remembers progress in this browser.</li><li>Random - Picks an image independently each time; repeats are possible.</li><li>Sequential - Shows images in filename order, then repeats. Remembers progress in this browser.</li><li>Daily - Shows everyone the same image for the day, using the forum timezone and daylight-saving setting.</li></ul>';

$l['welcome_landing_setting_fallback_image_title'] = 'Fallback image filename';
$l['welcome_landing_setting_fallback_image_desc'] = 'Fallback image filename inside the configured image URL path.';

$l['welcome_landing_setting_show_forgot_password_link_title'] = 'Show forgot-password link';
$l['welcome_landing_setting_show_forgot_password_link_desc'] = 'Show the forgot-password link on the Welcome Landing page and login modal.';

$l['welcome_landing_setting_show_about_modal_title'] = 'Show about modal';
$l['welcome_landing_setting_show_about_modal_desc'] = 'Show the About link and modal on the Welcome Landing page.';

$l['welcome_landing_images_title'] = 'Background Image Status';
$l['welcome_landing_images_save_stay'] = 'Save and stay';
$l['welcome_landing_images_save_exit'] = 'Save and exit';
$l['welcome_landing_images_url_path'] = 'Image URL path';
$l['welcome_landing_images_browser_access'] = 'Browser access';
$l['welcome_landing_images_browser_unchecked'] = 'Not checked';
$l['welcome_landing_images_open_image'] = 'Open image (new tab)';
$l['welcome_landing_images_no_sample'] = 'No image file available';
$l['welcome_landing_images_directory'] = 'Image folder (relative to forum root)';
$l['welcome_landing_images_folder'] = 'Folder status';
$l['welcome_landing_images_prefix'] = 'Filename prefix';
$l['welcome_landing_images_count'] = 'Matching image files';
$l['welcome_landing_images_fallback'] = 'Fallback image (relative to forum root)';
$l['welcome_landing_images_fallback_status'] = 'Fallback status';
$l['welcome_landing_images_mode'] = 'Selection mode';
$l['welcome_landing_images_scope'] = 'Check scope';
$l['welcome_landing_images_scope_value'] = 'Saved settings and server filesystem only; browser delivery and image contents not checked.';
$l['welcome_landing_images_missing'] = 'Not found';
$l['welcome_landing_images_unreadable'] = 'Not readable';
$l['welcome_landing_images_readable'] = 'Readable';
$l['welcome_landing_images_unknown'] = 'Could not determine';
$l['welcome_landing_images_mode_random'] = 'Shuffle';
$l['welcome_landing_images_mode_independent'] = 'Random';
$l['welcome_landing_images_mode_sequential'] = 'Sequential';
$l['welcome_landing_images_mode_daily'] = 'Daily';
$l['welcome_landing_images_notes'] = 'Notes';
$l['welcome_landing_images_folder_missing'] = 'The image folder could not be found or accessed. Check the configured filesystem path.';
$l['welcome_landing_images_folder_unreadable'] = 'The image folder is not readable by PHP. Check its permissions.';
$l['welcome_landing_images_scan_failed'] = 'The image folder scan failed. The welcome page will attempt to use the fallback image.';
$l['welcome_landing_images_no_images'] = 'No matching images were found. The welcome page will attempt to use the fallback image.';
$l['welcome_landing_images_one_image'] = 'Only one image matches. All selection modes will show that image.';
$l['welcome_landing_images_fallback_missing'] = 'The fallback image could not be found or accessed. Matching images can still be used, but the backup image is unavailable.';
$l['welcome_landing_images_fallback_unreadable'] = 'The fallback image is not readable by PHP. Check its permissions; browser delivery has not been tested.';
