<?php

declare( strict_types = 1 );

use Trasweb\Plugins\MyDesktop\My_Desktop;
use const Trasweb\Plugins\MyDesktop\PLUGIN_ID;

require( __DIR__ . '/src/class-my-desktop.php' );

( new My_Desktop() )();
do_action( 'uninstall_' . PLUGIN_ID );