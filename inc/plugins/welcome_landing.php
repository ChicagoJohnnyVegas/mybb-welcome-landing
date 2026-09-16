<?php
/**
 * MyBB Welcome Landing
 *
 * Provides:
 *   - /misc.php?action=welcome   (custom landing/login page, no MyBB header/footer)
 *   - Optional guest redirect from /index.php to the landing page
 *   - Optional gatekeeper redirect for guests hitting deep links (threads, PM links, etc.)
 *
 * IMPORTANT:
 * This was originally written for a private, invite-only forum.
 * It does NOT implement or support public registration flows.
 *
 * Provided AS-IS. No support.
 */

if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

/* ============================================================
 * CONFIGURATION
 * ============================================================ */

// Core behavior toggles
define('WEL_ENABLE_INDEX_GUEST_REDIRECT', false);
define('WEL_ENABLE_GUEST_GATEKEEPER', true);

// Landing endpoint
define('WEL_WELCOME_ACTION', 'welcome'); // /misc.php?action=welcome
define('WEL_WELCOME_ENDPOINT', '/misc.php?action=' . WEL_WELCOME_ACTION);
define('WEL_WELCOME_ENDPOINT_NORM', '/' . ltrim(WEL_WELCOME_ENDPOINT, '/'));

// Optional links
define('WEL_SHOW_FORGOT_PASSWORD_LINK', true);

// About modal
define('WEL_SHOW_ABOUT', true);

// Assets and images
define('WEL_ASSET_PATH', '/landing');                 // URL path for CSS/JS assets
define('WEL_IMAGE_DIR_REL', '/landing/img/landing');  // filesystem RELATIVE TO forum root
define('WEL_IMAGE_URL_REL', '/landing/img/landing');  // URL path for images
define('WEL_IMAGE_PREFIX', 'WL');                     // matches files like WL*.jpg/png/webp etc.
define('WEL_FALLBACK_IMAGE', 'WL1.jpg');              // fallback filename inside WEL_IMAGE_URL_REL


// Normalize paths (defensive against missing leading slashes)
define('WEL_ASSET_PATH_NORM', '/' . ltrim(WEL_ASSET_PATH, '/'));
define('WEL_IMAGE_URL_REL_NORM', '/' . ltrim(WEL_IMAGE_URL_REL, '/'));
define('WEL_IMAGE_DIR_REL_NORM', '/' . ltrim(WEL_IMAGE_DIR_REL, '/'));


// If you want to restrict formats, edit this list
$GLOBALS['WEL_ALLOWED_EXTS'] = array('jpg','jpeg','png','gif','webp','bmp');

// Gatekeeper public paths and prefixes that should be accessible without redirect
$GLOBALS['WEL_GUEST_PUBLIC_PATHS'] = array(
    '/favicon.ico',
    '/robots.txt',
);

$GLOBALS['WEL_GUEST_WHITELIST_PREFIXES'] = array(
    WEL_ASSET_PATH_NORM . '/', // landing assets
);

// Member actions that must be allowed through for login and recovery
$GLOBALS['WEL_ALLOWED_MEMBER_ACTIONS'] = array(
    'do_login', 'login', 'lostpw', 'resetpassword', 'logout', 'do_logout'
);

// Where to send logged-in users who hit the landing page
define('WEL_LOGGED_IN_REDIRECT', '/index.php');

define('WEL_LOGGED_IN_REDIRECT_NORM', '/' . ltrim(WEL_LOGGED_IN_REDIRECT, '/'));

// Plugin-owned persistence identifiers
define('WEL_SETTING_GROUP_NAME', 'welcome_landing');
define('WEL_SETTING_NAME_PREFIX', 'welcome_landing_');
define('WEL_TEMPLATE_PREFIX', 'welcome_landing_');
define('WEL_TEMPLATE_MIGRATION_VERSION', 1);

