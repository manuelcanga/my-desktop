<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;

/**
 * Class Route. Convert suburl( from web request or ajax request ) with a target base, to actions.
 *
 * @example  Request: https://my.site/my-plugin/custom_action  => do_action('my.site/request/custom_action');
 * @example  Request ajx: https://my.site/wp-admin/ajax.php?url=custom_action  => do_action('my.site/action/custom_action');
 */
class Route {

    /** @var string */
    private $current_action;

    /**
     * @var string
     */
    private $base_url;

    /**
     * @var string
     */
    private $base_action;

    /**
     * @var string
     */
    private $default_action;

    /**
     * Named constructor.
     *
     * @param array $config
     */
    public function configure( array $config )
    {
        $this->current_action = $config[ 'current_action' ] ?? '';
        $this->base_url = $config[ 'base_url' ];
        $this->base_action = $config[ 'base_action' ];
        $this->default_action = $config[ 'default_action' ];
    }

    /**
     * Route a web request to my-desktop.
     *
     * @param boolean $rewrite_rule_are_processed
     *
     * @return bool
     */
    public function route_request( bool $rewrite_rule_are_processed ): bool
    {
        $current_url = get_self_link();

        if ( ! $this->is_app_url( $current_url ) ) {
            return $rewrite_rule_are_processed;
        }

        $action = $this->parse_url( $current_url );

        $action_id = $this->base_action . $action[ 'action_name' ];

        if ( ! has_action( $action_id ) ) {
            return $rewrite_rule_are_processed;
        }

        WP_Hooks::do_action( $action_id, $action[ 'action_args' ] );
        WP_Hooks::do_action( $this->base_action, $action[ 'action_args' ] );

        return $rewrite_rule_are_processed;
    }

    /**
     * Route an action request( ajax request )
     *
     * @return void
     */
    public function route_action(): void
    {
        if ( empty( $this->current_action ) ) {
            return;
        }

        unset( $_POST[ 'url' ] );
        unset( $_POST[ 'action' ] );

        // Check token
        if ( ! Service::get( 'Token', __DIR__ )->is_OK() ) {
            return;
        }

        $action_name = $this->sanitize_action( $this->current_action );
        $action_id = $this->base_action . $action_name;

        WP_Hooks::do_action( $action_id, $_POST );
        WP_Hooks::do_action( $this->base_action, $_POST );

        exit();
    }

    /**
     * Check if current url is a request to my-desktop.
     *
     * @param string $url
     *
     * @return boolean
     */
    private function is_app_url( string $url ): bool
    {
        return 0 === strpos( $url, $this->base_url );
    }

    /**
     * Helper: Parse url in order to know which action must be launched.
     *
     * @param string $url
     *
     * @return array
     */
    private function parse_url( string $url ): array
    {
        $action_args = [];

        $current_action_url = substr( $url, strlen( $this->base_url ) );

        $action_name = $this->sanitize_action( strtok( " $current_action_url", '?' ) ) ?: $this->default_action;
        $query_string = trim( strtok( '' ) ?: '' );
        \parse_str( $query_string, $action_args );

        return compact( 'action_name', 'action_args' );
    }

    /***
     * Helper: Sanitize action
     *
     * @param string $current_app_suburl
     *
     * @return string
     */
    private function sanitize_action( string $current_app_suburl ): string
    {
        $key = trim( $current_app_suburl, ' /' );
        $keys = explode( '/', $key );
        $keys = array_map( 'sanitize_key', $keys );

        return implode( '/', $keys );
    }
}