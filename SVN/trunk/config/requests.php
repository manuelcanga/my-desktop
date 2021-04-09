<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop;

use Trasweb\Plugins\MyDesktop\Framework\Hook;
use Trasweb\Plugins\MyDesktop\Widgets;

return [
    Hook::new( 'do_parse_request', [
        'listener' => Framework\Route::class,
        'filter'   => 'route_request',
        'config'  => [
            'base_url'       => admin_url( 'my-desktop' ),
            'base_action'    => PLUGIN_NAME . '/request/',
            'default_action' => 'show-desktop',
        ],
    ] ),
    Hook::new( PLUGIN_NAME . '/request/show-desktop', [
        'listener' => Widgets\Desktop::class,
        'action'   => 'show',
    ] ),
    Hook::new( PLUGIN_NAME . '/request/switch', [
        'listener' => Widgets\Desktop::class,
        'action'   => 'switch',
    ] ),
    Hook::new( PLUGIN_NAME . '/request/logout', [
        'listener' => Widgets\Desktop::class,
        'action'   => 'logout',
    ] ),
    Hook::new( PLUGIN_NAME . '/request/restore', [
        'listener' => Widgets\Desktop::class,
        'action'   => 'restore',
    ] ),
    Hook::new( PLUGIN_NAME . '/request/note', [
        'listener' => Widgets\Vendor\Note::class,
        'action'   => 'show',
        'settings' => [
	        'colors'        => ['green' => '#76e9be'],
	        'default_color' => '#f9e9c5',
	        'vars' => [
	        	'saving_time'  => 1 * 1000, /* 1 seconds */
	        ]
        ],
    ] ),
    Hook::new( PLUGIN_NAME . '/action/widget/note-save', [
        'listener' => Widgets\Vendor\Note::class,
        'action'   => 'save',
    ] ),
    Hook::new( PLUGIN_NAME . '/widget/icon', [
        'listener' => Widgets\Sticky\Icon::class,
        'action'   => 'show',
    ] ),
    Hook::new( PLUGIN_NAME . '/widget/icon', [
        'listener' => Widgets\Sticky\Icon::class,
        'action'   => 'show',
    ] ),
    Hook::new( PLUGIN_NAME . '/action/widget/icon-save', [
        'listener' => Widgets\Sticky\Icon::class,
        'action'   => 'save_localization',
    ] ),
    Hook::new( PLUGIN_NAME . '/widget/iframe', [
        'listener' => Widgets\Sticky\Iframe::class,
        'action'   => 'show',
    ] ),
    Hook::new( PLUGIN_NAME . '/action/widget/iframe-save', [
        'listener' => Widgets\Sticky\Iframe::class,
        'action'   => 'save_localization',
    ] ),
];