$GLOBALS['WEL_SETTINGS'] = array(
    'enable_index_guest_redirect' => array(
        'title_key'       => 'welcome_landing_setting_enable_index_guest_redirect_title',
        'description_key' => 'welcome_landing_setting_enable_index_guest_redirect_desc',
        'title'           => 'Enable index guest redirect',
        'description'     => 'Redirect guests from index.php to the Welcome Landing page.',
        'optionscode' => 'yesno',
        'value'       => WEL_ENABLE_INDEX_GUEST_REDIRECT ? '1' : '0',
    ),
    'enable_guest_gatekeeper' => array(
        'title_key'       => 'welcome_landing_setting_enable_guest_gatekeeper_title',
        'description_key' => 'welcome_landing_setting_enable_guest_gatekeeper_desc',
        'title'           => 'Enable guest gatekeeper',
        'description'     => 'Redirect guests from protected forum pages to the Welcome Landing page.',
        'optionscode' => 'yesno',
        'value'       => WEL_ENABLE_GUEST_GATEKEEPER ? '1' : '0',
    ),
    'redirect_generic_login' => array(
        'title_key'       => 'welcome_landing_setting_redirect_generic_login_title',
        'description_key' => 'welcome_landing_setting_redirect_generic_login_desc',
        'title'           => 'Redirect generic MyBB login',
        'description'     => 'Redirect member.php?action=login to the Welcome Landing page for guests.',
        'optionscode' => 'yesno',
        'value'       => '1',
    ),
    'logged_in_redirect' => array(
        'title_key'       => 'welcome_landing_setting_logged_in_redirect_title',
        'description_key' => 'welcome_landing_setting_logged_in_redirect_desc',
        'title'           => 'Logged-in redirect path',
        'description'     => 'Site-relative path used when a logged-in user visits the Welcome Landing page.',
        'optionscode' => 'text',
        'value'       => WEL_LOGGED_IN_REDIRECT_NORM,
    ),
    'asset_path' => array(
        'title_key'       => 'welcome_landing_setting_asset_path_title',
        'description_key' => 'welcome_landing_setting_asset_path_desc',
        'title'           => 'Asset URL path',
        'description'     => 'URL path for Welcome Landing CSS and JavaScript assets.',
        'optionscode' => 'text',
        'value'       => WEL_ASSET_PATH_NORM,
    ),
    'image_url_path' => array(
        'title_key'       => 'welcome_landing_setting_image_url_path_title',
        'description_key' => 'welcome_landing_setting_image_url_path_desc',
        'title'           => 'Image URL path',
        'description'     => 'URL path for rotating Welcome Landing background images.',
        'optionscode' => 'text',
        'value'       => WEL_IMAGE_URL_REL_NORM,
    ),
    'image_dir_path' => array(
        'title_key'       => 'welcome_landing_setting_image_dir_path_title',
        'description_key' => 'welcome_landing_setting_image_dir_path_desc',
        'title'           => 'Image filesystem path',
        'description'     => 'Filesystem path relative to the forum root for rotating Welcome Landing background images.',
        'optionscode' => 'text',
        'value'       => WEL_IMAGE_DIR_REL_NORM,
    ),
    'image_prefix' => array(
        'title_key'       => 'welcome_landing_setting_image_prefix_title',
        'description_key' => 'welcome_landing_setting_image_prefix_desc',
        'title'           => 'Image filename prefix',
        'description'     => 'Only files whose names begin with this prefix are used as rotating background images.',
        'optionscode' => 'text',
        'value'       => WEL_IMAGE_PREFIX,
    ),
    'fallback_image' => array(
        'title_key'       => 'welcome_landing_setting_fallback_image_title',
        'description_key' => 'welcome_landing_setting_fallback_image_desc',
        'title'           => 'Fallback image filename',
        'description'     => 'Fallback image filename inside the configured image URL path.',
        'optionscode' => 'text',
        'value'       => WEL_FALLBACK_IMAGE,
    ),
    'show_forgot_password_link' => array(
        'title_key'       => 'welcome_landing_setting_show_forgot_password_link_title',
        'description_key' => 'welcome_landing_setting_show_forgot_password_link_desc',
        'title'           => 'Show forgot-password link',
        'description'     => 'Show the forgot-password link on the Welcome Landing page and login modal.',
        'optionscode' => 'yesno',
        'value'       => WEL_SHOW_FORGOT_PASSWORD_LINK ? '1' : '0',
    ),
    'show_about_modal' => array(
        'title_key'       => 'welcome_landing_setting_show_about_modal_title',
        'description_key' => 'welcome_landing_setting_show_about_modal_desc',
        'title'           => 'Show about modal',
        'description'     => 'Show the About link and modal on the Welcome Landing page.',
        'optionscode' => 'yesno',
        'value'       => WEL_SHOW_ABOUT ? '1' : '0',
    ),
);



/* ============================================================
 * PLUGIN METADATA
 * ============================================================ */

function welcome_landing_info()
{
    welcome_landing_load_admin_language();
    $description = welcome_landing_admin_lang('welcome_landing_plugin_desc', "Provides a custom landing page at {1} and optionally redirects guests from index.php and deep links. Intended for private forums.");
    $description = str_replace('{1}', WEL_WELCOME_ENDPOINT_NORM, $description);

    return array(
        "name"          => welcome_landing_admin_lang('welcome_landing_plugin_name', 'Welcome Landing'),
        "description"   => $description,
        "website"       => "",
        "author"        => "\"Chicago\" JohnnyVegas",
        "authorsite"    => "",
        "version"       => "1.21",
        "compatibility" => "18*"
    );
}

function welcome_landing_install()
{
    welcome_landing_ensure_settings();
    welcome_landing_ensure_templates();
    rebuild_settings();
}

function welcome_landing_is_installed()
{
    global $db;

    $query = $db->simple_select(
        'settinggroups',
        'gid',
        "name='" . $db->escape_string(WEL_SETTING_GROUP_NAME) . "'",
        array('limit' => 1)
    );

    return (bool)$db->num_rows($query);
}

