<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets\Sticky;

use Trasweb\Plugins\MyDesktop\Models\Widget as Widget_Model;
use Trasweb\Plugins\MyDesktop\Widgets\Widget;

/**
 * Class Sticky. A sticky widget is one which has width/height, pos_x/pos_y attributes.
 */
abstract class Sticky extends Widget {

    /**
     * @var: string TYPE class of widget.
     */
    protected const TYPE = 'sticky';

    /**
     * Save widget location.
     *
     * @param array   $widget_localization New widget position/size.
     * @param integer $user_id             Widget owner.
     *
     * @return boolean
     */
    public function save_localization( array $widget_localization, int $user_id = 0 ): bool
    {
        $widget_id = (int)( $widget_localization[ 'id' ] ?? 0 );
        $user_id = $user_id ?: \get_current_user_id();

        $saved_widget = ( new Widget_Model( $user_id ) )->get_by_widget_id( $widget_id );

        if ( empty( $saved_widget ) ) {
            return false;
        }

        $widget = $this->sanitize_widget( $widget_localization, $saved_widget[ 'meta_value' ] );

        return ( new Widget_Model( $user_id ) )->update( $widget_id, $widget );
    }

    /**
     * Check if widget data are right.
     *
     * @param array $widget_data  New widget data to save.
     * @param array $saved_widget Currently widget data.
     *
     * @return array
     */
    private function sanitize_widget( array $widget_data, array $saved_widget ): array
    {
        $widget = $saved_widget;

        // Not the same widget.
        if ( $widget[ 'title' ] !== $widget_data[ 'title' ] ) {
            return $no_valid_widget = [];
        }

        // Check atts.
        $widget[ 'posx' ] = $this->sanitize_atts( $widget_data[ 'posx' ], $saved_widget[ 'posx' ] );
        $widget[ 'posy' ] = $this->sanitize_atts( $widget_data[ 'posy' ], $saved_widget[ 'posy' ] );
        $widget[ 'width' ] = $this->sanitize_atts( $widget_data[ 'width' ], $saved_widget[ 'width' ] );
        $widget[ 'height' ] = $this->sanitize_atts( $widget_data[ 'height' ], $saved_widget[ 'height' ] );

        return $widget;
    }

    /**
     * Helper: Sanitize posx|posy|width|height of a widget
     *
     * @param string $size          posx|posy|width|height with unit( % or px ).
     * @param string $default_value
     *
     * @return string
     */
    private function sanitize_atts( string $size, string $default_value ): string
    {
        $amount = (float)$size;

        if ( "{$amount}px" !== $size && "{$amount}%" !== $size && "{$amount}em" !== $size ) {
            return $default_value;
        }

        return $size;
    }
}