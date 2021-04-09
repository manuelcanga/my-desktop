<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Models;

use Trasweb\Plugins\MyDesktop\Framework\Service;

/**
 * Class Widgets. Retrieve widges from current user.
 */
class Widget {
	/**
	 * @var integer
	 */
    private $user_id;

    /**
     * Widgets constructor.
     *
     * @param integer $user_id User which widgets will be retrieved. Default 0
     */
    public function __construct( int $user_id = 0 )
    {
        $this->user_id = $user_id ?: \get_current_user_id();
    }

    /**
     * Retrieve a widget using its widget id.
     *
     * @param integer $widget_id User meta id.
     * @param integer $desktop   Desktop which has the widget.
     *
     * @return array
     */
    public function get_by_widget_id( int $widget_id, int $desktop = 0 )
    {
        $no_widget = [];

        if ( empty( $widget_id ) ) {
            return $no_widget;
        }

        $before_widget = (array)( get_metadata_by_mid( 'user', $widget_id ) ?: [] );
        $widget_meta_key = Service::get( 'Desktop' )->get_widget_meta_key( $desktop );

        // is someone trying modify other meta ?
        if ( ! $before_widget || $widget_meta_key !== $before_widget[ 'meta_key' ] ) {
            return $no_widget;
        }

        return (array)$before_widget;
    }

    /**
     * Remove all user widgets.
     *
     * @return void
     */
    public function remove()
    {
        \delete_user_meta( $this->user_id, Service::get( 'Desktop' )->get_widget_meta_key() );
    }

    /**
     * Add a widget to user. Widget properties will be saed in database like a meta.
     *
     * @param $widget
     *
     * @return void
     */
    public function create( array $widget )
    {
        \add_user_meta( $this->user_id, Service::get( 'Desktop' )->get_widget_meta_key(), $widget, false );
    }

    /**
     * Update widget information.
     *
     * @param integer $widget_id   User meta id.
     * @param array   $widget_atts Array with widget data.
     *
     * @return false|integer
     */
    public function update( int $widget_id, array $widget_atts )
    {
        global $wpdb;

        if ( empty( $widget_atts ) ) {
            return false;
        }

        $set = [ 'meta_value' => \serialize( $widget_atts ) ];
        $where = [ 'umeta_id' => $widget_id, 'user_id' => $this->user_id ];

        return (bool)$wpdb->update( $wpdb->usermeta, $set, $where );
    }
}