function welcome_landing_uninstall()
{
    global $db;

    $settingPrefix = $db->escape_string(WEL_SETTING_NAME_PREFIX);
    $settingGroupName = $db->escape_string(WEL_SETTING_GROUP_NAME);

    $db->delete_query('settings', "name LIKE '{$settingPrefix}%'");
    $db->delete_query('settinggroups', "name='{$settingGroupName}'");
    welcome_landing_delete_templates();

    rebuild_settings();
}

function welcome_landing_activate()
{
    welcome_landing_ensure_settings();
    welcome_landing_ensure_templates();
    rebuild_settings();
}

function welcome_landing_deactivate() { }

function welcome_landing_ensure_settings()
{
    global $db;

    welcome_landing_load_admin_language();

    $gid = welcome_landing_get_setting_group_id();
    $settingGroupTitle = welcome_landing_admin_lang('welcome_landing_setting_group_title', 'Welcome Landing');
    $settingGroupDescription = welcome_landing_admin_lang('welcome_landing_setting_group_desc', 'Settings for the Welcome Landing guest entry plugin.');

    if (!$gid) {
        $query = $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder');
        $maxDisporder = (int)$db->fetch_field($query, 'max_disporder');

        $settingGroup = array(
            'name'        => WEL_SETTING_GROUP_NAME,
            'title'       => $db->escape_string($settingGroupTitle),
            'description' => $db->escape_string($settingGroupDescription),
            'disporder'   => $maxDisporder + 1,
            'isdefault'   => 0
        );

        $gid = (int)$db->insert_query('settinggroups', $settingGroup);
    } else {
        $db->update_query('settinggroups', array(
            'title'       => $db->escape_string($settingGroupTitle),
            'description' => $db->escape_string($settingGroupDescription),
        ), "gid='{$gid}'");
    }

    $disporder = 1;
    foreach ($GLOBALS['WEL_SETTINGS'] as $key => $setting) {
        $name = WEL_SETTING_NAME_PREFIX . $key;
        $title = welcome_landing_setting_admin_title($setting);
        $description = welcome_landing_setting_admin_description($setting);
        $query = $db->simple_select(
            'settings',
            'sid',
            "name='" . $db->escape_string($name) . "'",
            array('limit' => 1)
        );

        if ($db->num_rows($query)) {
            $sid = (int)$db->fetch_field($query, 'sid');
            $db->update_query('settings', array(
                'title'       => $db->escape_string($title),
                'description' => $db->escape_string($description),
                'disporder'   => $disporder,
                'gid'         => $gid,
            ), "sid='{$sid}'");
            ++$disporder;
            continue;
        }

        $db->insert_query('settings', array(
            'name'        => $db->escape_string($name),
            'title'       => $db->escape_string($title),
            'description' => $db->escape_string($description),
            'optionscode' => $setting['optionscode'],
            'value'       => $db->escape_string($setting['value']),
            'disporder'   => $disporder,
            'gid'         => $gid,
            'isdefault'   => 0
        ));

        ++$disporder;
    }
}

function welcome_landing_setting_admin_title($setting)
{
    return welcome_landing_admin_lang($setting['title_key'], $setting['title']);
}

function welcome_landing_setting_admin_description($setting)
{
    return welcome_landing_admin_lang($setting['description_key'], $setting['description']);
}

function welcome_landing_get_setting_group_id()
{
    global $db;

    $query = $db->simple_select(
        'settinggroups',
        'gid',
        "name='" . $db->escape_string(WEL_SETTING_GROUP_NAME) . "'",
        array('limit' => 1)
    );

    return $db->num_rows($query) ? (int)$db->fetch_field($query, 'gid') : 0;
}

