<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Menus;

use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Mocks\WP_Pages;
use Trasweb\Plugins\MyDesktop\Mocks\WP_User;

use const Trasweb\Plugins\MyDesktop\_PLUGIN_;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Show Konqueror menu viewer.
 */
class Konqueror {

    /**
     * @var string
     */
    private const PAGE_TITLE = 'Konqueror';

    /**
     * @var string
     */
    private const MENU_SLUG = PLUGIN_NAME . '-konqueror';

    /**
     * @var string
     */
    private const VIEW_FILE = _PLUGIN_ . '/views/Menus/konqueror.php';

    /**
     * Register Konqueror menu.
     *
     * @action admin admin_menu
     *
     * @return void
     */
    public function register_page(): void
    {
        if ( ! Service::get( 'Token' )->is_active() ) {
            return;
        }

        $capability = WP_User::is_admin() ? 'manage_options' : 'my_desktop_show_konqueror';

        $new_page = [
            'page_title' => __( self::PAGE_TITLE, PLUGIN_NAME ),
            'menu_title' => __( self::PAGE_TITLE, PLUGIN_NAME ),
            'capability' => $capability,
            'menu_slug'  => self::MENU_SLUG,
            'display'    => [ $this, 'show' ],
        ];

        WP_Pages::add_menu_page( $new_page );
    }

    /**
     * Show konqueror.
     *
     * @callback
     *
     * @return
     */
    public function show(): void
    {
        $konqueror_url = admin_url( '?page=my-desktop-konqueror' );
        $active_menu = \sanitize_user( $_GET[ 'active' ] ?? '', true );
        $_plugin_ = _PLUGIN_;

        include self::VIEW_FILE;
    }
}