<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Models;

use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class Settings. Manage plugin settings
 */
class Settings {

    /**
     * @var string
     */
    private const OPTION = 'sites';

    /**
     * Register plugin settings.
     *
     * @param $settings
     *
     * @return void
     */
    public function register( array $settings ): void
    {
        foreach ( $settings as $name => $config ) {
            register_setting( PLUGIN_NAME, $this->get_option_name( $name ), $config );
        }
    }

    /**
     * Retrieve sites from settings.
     *
     * @return array<string, array>
     */
    public function get_sites(): array
    {
        return $this->get( self::OPTION, [] ) ?: [];
    }

    /**
     * Helper: Retrieve value of a setting by its name.
     *
     * @param string $setting_name Setting name whose value is returned.
     * @param string $default      Default value if setting is not found.
     *
     * @return mixed|void
     */
    private function get( string $setting_name, $default = '' )
    {
        return get_option( $this->get_option_name( $setting_name ), $default );
    }

    /**
     * Helper: Make up option name from a prefix( plugin name ) and setting name.
     *
     * @param string $setting_name
     *
     * @return string
     */
    private function get_option_name( string $setting_name ): string
    {
        return PLUGIN_NAME . '-' . $setting_name;
    }
}