function welcome_landing_default_templates()
{
    return array(
        'nav_item_forgot' => <<<'HTML'
                        <li><a href="{$forgotUrl}">{$forgotText}</a></li>
HTML
        ,
        'nav_item_about' => <<<'HTML'
                        <li><a data-toggle="modal" href="#welcome_about">{$aboutLinkText}</a></li>
HTML
        ,
        'login_forgot_link' => <<<'HTML'
                                            <span class="col-md-12 text-right"><a href="{$forgotUrl}">{$forgotText}</a></span>
HTML
        ,
        'login_modal' => <<<'HTML'
        <div class="container">
            <div class="row">
                <div id="welcome_login" tabindex="-1" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button class="close" aria-hidden="true" type="button" data-dismiss="modal">x</button>
                                <h4 class="modal-title">{$loginTitle}</h4>
                            </div>

                            <form id="Form_User_SignIn" action="{$bburl}/misc.php?action=welcome" method="post">
                                <input type="hidden" name="wel_do" value="login">
                                <input type="hidden" name="url" value="{$redirectEsc}">
                                <input type="hidden" name="my_post_key" value="{$postCodeEsc}">
                                <input type="hidden" name="remember" value="yes">

                                <div class="modal-body">
                                    <div class="wel-login-error">
                                        {$loginErrorHtml}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>
                                                <input id="txtUserName" name="username" value="{$prefillUsernameEsc}" placeholder="{$usernamePlaceholder}"
                                                       class="form-control" type="text" autocomplete="username">
                                            </p>
                                            <p>
                                                <input id="txtPassword" name="password" placeholder="{$passwordPlaceholder}"
                                                       class="form-control" type="password" autocomplete="current-password">
                                            </p>
                                            {$loginForgotLinkHtml}
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-default" type="button" data-dismiss="modal">{$closeText}</button>
                                    <button class="btn btn-primary" type="submit">{$submitText}</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML
        ,
        'about_modal' => <<<'HTML'
        <div class="container">
            <div class="row">
                <div id="welcome_about" tabindex="-1" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button class="close" aria-hidden="true" type="button" data-dismiss="modal">x</button>
                                <h4 class="modal-title">{$aboutTitle}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        {$aboutHtml}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-default" type="button" data-dismiss="modal">{$aboutCloseText}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML
        ,
        'page_shell' => <<<'HTML'
<!DOCTYPE html>
<html id="universe" class="full InitialHide" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <title>{$pageTitle}</title>

    <link href="{$assetBase}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$assetBase}/css/the-big-picture.css" rel="stylesheet">
    <link href="{$assetBase}/css/imgLoader.css" rel="stylesheet">
</head>

<body>
    <div class="page-container">
        <nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">{$navToggleLabel}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" data-toggle="modal" href="#welcome_login">{$navBrand}</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
{$navForgotItemHtml}
{$navAboutItemHtml}
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-offset-6 col-md-5 col-sm-12 col-xs-12 input-group input-group-lg btn-row">
                    <button id="btnLogin" class="btn btn-primary btn-custom btn-success" data-toggle="modal" href="#welcome_login" type="button">
                        {$primaryBtn}
                    </button>
                </div>
            </div>
        </div>

{$loginModalHtml}
{$aboutModalHtml}
    </div>

    <script src="{$assetBase}/js/jquery.js"></script>
    <script src="{$assetBase}/js/bootstrap.min.js"></script>
    <script src="{$assetBase}/js/jquery.waitforimages.min.js"></script>

    <script>
      function openLoginModal() {
	      if (window.jQuery && $('#welcome_login').length) {
	          $('#welcome_login').modal('show');
	      }
      }

      function clearPostState() {
	      if (window.history && history.replaceState) {
	          history.replaceState(null, document.title, window.location.pathname + window.location.search);
	      }
	  }

      $(document).ready(function () {
        var images = {$imagesJson};
        var idx = Math.floor(Math.random() * images.length);
        var url = "url(" + images[idx] + ")";

        $(".full").css("background-image", url).waitForImages({
          waitForAll: true,
          finished: function () {
            $(".InitialHide").fadeIn(2000);
          }
        });
      });

      document.addEventListener('DOMContentLoaded', function () {
	      if ({$openLoginModalJs}) {
	          if (typeof openLoginModal === 'function') openLoginModal();
	          var u = document.querySelector('#Form_User_SignIn input[name="username"]');
	          if (u) u.focus();
	      }
	  });

	  $(function () {
	      $('#welcome_login').on('hidden.bs.modal', function () {
            if (window.history && history.replaceState) {
                history.replaceState(null, document.title, window.location.pathname + window.location.search);
            }

            var err = document.querySelector('.wel-login-error');
            if (err) {
                err.style.display = 'none';
            }

            var form = document.getElementById('Form_User_SignIn');
            if (form) {
                var u = form.querySelector('input[name="username"]');
                var p = form.querySelector('input[name="password"]');

                if (u) u.value = '';
                if (p) p.value = '';
            }
         });
	  });
    </script>

</body>
</html>
HTML
        ,
    );
}

function welcome_landing_ensure_templates()
{
    global $db;

    $templates = welcome_landing_default_templates();
    $dateline = defined('TIME_NOW') ? TIME_NOW : time();

    foreach ($templates as $key => $template) {
        $title = WEL_TEMPLATE_PREFIX . $key;
        $query = $db->simple_select(
            'templates',
            'tid, template',
            "title='" . $db->escape_string($title) . "'"
        );

        if ($db->num_rows($query)) {
            while ($row = $db->fetch_array($query)) {
                $tid = (int)$row['tid'];
                $storedTemplate = (string)$row['template'];
                $updates = array('sid' => -1);
                $migratedTemplate = welcome_landing_migrate_template($key, $storedTemplate);

                if ($migratedTemplate !== $storedTemplate) {
                    $updates['template'] = $db->escape_string($migratedTemplate);
                    $updates['dateline'] = $dateline;
                }

                $db->update_query('templates', $updates, "tid='{$tid}'");
            }

            continue;
        }

        $db->insert_query('templates', array(
            'title' => $db->escape_string($title),
            'template' => $db->escape_string($template),
            'sid' => -1,
            'version' => 1800,
            'dateline' => $dateline,
        ));
    }
}

function welcome_landing_template_migrations()
{
    return array(
        array(
            'version' => 1,
            'key' => 'page_shell',
            'callback' => 'welcome_landing_migrate_page_shell_nav_toggle_label',
        ),
    );
}

