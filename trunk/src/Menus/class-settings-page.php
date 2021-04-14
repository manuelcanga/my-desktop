<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Menus;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Pages;
use Trasweb\Plugins\MyDesktop\Mocks\WP_User;

use const Trasweb\Plugins\MyDesktop\_PLUGIN_;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class Settings
 *
 * Show Desktop Setting menu and page.
 */
class Settings_Page {

    /**
     * @var string
     */
    private const PAGE_TITLE = 'Desktop Settings';

    /**
     * @var string
     */
    private const MENU_SLUG = PLUGIN_NAME;

    /**
     * @var string
     */
    private const VIEW_FILE = _PLUGIN_ . '/views/Menus/settings.php';

    /**
     * Register Setting menu.
     *
     * @action admin admin_menu
     *
     * @return void
     */
    public function register_page(): void
    {
        $capability = WP_User::is_admin() ? 'manage_options' : 'my_desktop_settings';

        $new_page = [
            'page_title' => __( self::PAGE_TITLE, PLUGIN_NAME ),
            'menu_title' => __( self::PAGE_TITLE, PLUGIN_NAME ),
            'capability' => $capability,
            'menu_slug'  => self::MENU_SLUG,
            'display'    => [ $this, 'show' ],
            'icon'       => 'dashicons-welcome-view-site',
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
        include self::VIEW_FILE;
    }
}