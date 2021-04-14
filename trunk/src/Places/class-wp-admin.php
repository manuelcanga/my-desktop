<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Places;

use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Framework\View;
use WP_Admin_Bar;

use const Trasweb\Plugins\MyDesktop\MY_DESKTOP_URL;

/**
 * Class WP_Admin: Disable Admin bar, WP Footer and others.
 */
class WP_Admin {
    /**
     * Redirect to my desktop in order to avoid showing wp-admin without menu.
     *
     * @action admin admin_init
     *
     * @return void
     */
    public function redirect_to_my_desktop(): void
    {
        if ( ! $this->is_wp_admin_request() ) {
            return;
        }

        if ( \admin_url() !== \get_self_link() && \admin_url() . 'index.php' !== \get_self_link() ) {
            return;
        }

        \wp_redirect( MY_DESKTOP_URL );
    }

    /**
     * Add styles in order to hide some WP Admin elements.
     *
     * @action admin admin_print_styles
     *
     * @return void
     */
    public function disable(): void
    {
        if ( ! $this->is_wp_admin_request() ) {
            return;
        }

        echo View::get( 'Places/wp-admin', [] );
    }

    /**
     * Add a link to admin bar.
     *
     * @param array        $link_settings Custom settings for admin bar link.
     * @param WP_Admin_Bar $admin_bar
     *
     * @action admin admin_bar_menu
     *
     * @return void
     */
    public function add_link_to_admin_bar( array $link_settings, WP_Admin_Bar $admin_bar ): void
    {
        $admin_bar->add_menu( $link_settings );
    }

    /**
     * Add a notice in each admin page
     *
     * @action admin admin_notices
     *
     */
    public function add_admin_notice(): void
    {
        if ( Service::get( 'Token' )->is_active() ) {
            return;
        }

        $vars[ 'my_desktop_url' ] = MY_DESKTOP_URL;

        echo View::get( 'Places/wp-admin_message', $vars );
    }

    /**
     * Retrieve if current request is to wp_admin.
     *
     * @return boolean
     */
    private function is_wp_admin_request(): bool
    {
        if ( ! is_admin() && \wp_doing_ajax() ) {
            return false;
        }

        if ( ! Service::get( 'Token' )->is_active() ) {
            return false;
        }

        return true;
    }
}