function welcome_landing_migrate_template($key, $template)
{
    foreach (welcome_landing_template_migrations() as $migration) {
        if ($migration['key'] !== $key || !is_callable($migration['callback'])) {
            continue;
        }

        $template = call_user_func($migration['callback'], $template);
    }

    return $template;
}

function welcome_landing_migrate_page_shell_nav_toggle_label($template)
{
    if (strpos($template, 'Toggle navigation') === false || strpos($template, '{$navToggleLabel}') !== false) {
        return $template;
    }

    return str_replace('Toggle navigation', '{$navToggleLabel}', $template);
}

function welcome_landing_delete_templates()
{
    global $db;

    foreach (array_keys(welcome_landing_default_templates()) as $key) {
        $title = WEL_TEMPLATE_PREFIX . $key;
        $db->delete_query('templates', "title='" . $db->escape_string($title) . "'");
    }
}

function welcome_landing_setting($key, $default = '')
{
    global $mybb;

    $name = WEL_SETTING_NAME_PREFIX . $key;

    if (isset($mybb->settings[$name]) && $mybb->settings[$name] !== '') {
        return $mybb->settings[$name];
    }

    return $default;
}

function welcome_landing_setting_bool($key, $default = false)
{
    $value = welcome_landing_setting($key, $default ? '1' : '0');

    return $value === '1' || $value === 1 || $value === true || $value === 'yes';
}

function welcome_landing_setting_path($key, $default)
{
    $value = trim((string)welcome_landing_setting($key, $default));

    if ($value === '') {
        $value = $default;
    }

    return '/' . ltrim($value, '/');
}

function welcome_landing_setting_text($key, $default)
{
    $value = trim((string)welcome_landing_setting($key, $default));

    return $value === '' ? $default : $value;
}

function welcome_landing_default_redirect_path()
{
    return welcome_landing_safe_redirect_path(
        welcome_landing_setting('logged_in_redirect', WEL_LOGGED_IN_REDIRECT_NORM),
        WEL_LOGGED_IN_REDIRECT_NORM
    );
}

function welcome_landing_safe_redirect_path($redirect, $default = WEL_LOGGED_IN_REDIRECT_NORM)
{
    $default = preg_replace('/[\x00-\x1F\x7F]+/', '', str_replace("\\", '', (string)$default));
    $default = '/' . ltrim($default, '/');
    $redirect = rawurldecode((string)$redirect);
    $redirect = trim(preg_replace('/[\x00-\x1F\x7F]+/', '', $redirect));

    if ($redirect === '') {
        return $default;
    }

    if (strpos($redirect, '://') !== false || strpos($redirect, '\\') !== false) {
        return $default;
    }

    if (substr($redirect, 0, 1) !== '/' || strpos($redirect, '//') === 0) {
        return $default;
    }

    return $redirect;
}

function welcome_landing_login_attempt_user($username)
{
    if ($username === '' || !function_exists('get_user_by_username')) {
        return array();
    }

    $options = array(
        'fields' => array('loginattempts'),
        'username_method' => isset($GLOBALS['mybb']->settings['username_method']) ? (int)$GLOBALS['mybb']->settings['username_method'] : 0,
    );

    $user = get_user_by_username($username, $options);

    return is_array($user) ? $user : array();
}

function welcome_landing_check_login_attempts($uid = 0)
{
    global $mybb;

    if (function_exists('login_attempt_check')) {
        return (int)login_attempt_check((int)$uid);
    }

    return isset($mybb->cookies['loginattempts']) ? (int)$mybb->cookies['loginattempts'] : 0;
}

function welcome_landing_record_failed_login_attempt($uid, $loginAttempts)
{
    global $db;

    my_setcookie('loginattempts', (int)$loginAttempts + 1);

    if ((int)$uid > 0) {
        $db->update_query('users', array('loginattempts' => 'loginattempts+1'), "uid='" . (int)$uid . "'", 1, true);
    }
}

function welcome_landing_clear_login_attempts($uid)
{
    global $db, $session;

    $uid = (int)$uid;

    my_setcookie('loginattempts', 1);

    if (isset($session) && is_object($session) && !empty($session->sid)) {
        $sid = $db->escape_string($session->sid);
        $db->update_query('sessions', array('uid' => $uid), "sid='{$sid}'");
    }

    if ($uid > 0) {
        $db->update_query('users', array('loginattempts' => 1), "uid='{$uid}'");
    }
}

function welcome_landing_load_language()
{
    global $lang;

    if (isset($lang) && is_object($lang) && method_exists($lang, 'load')) {
        $lang->load('welcome_landing', false, true);
    }
}

function welcome_landing_load_admin_language()
{
    global $lang;

    if (isset($lang) && is_object($lang) && method_exists($lang, 'load')) {
        $lang->load('welcome_landing', false, true);
    }
}

