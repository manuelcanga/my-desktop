<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Models;

use Trasweb\Plugins\MyDesktop\Framework\Service;

use const ARRAY_A;

/**
 * Class Widgets
 */
class Widgets {

    /**
     * @var string In the future, My Desktop will support multiple widget places: panel, menu, webtop, ...
     */
    private const DEFAULT_PLACE = 'webtop';

    /**
     * @var string
     */
    private const QUERY_WIDGETS = <<<QUERY
			SELECT umeta_id, meta_value 
			FROM %s 
			WHERE meta_key = "%s" and user_id = %d 
			ORDER BY umeta_id ASC
QUERY;

    /**
     * @var integer
     */
    private $user_id;

    /**
     * @var string
     */
    private $widgets;

    /**
     * Widgets constructor.
     *
     * @param integer $user_id User which widgets will be retrieved. Default 0.
     */
    public function __construct( int $user_id = 0 )
    {
        $this->user_id = $user_id ?: \get_current_user_id();
        $this->widgets = $this->search_user_widgets();
    }

    /**
     * Search by user widgets
     *
     * @return array
     */
    public function search_user_widgets(): array
    {
        $widgets = [];
        foreach ( $this->query_to_database() as $widget_atts ) {
            $widget_place = $widget_atts[ 'place' ] ?? self::DEFAULT_PLACE;
            $default_atts = [ 'id' => $widget_atts[ 'umeta_id' ] ];
            $widgets[ $widget_place ][] = maybe_unserialize( $widget_atts[ 'meta_value' ] ) + $default_atts;
        }

        return $widgets;
    }

    /**
     * launch query to database and retrieve results
     *
     * @return array
     */
    private function query_to_database(): array
    {
        global $wpdb;

        $widget_meta_key = Service::get( 'Desktop' )->get_widget_meta_key();

        $query = sprintf( self::QUERY_WIDGETS, $wpdb->usermeta, $widget_meta_key, $this->user_id );

        return $wpdb->get_results( $query, ARRAY_A ) ?: [];
    }

    /**
     * Retrieve widgets from  repository
     *
     * @return array
     */
    public function get(): array
    {
        if ( empty( $this->widgets ) ) {
            $this->restore_defaults();
            $this->widgets = $this->search_user_widgets();
        }

        return $this->widgets;
    }

    /**
     * Generate default widgets for user.
     *
     * @return void
     */
    public function restore_defaults(): void
    {
        $widget_rows = Config::widgets();

        ( new Widget( $this->user_id ) )->remove();

        foreach ( $widget_rows as $widget_title => $widget_atts ) {
            $default_atts = [ 'title' => $widget_title ];
            ( new Widget( $this->user_id ) )->create( $widget_atts + $default_atts );
        }
    }

    /**
     * Remove all widgets for all users. Useful when plugin is uninstalled.
     *
     * @return void
     */
    public function delete_all(): void
    {
        global $wpdb;

	    $widget_meta_key = Service::get( 'Desktop' )->get_widget_meta_key();
        $wpdb->delete( $wpdb->usermeta, [ 'meta_key' => $widget_meta_key ] );
    }
}