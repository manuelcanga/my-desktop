<?php

declare( strict_types = 1 );

/*
 * Plugin Name: My Desktop
 * Plugin URI: https://github.com/manuelcanga/my-desktop
 * Description: Manage your site from a web desktop. Do multitasking with  windows, click icons, use notes or jump to any place in your wp-admin quickly. Only switch to wp-admin/my-desktop.
 * Version: 0.1
 * Author: Manuel Canga
 * Author URI: https://manuelcanga.dev
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: my-desktop
 * Domain Path: /languages
 */

use Trasweb\Plugins\MyDesktop\My_Desktop;

if ( !defined( 'ABSPATH' ) ) {
	die( 'Hello, World!' );
}

( static function () {
	include __DIR__ . '/src/class-my-desktop.php';

	add_action( 'init', new My_Desktop() );
} )();