function welcome_landing_lang($key, $default)
{
    global $lang;

    welcome_landing_load_language();

    if (isset($lang) && is_object($lang) && isset($lang->$key) && $lang->$key !== '') {
        return $lang->$key;
    }

    return $default;
}

function welcome_landing_admin_lang($key, $default)
{
    global $lang;

    welcome_landing_load_admin_language();

    if (isset($lang) && is_object($lang) && isset($lang->$key) && $lang->$key !== '') {
        return $lang->$key;
    }

    return $default;
}

function welcome_landing_render_template($key, $vars = array())
{
    global $templates;

    $defaults = welcome_landing_default_templates();
    $template = isset($defaults[$key]) ? $defaults[$key] : '';
    $usingStoredTemplate = false;
    $title = WEL_TEMPLATE_PREFIX . $key;

    if (isset($templates) && is_object($templates) && method_exists($templates, 'get')) {
        $storedTemplate = $templates->get($title);

        if ($storedTemplate !== false && $storedTemplate !== '') {
            $template = $storedTemplate;
            $usingStoredTemplate = true;
        }
    }

    if (!$usingStoredTemplate) {
        $template = str_replace("\\'", "'", addslashes($template));
    }

    extract($vars, EXTR_SKIP);
    $rendered = '';
    eval("\$rendered = \"" . $template . "\";");

    return $rendered;
}

/* ============================================================
 * HOOKS
 * ============================================================ */

$plugins->add_hook('global_start', 'welcome_landing_intercept_welcome');
$plugins->add_hook('global_start', 'welcome_landing_redirect_index_guests');
$plugins->add_hook('global_start', 'welcome_landing_guest_gatekeeper');

/* ============================================================
 * IMPLEMENTATION
 * ============================================================ */

function welcome_landing_intercept_welcome()
{
    global $mybb;

    if (!isset($_SERVER['SCRIPT_NAME']) || basename($_SERVER['SCRIPT_NAME']) !== 'misc.php') {
        return;
    }

    if ($mybb->get_input('action') !== WEL_WELCOME_ACTION) {
        return;
    }

    // Logged in: send to forum home
    if (!empty($mybb->user['uid'])) {
        $bburl = rtrim($mybb->settings['bburl'], '/');
        header("Location: {$bburl}" . welcome_landing_default_redirect_path());
        exit;
    }

    $redirect = welcome_landing_safe_redirect_path($mybb->get_input('url'), welcome_landing_default_redirect_path());
    $bburl = rtrim($mybb->settings['bburl'], '/');
    $assetBase = $bburl . welcome_landing_setting_path('asset_path', WEL_ASSET_PATH_NORM);

	$openLoginModal = false;
	$prefillUsername = '';
	$loginError = '';

	// Handle login POST from our modal
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mybb->get_input('wel_do') === 'login') {

		// CSRF check (uses MyBB's post key)
		require_once MYBB_ROOT . 'inc/functions.php';
		verify_post_check($mybb->get_input('my_post_key'));

		require_once MYBB_ROOT . 'inc/functions_user.php';
		$prefillUsername = $mybb->get_input('username');
		$password = $mybb->get_input('password');
		$loginAttemptUser = welcome_landing_login_attempt_user($prefillUsername);
		$loginAttemptUid = !empty($loginAttemptUser['uid']) ? (int)$loginAttemptUser['uid'] : 0;
		$loginAttempts = welcome_landing_check_login_attempts($loginAttemptUid);

		$openLoginModal = true;

		if (trim($prefillUsername) === '' || trim($password) === '') {
			$loginError = welcome_landing_lang('welcome_landing_error_missing_credentials', 'Please enter both username and password.');
		} else {
			// IMPORTANT:
			// MyBB’s exact login code lives in member.php (action=do_login).
			// We are calling the same underlying validation function here.
			$user = validate_password_from_username($prefillUsername, $password);

			if (!is_array($user) || empty($user['uid'])) {
				welcome_landing_record_failed_login_attempt($loginAttemptUid, $loginAttempts);
				$loginError = welcome_landing_lang('welcome_landing_error_invalid_credentials', 'Invalid username or password.');
			} else {
				// Successful auth. Use MyBB’s normal login helper if available.
				// Different MyBB installs expose different helpers, so we try the canonical path.

				// Ensure loginkey exists (MyBB usually sets/rotates this on login)
				if (empty($user['loginkey'])) {
					$user['loginkey'] = generate_loginkey();
					// Persist the loginkey
					$db = $GLOBALS['db'];
					$db->update_query('users', array('loginkey' => $db->escape_string($user['loginkey'])), "uid='" . (int)$user['uid'] . "'");
				}

				welcome_landing_clear_login_attempts((int)$user['uid']);

				// Set login cookie (standard MyBB cookie format). The null expiry matches MyBB's normal persistent login cookie.
				my_setcookie('mybbuser', (int)$user['uid'] . '_' . $user['loginkey'], null, true);
				my_setcookie('sid', '', -1, true);

				$target = welcome_landing_safe_redirect_path($redirect, welcome_landing_default_redirect_path());
				header("Location: {$bburl}{$target}");
				exit;
			}
		}
	}


    header("Content-Type: text/html; charset=UTF-8");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");

    echo welcome_landing_render($bburl, $assetBase, $redirect, $mybb->post_code, $openLoginModal, $prefillUsername, $loginError);

    exit;
}

