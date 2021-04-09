<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Mocks;

/**
 * Class WP_Pages
 */
class WP_Pages {
    /**
     * Add a page to admin menu.
     *
     * @param array $page_attrs
     *
     * @return string
     */
    public static function add_menu_page( array $page_attrs ): ?string
    {
        return add_menu_page( ...array_values( $page_attrs ) );
    }
}