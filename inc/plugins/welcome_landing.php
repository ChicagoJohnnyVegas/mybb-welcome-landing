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
    'do_login', 'login', 'lostpw', 'do_lostpw', 'resetpassword', 'logout', 'do_logout'
);

// Where to send logged-in users who hit the landing page
define('WEL_LOGGED_IN_REDIRECT', '/index.php');

define('WEL_LOGGED_IN_REDIRECT_NORM', '/' . ltrim(WEL_LOGGED_IN_REDIRECT, '/'));

// Plugin-owned persistence identifiers
define('WEL_SETTING_GROUP_NAME', 'welcome_landing');
define('WEL_SETTING_NAME_PREFIX', 'welcome_landing_');
define('WEL_TEMPLATE_PREFIX', 'welcome_landing_');

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
        'description'     => 'Path relative to the forum root, such as /index.php. Do not include the forum subdirectory or a full URL.',
        'optionscode' => 'text',
        'value'       => WEL_LOGGED_IN_REDIRECT_NORM,
    ),
    'asset_path' => array(
        'title_key'       => 'welcome_landing_setting_asset_path_title',
        'description_key' => 'welcome_landing_setting_asset_path_desc',
        'title'           => 'Asset URL path',
        'description'     => 'URL path relative to the forum root for CSS and JavaScript assets, normally /landing.',
        'optionscode' => 'text',
        'value'       => WEL_ASSET_PATH_NORM,
    ),
    'image_url_path' => array(
        'title_key'       => 'welcome_landing_setting_image_url_path_title',
        'description_key' => 'welcome_landing_setting_image_url_path_desc',
        'title'           => 'Image URL path',
        'description'     => 'URL path relative to the forum root for background images, normally /landing/img/landing.',
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
        "website"       => "https://github.com/ChicagoJohnnyVegas/mybb-welcome-landing",
        "author"        => "\"Chicago\" JohnnyVegas",
        "authorsite"    => "",
        "version"       => "1.32",
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

    foreach (array_keys($GLOBALS['WEL_SETTINGS']) as $key) {
        $name = $db->escape_string(WEL_SETTING_NAME_PREFIX . $key);
        $db->delete_query('settings', "name='{$name}'");
    }

    $gid = welcome_landing_get_setting_group_id();
    if ($gid) {
        $query = $db->simple_select('settings', 'sid', "gid='{$gid}'", array('limit' => 1));
        // Do not orphan unrelated settings added to the plugin's group.
        if (!$db->num_rows($query)) {
            $db->delete_query('settinggroups', "gid='{$gid}'");
        }
    }
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
                        <li class="nav-item"><a class="nav-link" href="{$forgotUrl}">{$forgotText}</a></li>
HTML
        ,
        'nav_item_about' => <<<'HTML'
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="modal" href="#welcome_about">{$aboutLinkText}</a></li>
HTML
        ,
        'login_forgot_link' => <<<'HTML'
                                            <span class="col-md-12 text-end"><a href="{$forgotUrl}">{$forgotText}</a></span>
HTML
        ,
        'login_modal' => <<<'HTML'
        <div class="container">
            <div class="row">
                <div id="welcome_login" tabindex="-1" class="modal fade" aria-labelledby="welcome_login_title">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button class="close" aria-label="{$dismissText}" type="button" data-bs-dismiss="modal"><span aria-hidden="true">x</span></button>
                                <h2 class="modal-title" id="welcome_login_title">{$loginTitle}</h2>
                            </div>

                            <form aria-labelledby="welcome_login_title" id="Form_User_SignIn" action="{$bburl}/member.php?action=do_login" method="post">
                                <input type="hidden" name="wel_do" value="login">
                                <input type="hidden" name="url" value="{$redirectEsc}">
                                <input type="hidden" name="my_post_key" value="{$postCodeEsc}">
                                <input type="hidden" name="remember" value="yes">

                                <div class="modal-body">
                                    <div class="wel-login-error" id="welcome_login_errors" role="alert" tabindex="-1">
                                        {$loginErrorHtml}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>
                                                <label class="visually-hidden" for="txtUserName">{$usernameLabel}</label><input name="username" id="txtUserName" value="{$prefillUsernameEsc}" placeholder="{$usernamePlaceholder}"
                                                       class="form-control" type="text" autocomplete="username">
                                            </p>
                                            <p>
                                                <label class="visually-hidden" for="txtPassword">{$passwordLabel}</label><input name="password" id="txtPassword" placeholder="{$passwordPlaceholder}"
                                                       class="form-control" type="password" autocomplete="current-password">
                                            </p>
                                            {$loginCaptchaHtml}
                                            {$loginForgotLinkHtml}
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">{$closeText}</button>
                                    <input class="btn btn-primary" type="submit" name="submit" value="{$submitText}">
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
                <div id="welcome_about" tabindex="-1" class="modal fade" aria-labelledby="welcome_about_title">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button class="close" aria-label="{$dismissText}" type="button" data-bs-dismiss="modal"><span aria-hidden="true">x</span></button>
                                <h2 class="modal-title" id="welcome_about_title" tabindex="-1">{$aboutTitle}</h2>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        {$aboutHtml}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">{$aboutCloseText}</button>
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
<html id="universe" class="full" lang="{$htmlLanguage}" dir="{$htmlDirection}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <title>{$pageTitle}</title>

    <link href="{$assetBase}/css/{$bootstrapCssFile}" rel="stylesheet">
    <link href="{$assetBase}/css/the-big-picture.css" rel="stylesheet">
    <link href="{$assetBase}/css/imgLoader.css" rel="stylesheet">
    <script src="{$assetBase}/js/jquery.js"></script>
    {$loginHeadHtml}
    <script src="{$assetBase}/js/bootstrap.min.js"></script>
    <script id="wel-modal-startup">
      document.documentElement.classList.add('wel-modals-pending');
      window.welcomeLandingStartupTimer = setTimeout(function () {
        window.welcomeLandingInline = true;
        document.documentElement.classList.remove('wel-modals-pending');
      }, 1500);
    </script>
</head>

<body data-bs-no-jquery>
    <h1 class="visually-hidden">{$pageTitle}</h1>
    <div class="page-container">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-bottom wel-navbar" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="visually-hidden">{$navToggleLabel}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" data-bs-toggle="modal" href="#welcome_login">{$navBrand}</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="navbar-nav">
{$navForgotItemHtml}
{$navAboutItemHtml}
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="offset-md-6 col-md-5 col-12 btn-row">
                    <a id="btnLogin" class="btn btn-primary btn-custom btn-success" data-bs-toggle="modal" href="#welcome_login">
                        {$primaryBtn}
                    </a>
                </div>
            </div>
        </div>

{$loginModalHtml}
{$aboutModalHtml}
    </div>

    <script>
      if (window.jQuery && window.bootstrap && window.bootstrap.Modal) {
      function openLoginModal() {
	      if (window.jQuery && $('#welcome_login').length) {
	          window.bootstrap.Modal.getOrCreateInstance(document.getElementById('welcome_login')).show();
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

        if (typeof window.welcomeLandingSetBackground === 'function') {
          window.welcomeLandingSetBackground(images[idx]);
        } else {
          $(".full").css("background-image", url);
        }
      });

      document.addEventListener('DOMContentLoaded', function () {
	      if ({$openLoginModalJs}) {
	          if (typeof openLoginModal === 'function') openLoginModal();
	      }
	  });

	  $(function () {
	      document.getElementById('welcome_login').addEventListener('hidden.bs.modal', function () {
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
      }
    </script>
    <script src="{$assetBase}/js/welcome-landing.js"></script>

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
            'tid, sid, template',
            "title='" . $db->escape_string($title) . "' AND sid IN (-2,-1)"
        );

        $globalRows = array();
        $legacyRows = array();
        while ($row = $db->fetch_array($query)) {
            if ((int)$row['sid'] === -1) {
                $globalRows[] = $row;
            } else {
                $legacyRows[] = $row;
            }
        }

        // Existing globals win. Ambiguous duplicates need manual review, not merging.
        $rows = $globalRows ? $globalRows : $legacyRows;
        if ($rows) {
            if (count($rows) === 1) {
                $row = $rows[0];
                $tid = (int)$row['tid'];
                $storedTemplate = (string)$row['template'];
                $updates = array();
                if ((int)$row['sid'] === -2) {
                    $updates['sid'] = -1;
                }
                $migratedTemplate = welcome_landing_migrate_template($key, $storedTemplate);

                if ($migratedTemplate !== $storedTemplate) {
                    $updates['template'] = $db->escape_string($migratedTemplate);
                    $updates['dateline'] = $dateline;
                }

                if ($updates) {
                    $db->update_query('templates', $updates, "tid='{$tid}'");
                }
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
    // Ordered, repeatable transforms; no persisted migration version is tracked.
    return array(
        array(
            'key' => 'page_shell',
            'callback' => 'welcome_landing_migrate_page_shell_nav_toggle_label',
        ),
        array(
            'key' => 'login_modal',
            'callback' => 'welcome_landing_migrate_login_modal_native_login',
        ),
        array(
            'key' => 'page_shell',
            'callback' => 'welcome_landing_migrate_page_shell_login_scripts',
        ),
        array(
            'key' => 'page_shell',
            'callback' => 'welcome_landing_migrate_page_shell_availability',
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

    return welcome_landing_migrate_startup(welcome_landing_migrate_accessibility(welcome_landing_migrate_bootstrap5($template)));
}

function welcome_landing_migrate_startup($template)
{
    if (strpos($template, 'id="wel-modal-startup"') !== false) {
        return $template;
    }
    $anchor = '<script src="{$assetBase}/js/bootstrap.min.js"></script>';
    $startup = <<<'HTML'
    <script id="wel-modal-startup">
      document.documentElement.classList.add('wel-modals-pending');
      window.welcomeLandingStartupTimer = setTimeout(function () {
        window.welcomeLandingInline = true;
        document.documentElement.classList.remove('wel-modals-pending');
      }, 1500);
    </script>
HTML;
    return str_replace($anchor, $anchor . "\n" . $startup, $template);
}

function welcome_landing_migrate_accessibility($template)
{
    if (strpos($template, '<h1') === false) {
        $template = str_replace('<body data-bs-no-jquery>', '<body data-bs-no-jquery>' . "\n" . '    <h1 class="visually-hidden">{$pageTitle}</h1>', $template);
    }
    return strtr($template, array(
        'var u = document.querySelector(\'#Form_User_SignIn input[name="username"]\');' => '',
        'if (u) u.focus();' => '',
        '<html id="universe" class="full" lang="en">' => '<html id="universe" class="full" lang="{$htmlLanguage}" dir="{$htmlDirection}">',
        'href="{$assetBase}/css/bootstrap.min.css"' => 'href="{$assetBase}/css/{$bootstrapCssFile}"',
        'id="welcome_login" tabindex="-1" class="modal fade">' => 'id="welcome_login" tabindex="-1" class="modal fade" aria-labelledby="welcome_login_title">',
        'id="welcome_about" tabindex="-1" class="modal fade">' => 'id="welcome_about" tabindex="-1" class="modal fade" aria-labelledby="welcome_about_title">',
        '<h4 class="modal-title">{$loginTitle}</h4>' => '<h2 class="modal-title" id="welcome_login_title">{$loginTitle}</h2>',
        '<h4 class="modal-title">{$aboutTitle}</h4>' => '<h2 class="modal-title" id="welcome_about_title" tabindex="-1">{$aboutTitle}</h2>',
        '<button class="close" aria-hidden="true" type="button" data-bs-dismiss="modal">x</button>' => '<button class="close" aria-label="{$dismissText}" type="button" data-bs-dismiss="modal"><span aria-hidden="true">x</span></button>',
        '<input id="txtUserName" name="username"' => '<label class="visually-hidden" for="txtUserName">{$usernameLabel}</label><input name="username" id="txtUserName"',
        '<input id="txtPassword" name="password"' => '<label class="visually-hidden" for="txtPassword">{$passwordLabel}</label><input name="password" id="txtPassword"',
        '<div class="wel-login-error">' => '<div class="wel-login-error" id="welcome_login_errors" role="alert" tabindex="-1">',
        '<form id="Form_User_SignIn"' => '<form aria-labelledby="welcome_login_title" id="Form_User_SignIn"',
        'data-bs-target="#bs-example-navbar-collapse-1">' => 'data-bs-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false">',
    ));
}

function welcome_landing_migrate_bootstrap5($template)
{
    // Exact legacy tokens only; custom Bootstrap markup still requires admin review.
    return strtr($template, array(
        'data-toggle=' => 'data-bs-toggle=',
        'data-target=' => 'data-bs-target=',
        'data-dismiss=' => 'data-bs-dismiss=',
        '<body>' => '<body data-bs-no-jquery>',
        'class="navbar navbar-inverse navbar-fixed-bottom"' => 'class="navbar navbar-expand-sm navbar-dark bg-dark fixed-bottom wel-navbar"',
        'class="navbar-toggle"' => 'class="navbar-toggler"',
        'class="sr-only"' => 'class="visually-hidden"',
        'class="nav navbar-nav"' => 'class="navbar-nav"',
        '<li><a ' => '<li class="nav-item"><a class="nav-link" ',
        'class="col-md-12 text-right"' => 'class="col-md-12 text-end"',
        'class="btn btn-default"' => 'class="btn btn-outline-secondary"',
        'class="col-md-offset-6 col-md-5 col-sm-12 col-xs-12 input-group input-group-lg btn-row"' => 'class="offset-md-6 col-md-5 col-12 btn-row"',
        'if (window.jQuery && window.jQuery.fn.modal && window.jQuery.fn.modal.Constructor) {' => 'if (window.jQuery && window.bootstrap && window.bootstrap.Modal) {',
        '$(\'#welcome_login\').modal(\'show\');' => 'window.bootstrap.Modal.getOrCreateInstance(document.getElementById(\'welcome_login\')).show();',
        '$(\'#welcome_login\').on(\'hidden.bs.modal\', function () {' => 'document.getElementById(\'welcome_login\').addEventListener(\'hidden.bs.modal\', function () {',
        '    <script src="{$assetBase}/js/jquery.waitforimages.min.js"></script>' => '',
    ));
}

function welcome_landing_migrate_page_shell_nav_toggle_label($template)
{
    if (strpos($template, 'Toggle navigation') === false || strpos($template, '{$navToggleLabel}') !== false) {
        return $template;
    }

    return str_replace('Toggle navigation', '{$navToggleLabel}', $template);
}

function welcome_landing_migrate_login_modal_native_login($template)
{
    $template = str_replace(
        'action="{$bburl}/misc.php?action=welcome"',
        'action="{$bburl}/member.php?action=do_login"',
        $template
    );

    if (strpos($template, '{$loginCaptchaHtml}') === false) {
        $template = str_replace('{$loginForgotLinkHtml}', '{$loginCaptchaHtml}{$loginForgotLinkHtml}', $template);
    }

    return str_replace(
        '<button class="btn btn-primary" type="submit">{$submitText}</button>',
        '<input class="btn btn-primary" type="submit" name="submit" value="{$submitText}">',
        $template
    );
}

function welcome_landing_migrate_page_shell_login_scripts($template)
{
    if (strpos($template, '{$loginHeadHtml}') !== false || substr_count($template, '</head>') !== 1) {
        return $template;
    }

    $scripts = array(
        '<script src="{$assetBase}/js/jquery.js"></script>',
        '<script src="{$assetBase}/js/bootstrap.min.js"></script>',
        '<script src="{$assetBase}/js/jquery.waitforimages.min.js"></script>',
    );

    foreach ($scripts as $script) {
        if (substr_count($template, $script) !== 1) {
            return $template;
        }
    }

    // Native CAPTCHA templates execute scripts as the form is parsed.
    $template = str_replace($scripts, '', $template);
    $headScripts = $scripts[0] . "\n    " . '{$loginHeadHtml}' . "\n    "
        . $scripts[1] . "\n    " . $scripts[2];
    return str_replace('</head>', $headScripts . "\n</head>", $template);
}

function welcome_landing_migrate_page_shell_availability($template)
{
    $template = str_replace('class="full InitialHide"', 'class="full"', $template);
    $oldImageLoad = <<<'HTML'
        $(".full").css("background-image", url).waitForImages({
          waitForAll: true,
          finished: function () {
            $(".InitialHide").fadeIn(2000);
          }
        });
HTML;
    // Compare with normalized newlines so CRLF-stored templates also match.
    $oldImageLoad = str_replace("\r\n", "\n", $oldImageLoad);
    $newImageLoad = '        $(".full").css("background-image", url);';
    $template = str_replace(array($oldImageLoad, str_replace("\n", "\r\n", $oldImageLoad)), $newImageLoad, $template);

    $backgroundFade = <<<'HTML'
        if (typeof window.welcomeLandingSetBackground === 'function') {
          window.welcomeLandingSetBackground(images[idx]);
        } else {
          $(".full").css("background-image", url);
        }
HTML;
    if (strpos($template, 'window.welcomeLandingSetBackground') === false) {
        $template = str_replace($newImageLoad, $backgroundFade, $template);
    }

    $oldButton = <<<'HTML'
                    <button id="btnLogin" class="btn btn-primary btn-custom btn-success" data-toggle="modal" href="#welcome_login" type="button">
                        {$primaryBtn}
                    </button>
HTML;
    $newButton = <<<'HTML'
                    <a id="btnLogin" class="btn btn-primary btn-custom btn-success" data-toggle="modal" href="#welcome_login">
                        {$primaryBtn}
                    </a>
HTML;
    $oldButton = str_replace("\r\n", "\n", $oldButton);
    $template = str_replace(array($oldButton, str_replace("\n", "\r\n", $oldButton)), $newButton, $template);

    // Only guard the recognized legacy inline script; leave custom scripts intact.
    $start = '    <script>' . "\n" . '      function openLoginModal() {';
    $normalized = str_replace("\r\n", "\n", $template);
    $offset = strpos($normalized, $start);
    if ($offset !== false && strpos($normalized, $start, $offset + 1) === false) {
        $end = strpos($normalized, '    </script>', $offset);
        if ($end !== false) {
            $script = substr($normalized, $offset, $end - $offset + strlen('    </script>'));
            $guarded = str_replace('    <script>', '    <script>' . "\n" . '      if (window.jQuery && window.jQuery.fn.modal && window.jQuery.fn.modal.Constructor) {', $script);
            $guarded = str_replace('    </script>', '      }' . "\n" . '    </script>', $guarded);
            $template = str_replace(array($script, str_replace("\n", "\r\n", $script)), $guarded, $template);
        }
    }

    $asset = '<script src="{$assetBase}/js/welcome-landing.js"></script>';
    if (strpos($template, $asset) === false && substr_count($template, '</body>') === 1) {
        $template = str_replace('</body>', '    ' . $asset . "\n</body>", $template);
    }
    return $template;
}

function welcome_landing_delete_templates()
{
    global $db;

    foreach (array_keys(welcome_landing_default_templates()) as $key) {
        $title = WEL_TEMPLATE_PREFIX . $key;
        $db->delete_query('templates', "title='" . $db->escape_string($title) . "' AND sid IN (-2,-1)");
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
    $default = (string)$default;
    if (!welcome_landing_is_safe_redirect_path($default)) {
        $default = WEL_LOGGED_IN_REDIRECT_NORM;
    }
    $redirect = (string)$redirect;

    return welcome_landing_is_safe_redirect_path($redirect) ? $redirect : $default;
}

function welcome_landing_is_safe_redirect_path($path)
{
    if ($path === '' || substr($path, 0, 1) !== '/' || strpos($path, '//') === 0
        || strpos($path, '\\') !== false || preg_match('/[\x00-\x20\x7F]/', $path)
        || preg_match('/%(?:0[0-9a-f]|1[0-9a-f]|7f)/i', $path)) {
        return false;
    }

    // Inspect only the path for traversal/ambiguous separators. Query and
    // fragment escapes are data and must survive the login round trip unchanged.
    $pathname = substr($path, 0, strcspn($path, '?#'));
    if (strpos($pathname, ':') !== false
        || preg_match('/%(?![0-9a-f]{2})|%(?:2f|5c|25)/i', $pathname)) {
        return false;
    }
    $decodedPath = rawurldecode($pathname);
    return !preg_match('#(?:^|/)\.{1,2}(?:/|$)#', $decodedPath);
}

function welcome_landing_welcome_url()
{
    global $mybb;

    return rtrim($mybb->settings['bburl'], '/') . WEL_WELCOME_ENDPOINT_NORM;
}

function welcome_landing_request_to_forum_path($uri)
{
    global $mybb;

    $uri = (string)$uri;
    if (!welcome_landing_is_safe_redirect_path($uri)) {
        return '';
    }

    // REQUEST_URI starts at the website root; settings and form destinations
    // start at the forum root. Strip the configured board prefix exactly once.
    $boardPath = rtrim((string)parse_url($mybb->settings['bburl'], PHP_URL_PATH), '/');
    $pathname = substr($uri, 0, strcspn($uri, '?#'));
    if ($boardPath !== '') {
        if ($pathname === $boardPath) {
            $uri = '/' . substr($uri, strlen($boardPath));
        } elseif (strpos($pathname, $boardPath . '/') === 0) {
            $uri = substr($uri, strlen($boardPath));
        } else {
            return '';
        }
    }

    return welcome_landing_is_safe_redirect_path($uri) ? $uri : '';
}

function welcome_landing_load_language()
{
    global $lang;
    static $loadedContext = null;

    if (isset($lang) && is_object($lang) && method_exists($lang, 'load')) {
        // MyBB includes the public/admin area in language and fallback paths.
        // Keep only the last context so switching away and back reloads it.
        $context = array($lang, $lang->path, $lang->language, $lang->fallback);
        if ($loadedContext !== $context) {
            $lang->load('welcome_landing', false, true);
            $loadedContext = $context;
        }
    }
}

function welcome_landing_load_admin_language()
{
    welcome_landing_load_language();
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

function welcome_landing_has_stored_template($title)
{
    global $db, $theme;
    static $exists = array();

    $templateSet = isset($theme['templateset']) ? (int)$theme['templateset'] : 0;
    $cacheKey = $templateSet . ':' . $title;
    if (!array_key_exists($cacheKey, $exists)) {
        // MyBB caches both a missing row and an intentional blank as ''.
        // Match its eligible template sets, without changing lookup precedence.
        $sets = $templateSet ? "'-2','-1','{$templateSet}'" : "'-2','-1'";
        $query = $db->simple_select(
            'templates',
            'tid',
            "title='" . $db->escape_string($title) . "' AND sid IN ({$sets})",
            array('limit' => 1)
        );
        $exists[$cacheKey] = $db->num_rows($query) > 0;
    }

    return $exists[$cacheKey];
}

function welcome_landing_cache_templates($keys)
{
    global $templates, $theme;

    // MyBB's batch API includes sid 0 without a theme, unlike get() in ACP.
    // Leave that context and nonstandard template engines on the get() path.
    if (empty($theme['templateset']) || !isset($templates) || !is_object($templates)
        || !method_exists($templates, 'cache') || !isset($templates->cache) || !is_array($templates->cache)) {
        return;
    }

    $pending = array();
    // Keys are internal literals only; never pass request values to cache().
    foreach ($keys as $key) {
        $title = WEL_TEMPLATE_PREFIX . $key;
        if (!isset($templates->cache[$title])) {
            $pending[] = $title;
        }
    }
    if ($pending) {
        $templates->cache(implode(',', array_unique($pending)));
    }
}

function welcome_landing_render_template($key, $vars = array())
{
    global $templates;
    static $defaults = null;

    $template = '';
    $usingStoredTemplate = false;
    $title = WEL_TEMPLATE_PREFIX . $key;

    if (isset($templates) && is_object($templates) && method_exists($templates, 'get')) {
        $storedTemplate = $templates->get($title, 1, 0);

        if ($storedTemplate !== false
            && ($storedTemplate !== '' || welcome_landing_has_stored_template($title))) {
            // Preserve MyBB's normal escaping, comments and theme selection.
            $template = $templates->get($title);
            $usingStoredTemplate = true;
        }
    }

    if (!$usingStoredTemplate) {
        if ($defaults === null) {
            $defaults = welcome_landing_default_templates();
        }
        $template = isset($defaults[$key]) ? $defaults[$key] : '';
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
$plugins->add_hook('global_start', 'welcome_landing_redirect_generic_login');
$plugins->add_hook('global_start', 'welcome_landing_guest_gatekeeper');
$plugins->add_hook('member_do_login_start', 'welcome_landing_prepare_native_login');
$plugins->add_hook('member_login_end', 'welcome_landing_present_native_login', 100);

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

    $bburl = rtrim($mybb->settings['bburl'], '/');

    // Preserve POST bodies from already-open forms and unmigrated templates.
    // MyBB's member controller performs the post-key and login checks.
    if ($mybb->request_method === 'post' && $mybb->get_input('wel_do') === 'login') {
        header('Location: ' . $bburl . '/member.php?action=do_login', true, 307);
        exit;
    }

    $redirect = welcome_landing_safe_redirect_path($mybb->get_input('url'), welcome_landing_default_redirect_path());
    $assetBase = $bburl . welcome_landing_setting_path('asset_path', WEL_ASSET_PATH_NORM);


    header("Content-Type: text/html; charset=UTF-8");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");

    echo welcome_landing_render($bburl, $assetBase, $redirect, $mybb->post_code);

    exit;
}

function welcome_landing_prepare_native_login()
{
    global $mybb, $welcome_landing_login_request;

    if ($mybb->get_input('wel_do') !== 'login') {
        return;
    }

    $redirect = welcome_landing_safe_redirect_path($mybb->get_input('url'), welcome_landing_default_redirect_path());
    $welcome_landing_login_request = array('redirect' => $redirect);

    // Core preserves full board URLs; a bare path can lose nested segments.
    $mybb->input['url'] = rtrim($mybb->settings['bburl'], '/') . $redirect;
}

function welcome_landing_present_native_login()
{
    global $mybb, $templates, $inline_errors, $captcha;
    global $welcome_landing_login_request, $welcome_landing_login_page;

    if (empty($welcome_landing_login_request) || !empty($mybb->user['uid'])) {
        return;
    }

    $loginCaptchaHtml = (string)$captcha;
    $loginHeadHtml = '';

    if ($loginCaptchaHtml !== '') {
        welcome_landing_cache_templates(array('login_modal', 'page_shell'));
        $modal = $templates->get(WEL_TEMPLATE_PREFIX . 'login_modal', 0, 0);
        $shell = $templates->get(WEL_TEMPLATE_PREFIX . 'page_shell', 0, 0);

        // Keep MyBB's complete login form if a custom/old template cannot host
        // the native challenge. Never hide a required CAPTCHA to retain styling.
        if (strpos($modal, '{$loginCaptchaHtml}') === false
            || strpos($modal, 'name="submit"') === false
            || strpos($shell, '{$loginHeadHtml}') === false) {
            return;
        }

        if (stripos($loginCaptchaHtml, '<tr') !== false) {
            $loginCaptchaHtml = '<table class="table"><tbody>' . $loginCaptchaHtml . '</tbody></table>';
        }
        $captchaLabelHtml = '';
        if (strpos($loginCaptchaHtml, 'id="imagestring"') !== false && strpos($loginCaptchaHtml, 'for="imagestring"') === false) {
            $captchaLabelHtml = '<label class="visually-hidden" for="imagestring">'
                . htmlspecialchars_uni(welcome_landing_lang('welcome_landing_captcha_label', 'Image verification')) . '</label>';
        }
        $loginCaptchaHtml = '<div class="wel-login-captcha">' . $captchaLabelHtml . $loginCaptchaHtml . '</div>';

        $assetUrl = htmlspecialchars_uni(rtrim($mybb->asset_url, '/'));
        $useAjax = json_encode((string)$mybb->settings['use_xmlhttprequest']);
        // MyBB owns CAPTCHA helpers; Bootstrap uses its native API with its
        // jQuery bridge disabled so the two modal implementations stay separate.
        $loginHeadHtml = '<script src="' . $assetUrl . '/jscripts/jquery.plugins.min.js?ver=1821"></script>'
            . '<script>var lang = window.lang || {}; var use_xmlhttprequest = ' . $useAjax . ';</script>';
    }

    $bburl = rtrim($mybb->settings['bburl'], '/');
    $assetBase = $bburl . welcome_landing_setting_path('asset_path', WEL_ASSET_PATH_NORM);
    $welcome_landing_login_page = welcome_landing_render(
        $bburl,
        $assetBase,
        $welcome_landing_login_request['redirect'],
        $mybb->post_code,
        true,
        $mybb->get_input('username'),
        (string)$inline_errors,
        $loginCaptchaHtml,
        $loginHeadHtml
    );

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    // Replace only this request's presentation; let the member controller and
    // remaining hooks finish normally, including MyBB's output pipeline.
    $templates->cache['member_login'] = '{$welcome_landing_login_page}';
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

    header("Location: " . welcome_landing_welcome_url());
    exit;
}

function welcome_landing_redirect_generic_login()
{
    global $mybb;

    if (!welcome_landing_setting_bool('redirect_generic_login', true)
        || !empty($mybb->user['uid']) || $mybb->request_method !== 'get') {
        return;
    }

    $request = welcome_landing_current_request();
    if (!welcome_landing_request_targets_file($request, 'member.php') || $request['action'] !== 'login') {
        return;
    }

    $redirect = welcome_landing_safe_redirect_path($mybb->get_input('url'), welcome_landing_default_redirect_path());
    header('Location: ' . welcome_landing_welcome_url() . '&url=' . rawurlencode($redirect));
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

        if ($action === '' || in_array($action, $GLOBALS['WEL_ALLOWED_MEMBER_ACTIONS'], true)) return;
    }

    if (welcome_landing_is_public_guest_path($request['path'])) {
        return;
    }

    // Redirect guests to welcome page, preserving destination
    $redirect = welcome_landing_safe_redirect_path(
        welcome_landing_request_to_forum_path($request['uri']),
        welcome_landing_default_redirect_path()
    );
    $dest = rawurlencode($redirect);
    header("Location: " . welcome_landing_welcome_url() . "&url={$dest}");
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
    $path = welcome_landing_request_to_forum_path($path);
    if ($path === '') {
        return false;
    }

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

function welcome_landing_image_json($bburl)
{
    $imageDirRel = welcome_landing_setting_path('image_dir_path', WEL_IMAGE_DIR_REL_NORM);
    $imageUrlRel = welcome_landing_setting_path('image_url_path', WEL_IMAGE_URL_REL_NORM);
    $imagePrefix = welcome_landing_setting_text('image_prefix', WEL_IMAGE_PREFIX);
    $fallbackImage = welcome_landing_setting_text('fallback_image', WEL_FALLBACK_IMAGE);

    $imgDir = rtrim(MYBB_ROOT, '/\\') . $imageDirRel;
    $imgUrlBase = $bburl . $imageUrlRel;

    $images = array();
    $allowedExts = $GLOBALS['WEL_ALLOWED_EXTS'];

    if (is_dir($imgDir)) {
        foreach (glob($imgDir . '/' . $imagePrefix . '*') ?: array() as $file) {
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
    return $imagesJson;
}

function welcome_landing_render($bburl, $assetBase, $redirect, $postCode, $openLoginModal = false, $prefillUsername = '', $loginErrorHtml = '', $loginCaptchaHtml = '', $loginHeadHtml = '')
{
    global $lang;

    $languageSettings = isset($lang->settings) && is_array($lang->settings) ? $lang->settings : array();
    $htmlLanguage = isset($languageSettings['htmllang']) ? str_replace('_', '-', trim((string)$languageSettings['htmllang'])) : 'en';
    if (!preg_match('/\A[a-zA-Z]{1,8}(?:-[a-zA-Z0-9]{1,8})*\z/', $htmlLanguage)) {
        $htmlLanguage = 'en';
    }
    $htmlDirection = !empty($languageSettings['rtl']) ? 'rtl' : 'ltr';
    $bootstrapCssFile = $htmlDirection === 'rtl' ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css';
    $imagesJson = welcome_landing_image_json($bburl);

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
    $usernameLabel = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_username_label', 'Username'));
    $passwordLabel = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_password_label', 'Password'));
    $dismissText = htmlspecialchars_uni(welcome_landing_lang('welcome_landing_dismiss_modal', 'Close'));

    $forgotUrl = $bburl . "/member.php?action=lostpw";

    $openLoginModalJs = $openLoginModal ? 'true' : 'false';
    $prefillUsernameEsc = htmlspecialchars_uni($prefillUsername);

    $templateKeys = array('login_modal', 'page_shell');
    if ($showForgot) {
        $templateKeys[] = 'nav_item_forgot';
        $templateKeys[] = 'login_forgot_link';
    }
    if ($showAbout) {
        $templateKeys[] = 'nav_item_about';
        $templateKeys[] = 'about_modal';
    }
    welcome_landing_cache_templates($templateKeys);

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
        'usernameLabel' => $usernameLabel,
        'passwordLabel' => $passwordLabel,
        'dismissText' => $dismissText,
        'bburl' => $bburl,
        'redirectEsc' => $redirectEsc,
        'postCodeEsc' => $postCodeEsc,
        'loginErrorHtml' => $loginErrorHtml,
        'loginCaptchaHtml' => $loginCaptchaHtml,
        'prefillUsernameEsc' => $prefillUsernameEsc,
        'usernamePlaceholder' => $usernamePlaceholder,
        'passwordPlaceholder' => $passwordPlaceholder,
        'loginForgotLinkHtml' => $loginForgotLinkHtml,
        'closeText' => $closeText,
        'submitText' => $submitText,
    ));

    $aboutModalHtml = $showAbout ? welcome_landing_render_template('about_modal', array(
        'aboutTitle' => $aboutTitle,
        'dismissText' => $dismissText,
        'aboutHtml' => $aboutHtml,
        'aboutCloseText' => $aboutCloseText,
    )) : '';

    return welcome_landing_render_template('page_shell', array(
        'pageTitle' => $pageTitle,
        'htmlLanguage' => htmlspecialchars_uni($htmlLanguage),
        'htmlDirection' => $htmlDirection,
        'bootstrapCssFile' => $bootstrapCssFile,
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
        'loginHeadHtml' => $loginHeadHtml,
    ));
}