function welcome_landing_redirect_index_guests()
{
    if (!welcome_landing_setting_bool('enable_index_guest_redirect', WEL_ENABLE_INDEX_GUEST_REDIRECT)) return;

    global $mybb;

    if (!isset($_SERVER['SCRIPT_NAME']) || basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
        return;
    }

    if (!empty($mybb->user['uid'])) {
        return;
    }

    header("Location: " . WEL_WELCOME_ENDPOINT_NORM);
    exit;
}

function welcome_landing_guest_gatekeeper()
{
    if (!welcome_landing_setting_bool('enable_guest_gatekeeper', WEL_ENABLE_GUEST_GATEKEEPER)) return;

    global $mybb;

    // Logged-in users: never redirect
    if (!empty($mybb->user['uid'])) return;

    $request = welcome_landing_current_request();

    // Allow MyBB CAPTCHA image generation for guest-facing forms such as lost password.
    if (welcome_landing_request_targets_file($request, 'captcha.php')) {
        return;
    }

    // Allow welcome endpoint itself
    if (welcome_landing_is_welcome_request($request)) {
        return;
    }

    // Allow member.php login flows
    if (welcome_landing_request_targets_file($request, 'member.php')) {
        $action = $request['action'];

        // Send MyBB's generic login page to the custom welcome/login page.
        if ($action === 'login' && welcome_landing_setting_bool('redirect_generic_login', true)) {
            header("Location: " . WEL_WELCOME_ENDPOINT_NORM);
            exit;
        }

        if ($action === '' || in_array($action, $GLOBALS['WEL_ALLOWED_MEMBER_ACTIONS'], true)) return;
    }

    if (welcome_landing_is_public_guest_path($request['path'])) {
        return;
    }

    // Redirect guests to welcome page, preserving destination
    $dest = rawurlencode($request['uri']);
    header("Location: " . WEL_WELCOME_ENDPOINT_NORM . "&url={$dest}");
    exit;
}

