<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop;

use Trasweb\Plugins\MyDesktop\Framework\Hook;

return [
    Hook::new( 'wp_ajax_' . PLUGIN_NAME, [
        'listener' => Framework\Route::class,
        'action'   => 'route_action',
        'config'   => [
            'current_action' => sanitize_user( $_POST[ 'url' ] ?? '' ),
            'base_url'       => admin_url( 'my-desktop' ),
            'base_action'    => PLUGIN_NAME . '/action/',
            'default_action' => 'show-desktop',
        ],
    ] ),
    Hook::new( 'admin_init', [
        'listener' => Places\WP_Admin::class,
        'action'   => 'redirect_to_my_desktop',
    ] ),
    Hook::new( 'admin_print_styles', [
        'listener' => Places\WP_Admin::class,
        'action'   => 'disable',
    ] ),
    Hook::new( 'admin_menu', [
        'listener' => Menus\Konqueror::class,
        'action'   => 'register_page',
    ] ),
    Hook::new( 'admin_menu', [
        'listener' => Menus\Settings_Page::class,
        'action'   => 'register_page',
    ] ),
    Hook::new( 'admin_bar_menu', [
        'listener' => Places\WP_Admin::class,
        'action'   => 'add_link_to_admin_bar',
        'priority' => 40,
        'settings' => [
            'id'    => 'my-desktop',
            'title' => __( 'Switch to your desktop', PLUGIN_NAME ),
            'href'  => MY_DESKTOP_URL,
            'meta'  => [
                'title' => __( 'Switch to your desktop', PLUGIN_NAME ),
            ],
        ],
    ] ),
    Hook::new( 'admin_notices', [
        'listener' => Places\WP_Admin::class,
        'action'   => 'add_admin_notice',
    ] ),
    Hook::new( 'deactivate_' . PLUGIN_ID, [
        'listener' => Places\Plugin_List_Page::class,
        'action'   => 'disable_plugin',
    ] ),
    Hook::new( 'uninstall_' . PLUGIN_ID, [
        'listener' => Places\Plugin_List_Page::class,
        'action'   => 'uninstall_plugin',
    ] ),
    Hook::new( 'plugin_action_links', [
        'listener' => Places\Plugin_List_Page::class,
        'filter'   => 'show_go_to_desktop_in_plugin_links',
        'settings' => [
            'label'  => __( 'Switch to your desktop', PLUGIN_NAME ),
            'format' => '<a href="%s">%s</a>',
        ],
    ] ),
];