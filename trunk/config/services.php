<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop;

use Trasweb\Plugins\MyDesktop\Framework\Service;

use const ABSPATH;

return [
    Service::new( 'Autoload', Framework\Autoload::class, [
        'bootstrap' => My_Desktop::_CLASSES_ . '/Framework/class-autoload.php',
        'config'    => [
            'base_namespace' => __NAMESPACE__,
            'base_dir'       => My_Desktop::_CLASSES_,
        ],
    ] ),
    Service::new( 'Desktop', Models\Desktop::class, [
        'only_once' => true,
        'config'    => [
            'current_desktop' => intval( $_REQUEST[ 'my_desktop_current' ] ?? 0 ),
        ],
    ] ),
    Service::new( 'Token', Framework\Client\Token::class, [
        'only_once' => true,
        'config'    => [
            'project'       => md5( 'my_desktop/' . ABSPATH ),
            'request_token' => sanitize_title( $_REQUEST[ 'my_desktop_token' ] ?? '' ),
        ],
    ] ),
    Service::new( 'User', Framework\Client\User::class, [
        'only_once' => true,
        'config'    => [
            'needed_capability' => 'my_desktop_show_desktop',
        ],
    ] ),
    Service::new( 'Parser', Framework\Parser::class, [
        'config' => [
            'PLUGIN_NAME'      => PLUGIN_NAME,
            'FILTER_SEPARATOR' => '|',
            'DEFAULT_VALUE'    => '',
        ],
    ] ),
    Service::new( 'View', Framework\View::class, [
        'config' => [
            'base_path' => _PLUGIN_ . '/views',
        ],
    ] ),
];