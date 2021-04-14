<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets;

use Trasweb\Plugins\MyDesktop\Framework\Client\User_Agent;
use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;
use Trasweb\Plugins\MyDesktop\Models\Widgets;

use const Trasweb\Plugins\MyDesktop\MY_DESKTOP_URL;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;
use const Trasweb\Plugins\MyDesktop\PLUGIN_URL;

/**
 * Class Desktop. Desktop widget
 */
class Desktop extends Widget {

    /**
     * @var string VIEW Appearance of view.
     */
    protected const VIEW = 'Widgets/Desktop/desktop';

    /**
     * @var: string TYPE class of widget.
     */
    protected const TYPE = 'desktop';

    /**
     * @var array JS_ASSETS Javascript asset list used in desktop.
     */
    private const   JS_ASSETS     = [
        PLUGIN_URL . '/assets/js/base/trasweb.js',
        PLUGIN_URL . '/assets/js/base/drag_drop.min.js',
        PLUGIN_URL . '/assets/js/desktop/jquery.window.js',
        // PLUGIN_URL . '/assets/js/widgets/abstract.widget.js',
        PLUGIN_URL . '/assets/js/widgets/iframe.widget.js',
        PLUGIN_URL . '/assets/js/widgets/icon.widget.js',
        PLUGIN_URL . '/assets/js/desktop/panel.js',
        PLUGIN_URL . '/assets/js/desktop/desktop.js',
        PLUGIN_URL . '/assets/js/my_desktop.js',
    ];

    private const   JQUERY_ASSETS = [
        'jquery'               => '/js/jquery/jquery.js',
        'jquery-core'          => '/js/jquery/ui/core.min.js',
        'jquery-ui-widget'     => '/js/jquery/ui/widget.min.js',
        'jquery-ui-mouse'      => '/js/jquery/ui/mouse.min.js',
        'jquery-ui-selectable' => '/js/jquery/ui/selectable.min.js',
        'jquery-ui-draggable'  => '/js/jquery/ui/draggable.min.js',
        'jquery-ui-resizable'  => '/js/jquery/ui/resizable.min.js',
    ];

    /**
     * @var array CSS_ASSETS CSS asset list used in desktop.
     */
    protected const CSS_ASSETS = [
        PLUGIN_URL . '/assets/css/commons.css',
        PLUGIN_URL . '/assets/css/jquery-ui-1.10.4.custom.min.css',
        PLUGIN_URL . '/assets/css/webtop.css',
        PLUGIN_URL . '/assets/css/jquery.window.css',
    ];

    /**
     * Show desktop which is base for others widgets.
     *
     * @return never-return
     */
    // [NoReturn]
    public function show(): void
    {
        Service::get( 'Token' )->generate();

        $widget_list = ( new Widgets() )->get();

        $vars = [
            'weekdays'           => $this->get_weekdays(),
            'months'             => $this->get_months(),
            'webtop_widget_list' => $widget_list[ 'webtop' ] ?? [],
            'js_assets'          => WP_Hooks::apply_filters( PLUGIN_NAME . '/js_assets', $this->get_javascripts() ),
            'css_assets'         => WP_Hooks::apply_filters( PLUGIN_NAME . '/css_assets', self::CSS_ASSETS ),
            'body_class'         => implode( ' ', $this->get_body_class() ),
            'restore_link'       => MY_DESKTOP_URL . '/restore',
        ];

        // This will be alway main frame.
        @header( 'X-Frame-Options: DENY' );

        echo $this->render( $vars );
        die();
    }

    /**
     * Retrieve javascripts need for my-desktop.
     *
     * @return array
     */
    private function get_javascripts(): array
    {
        $jquery_assets = array_map( 'includes_url', self::JQUERY_ASSETS );

        return array_merge( $jquery_assets, self::JS_ASSETS );
    }

    /**
     * Retrieve localized months.
     *
     * @return string;
     */
    private function get_months(): string
    {
        // Remove zeros keys
        $months = array_flip( $GLOBALS[ 'month' ] );
        $months = array_map( 'intval', $months );
        $months = array_flip( $months );

        // Sort keys
        ksort( $months, SORT_NUMERIC );

        return json_encode( $months );
    }

    /**
     * Retrieve localized weekdays.
     *
     * @return string;
     */
    private function get_weekdays(): string
    {
        return json_encode( array_map( 'ucfirst', array_values( $GLOBALS[ 'weekday' ] ) ) );
    }

    /**
     * Retrieve HTML classes in body tag.
     *
     * @return array
     */
    private function get_body_class(): array
    {
        $classes = get_body_class();

        $user_agent = ( new User_Agent() )->check();

        $classes[] = $user_agent[ 'navigator' ] . '_desktop';
        $classes[] = $user_agent[ 'device' ] . '_desktop';

        return $classes;
    }

    /**
     * Switch to wp-admin.
     *
     * @return never-return
     */
    // [NoReturn]
    public function switch(): void
    {
        Service::get( 'Token' )->remove();
        wp_redirect( admin_url() );
        die();
    }

    /**
     * Logout of current user
     *
     * @return never-return
     */
    // [NoReturn]
    public function logout(): void
    {
        Service::get( 'Token' )->remove();
        wp_logout();
        wp_redirect( site_url() );
        die();
    }

    /**
     * Restore current desktop
     *
     * @return void
     */
    public function restore(): void
    {
        if ( ! Service::get( 'Token' )->is_OK() ) {
            return;
        }

        ( new Widgets() )->restore_defaults();
        wp_redirect( MY_DESKTOP_URL );
        die();
    }
}