function welcome_landing_current_request()
{
    global $mybb;

    $reqUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $reqPath = parse_url($reqUri, PHP_URL_PATH);
    if (!$reqPath) $reqPath = '/';

    return array(
        'uri' => $reqUri,
        'path' => $reqPath,
        'action' => $mybb->get_input('action'),
        'script_name' => isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : '',
        'php_self' => isset($_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : '',
        'script_filename' => isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '',
        'path_file' => basename($reqPath),
    );
}

function welcome_landing_request_targets_file($request, $filename)
{
    return $request['script_name'] === $filename
        || $request['php_self'] === $filename
        || $request['script_filename'] === $filename
        || $request['path_file'] === $filename;
}

function welcome_landing_is_welcome_request($request)
{
    return welcome_landing_request_targets_file($request, 'misc.php')
        && $request['action'] === WEL_WELCOME_ACTION;
}

function welcome_landing_is_public_guest_path($path)
{
    if (in_array($path, $GLOBALS['WEL_GUEST_PUBLIC_PATHS'], true)) {
        return true;
    }

    foreach ($GLOBALS['WEL_GUEST_WHITELIST_PREFIXES'] as $prefix) {
        if (welcome_landing_path_matches_public_prefix($path, $prefix)) {
            return true;
        }
    }

    $assetPath = welcome_landing_setting_path('asset_path', WEL_ASSET_PATH_NORM);

    return welcome_landing_path_matches_public_prefix($path, $assetPath);
}

function welcome_landing_path_matches_public_prefix($path, $prefix)
{
    $prefix = '/' . trim((string)$prefix, '/');

    if ($prefix === '/') {
        return false;
    }

    return $path === $prefix || strpos($path, $prefix . '/') === 0;
}

function welcome_landing_render($bburl, $assetBase, $redirect, $postCode, $openLoginModal = false, $prefillUsername = '', $loginError = '')
{
    // Build image list dynamically
    // More reliable: use MYBB_ROOT directly for filesystem
    $imageDirRel = welcome_landing_setting_path('image_dir_path', WEL_IMAGE_DIR_REL_NORM);
    $imageUrlRel = welcome_landing_setting_path('image_url_path', WEL_IMAGE_URL_REL_NORM);
    $imagePrefix = welcome_landing_setting_text('image_prefix', WEL_IMAGE_PREFIX);
    $fallbackImage = welcome_landing_setting_text('fallback_image', WEL_FALLBACK_IMAGE);

    $imgDir = rtrim(MYBB_ROOT, '/\\') . $imageDirRel;


    // In practice assetBase == bburl + WEL_ASSET_PATH, so we want bburl + WEL_IMAGE_URL_REL_NORM
    $imgUrlBase = $bburl . $imageUrlRel;


    $images = array();
    $allowedExts = $GLOBALS['WEL_ALLOWED_EXTS'];

    if (is_dir($imgDir)) {
        foreach (glob($imgDir . '/' . $imagePrefix . '*') as $file) {
            if (!is_file($file)) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext === '' || !in_array($ext, $allowedExts, true)) continue;

            $images[] = $imgUrlBase . '/' . basename($file);
        }
    }

    if (empty($images)) {
        $images[] = $imgUrlBase . '/' . $fallbackImage;
    }

    sort($images);
    $imagesJson = json_encode(array_values($images));

    if ($imagesJson === false) {
        $imagesJson = json_encode(array($imgUrlBase . '/' . $fallbackImage));
    }

    $redirectEsc = htmlspecialchars_uni($redirect);
    $postCodeEsc = htmlspecialchars_uni($postCode);

    $pageTitle = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_page_title', 'Welcome'));
    $navBrand = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_nav_brand_text', 'Login to the site'));
    $navToggleLabel = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_nav_toggle_label', 'Toggle navigation'));
    $primaryBtn = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_primary_button_text', 'Log In'));
    $loginTitle = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_login_modal_title', 'Enter Your Login Credentials'));
    $submitText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_submit_button_text', 'Log In'));
    $closeText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_close_button_text', 'Cancel'));
    $aboutCloseText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_about_button_text', 'Close'));

    $showForgot = welcome_landing_setting_bool('show_forgot_password_link', WEL_SHOW_FORGOT_PASSWORD_LINK);
    $forgotText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_forgot_password_text', 'Forgot your password?'));

    $showAbout = welcome_landing_setting_bool('show_about_modal', WEL_SHOW_ABOUT);
    $aboutLinkText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_about_link_text', 'About'));
    $aboutTitle = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_about_modal_title', 'About This Site'));
    $aboutHtml = welcome_landing_lang('welcome_landing_about_modal_body', '<p>This community is private. Members can sign in from this welcome page to continue to the forum.</p><p>If you already have an account, use the login button to enter your username and password. If you need help accessing your account, use the password recovery link.</p><p>Site administrators can customize this message in the language file or replace the landing page templates and images to match their community.</p>');
    $usernamePlaceholder = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_username_placeholder', 'Username'));
    $passwordPlaceholder = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_password_placeholder', 'Password'));

    $forgotUrl = $bburl . "/member.php?action=lostpw";

    $openLoginModalJs = $openLoginModal ? 'true' : 'false';
    $prefillUsernameEsc = htmlspecialchars_uni($prefillUsername);
    $loginErrorHtml = '';

    if (!empty($loginError)) {
        $loginErrorEsc = htmlspecialchars_uni($loginError);
        $loginErrorHtml = '<div class="wel-login-error" style="margin: 10px 0; padding: 10px; border: 1px solid #c33; border-radius: 6px;">' . $loginErrorEsc . '</div>';
    }

    $navForgotItemHtml = $showForgot ? welcome_landing_render_template('nav_item_forgot', array(
        'forgotUrl' => $forgotUrl,
        'forgotText' => $forgotText,
    )) : '';

    $navAboutItemHtml = $showAbout ? welcome_landing_render_template('nav_item_about', array(
        'aboutLinkText' => $aboutLinkText,
    )) : '';

    $loginForgotLinkHtml = $showForgot ? welcome_landing_render_template('login_forgot_link', array(
        'forgotUrl' => $forgotUrl,
        'forgotText' => $forgotText,
    )) : '';

    $loginModalHtml = welcome_landing_render_template('login_modal', array(
        'loginTitle' => $loginTitle,
        'bburl' => $bburl,
        'redirectEsc' => $redirectEsc,
        'postCodeEsc' => $postCodeEsc,
        'loginErrorHtml' => $loginErrorHtml,
        'prefillUsernameEsc' => $prefillUsernameEsc,
        'usernamePlaceholder' => $usernamePlaceholder,
        'passwordPlaceholder' => $passwordPlaceholder,
        'loginForgotLinkHtml' => $loginForgotLinkHtml,
        'closeText' => $closeText,
        'submitText' => $submitText,
    ));

    $aboutModalHtml = $showAbout ? welcome_landing_render_template('about_modal', array(
        'aboutTitle' => $aboutTitle,
        'aboutHtml' => $aboutHtml,
        'aboutCloseText' => $aboutCloseText,
    )) : '';

    return welcome_landing_render_template('page_shell', array(
        'pageTitle' => $pageTitle,
        'assetBase' => $assetBase,
        'navBrand' => $navBrand,
        'navToggleLabel' => $navToggleLabel,
        'navForgotItemHtml' => $navForgotItemHtml,
        'navAboutItemHtml' => $navAboutItemHtml,
        'primaryBtn' => $primaryBtn,
        'loginModalHtml' => $loginModalHtml,
        'aboutModalHtml' => $aboutModalHtml,
        'imagesJson' => $imagesJson,
        'openLoginModalJs' => $openLoginModalJs,
    ));
}
