<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework\Client;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;

/**
 * Class User_Agent. Retrieve user agent information.
 */
final class User_Agent {

    /**
     * @var bool
     */
    private $is_mobile = false;

    /**
     * @var bool
     */
    private $is_computer = false;

    /**
     * @var bool
     */
    private $is_tablet = false;

    /**
     * @var bool
     */
    private $is_bot = false;

    /**
     * @var string
     */
    private $device = 'computer';

    /**
     * @var string
     */
    private $navigator = 'explorer';

    /**
     * @var bool
     */
    private $is_android = false;

    /**
     * Get information about user agent.
     *
     * @param string  $key             Only retrieve $key field of user agent information.
     * @param boolean $must_recheck    Forcing a check of user agent again.
     * @param string  $http_user_agent Specific user agent string to check.
     *
     * @return array
     *
     */
    public function check( string $key = '', bool $must_recheck = false, string $http_user_agent = '' ): array
    {
        static $user_agent;

        if ( ! $must_recheck && isset( $user_agent ) ) {
            return $key ? $user_agent[ $key ] : $user_agent;
        }

        $http_user_agent = $http_user_agent ?: $_SERVER[ 'HTTP_USER_AGENT' ];

        if ( empty( $http_user_agent ) ) {
            return $this->get_user_agent_information( $key );
        }

        $this->is_mobile = strpos( $http_user_agent, 'Mobile' ) !== false;
        $this->is_android = strpos( $http_user_agent, 'Android' ) !== false;

        if ( $this->is_tablet( $http_user_agent ) ) {
            $this->is_tablet = true;
            $this->device = $this->navigator = 'tablet';
        }

        if ( $this->is_mobile( $http_user_agent ) ) {
            $this->is_mobile = true;
            $this->device = $this->navigator = 'mobile';
        }

        // ¿is desktop?
        if ( ! $this->is_mobile && ! $this->is_tablet ) {
            $this->is_computer = true;
            if ( strpos( $http_user_agent, 'Chrome' ) !== false ) {
                $this->navigator = 'chrome';
            } elseif ( strpos( $http_user_agent, 'Firefox' ) !== false ) {
                $this->navigator = 'firefox';
            } else {
                $this->navigator = 'explorer';
            }

            $this->is_bot = strpos( $http_user_agent, 'bot' ) !== false;
        }

        return $this->get_user_agent_information( $key );
    }

    /**
     * Retrieve information about user agent.
     *
     * @param string $key Specific information to retrieve.
     *
     * @return mixed
     */
    private function get_user_agent_information( string $key = '' )
    {
        $user_agent = [
            'navigator' => $this->navigator,
            'device'    => $this->device,
            'bot'       => $this->is_bot,
            'computer'  => $this->is_computer,
            'mobile'    => $this->is_mobile,
            'tablet'    => $this->is_tablet,
            'desktop'   => ( $this->is_computer || $this->is_tablet ),
        ];

        $user_agent = WP_Hooks::apply_filters( '/Trasweb/user_agent', $user_agent );

        return $key ? $user_agent[ $key ] : $user_agent;
    }

    /**
     * Retrieve if user agent( from $http_user_agent string ) is tablet or not.
     *
     * @param string $http_user_agent
     *
     * @return boolean
     */
    private function is_tablet( string $http_user_agent ): bool
    {
        $is_tablet = stripos( $http_user_agent, 'Tablet' ) !== false;
        $is_android_tablet = $this->is_android && ! $this->is_mobile;
        $is_kindle = strpos( $http_user_agent, 'Kindle' ) !== false;
        $is_ipad = strpos( $http_user_agent, 'iPad' ) !== false;

        return ( $is_tablet || $is_android_tablet || $is_android_tablet || $is_kindle || $is_ipad );
    }

    /**
     * Retrieve if user agent( from $http_user_agent string ) is mobile or not.
     *
     * @param string $http_user_agent
     *
     * @return boolean
     */
    private function is_mobile( string $http_user_agent ): bool
    {
        if ( $this->is_tablet ) {
            return false;
        }

        $is_silk = strpos( $http_user_agent, 'Silk/' ) !== false;
        $is_black_berry = strpos( $http_user_agent, 'BlackBerry' ) !== false;
        $is_opera_mini = strpos( $http_user_agent, 'Opera Mini' ) !== false;
        $is_opera_moby = strpos( $http_user_agent, 'Opera Mobi' ) !== false;

        return ( $this->is_mobile || $is_silk || $is_black_berry || $is_opera_mini || $is_opera_moby );
    }
}