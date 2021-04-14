<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Mocks;

/**
 * Class WP_User
 */
class WP_User {
    /**
     * Retrieve if current user is admin or not.
     *
     * @return boolean
     */
    public static function is_admin(): bool
    {
        return current_user_can( 'administrator' );
    }

    /**
     * Retrieve if current user has a capability.
     *
     * @param string $capability
     *
     * @return boolean
     */
    public static function current_user_can( string $capability ): bool
    {
        return current_user_can( $capability );
    }
}