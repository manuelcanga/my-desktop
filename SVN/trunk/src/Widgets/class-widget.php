<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;
use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Framework\View;

use const Trasweb\Plugins\MyDesktop\MY_DESKTOP_URL;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;
use const Trasweb\Plugins\MyDesktop\PLUGIN_URL;
use const Trasweb\Plugins\MyDesktop\SITE_PATH;

/**
 * Class Widget
 */
abstract class Widget {

    /**
     * @var string VIEW Appearance of view.
     */
    protected const VIEW = '';

    /**
     * @var: string TYPE class of widget.
     */
    protected const TYPE = 'widget';

    /**
     * @var string IMAGE_PATH Path to image directory.
     */
    private const   IMAGE_PATH = '/assets/images/';

    /**
     * Renderize( parse a view and display it ) a widget according to view.
     *
     * @param array $vars Vars used in widget view.
     *
     * @return string Retrieve output of rendering.
     */
    public function render( array $vars = [] ): string
    {
        $vars += $this->get_common_vars();
        $vars[ 'filters' ] = $vars[ 'filters' ] ?? [];
        $vars[ 'filters' ] += $this->get_common_filters();

        $vars = WP_Hooks::apply_filters( PLUGIN_NAME . '/widget/render/vars', $vars, static::TYPE );
        $view = WP_Hooks::apply_filters( PLUGIN_NAME . '/widget/render/view', static::VIEW, self::TYPE, $vars );

	    WP_Hooks::do_action( PLUGIN_NAME . '/widget/render', $vars, self::TYPE );

        return View::get( $view, $vars );
    }

    /**
     * Retrieve common vars for all widgets.
     *
     * @return array
     */
    private function get_common_vars(): array
    {
        return [
            'lang'              => substr( get_locale(), 0, 2 ),
            'plugin_name'       => PLUGIN_NAME,
            'plugin_url'        => PLUGIN_URL,
            'ajax_url'          => esc_js( admin_url( 'admin-ajax.php', 'relative' ) ),
            'site_name'         => get_bloginfo( 'name' ),
            'site_url'          => site_url(),
            'my_desktop_url'    => MY_DESKTOP_URL,
            'site_path'         => SITE_PATH,
            'current_user_name' => wp_get_current_user()->display_name,
            'my_desktop_token'  => Service::get( 'Token' )->get(),
        ];
    }

    /**
     * Retrieve common filters for all widgets.
     *
     * @return array
     */
    private function get_common_filters(): array
    {
        $filters = [];

        $filters[ 'image_url' ] = static function ( string $image_file ) {
            return PLUGIN_URL . self::IMAGE_PATH . $image_file;
        };

        $filters[ 'base_url' ] = static function ( string $widget_url ) {
            if ( '/' === $widget_url[ 0 ] ) {
                return SITE_PATH . $widget_url;
            }

            $widget_url = str_replace( '{{home_url}}', home_url(), $widget_url );

            return $widget_url;
        };

        $generated_token = Service::get( 'Token' )->get();
        $filters[ 'tokenize_url' ] = static function ( string $url ) use ( $generated_token ) {
            // Only internal urls have tokens
            if ( 0 !== strpos( $url, SITE_PATH . '/wp-admin' ) ) {
                return $url;
            }

            if ( false !== strpos( $url, '?' ) ) {
                return $url . '&my_desktop_token=' . $generated_token;
            }

            return $url . '?my_desktop_token=' . $generated_token;
        };

        return $filters;
    }
}