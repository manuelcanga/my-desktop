<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets\Vendor;

use Trasweb\Plugins\MyDesktop\Models\Widget as Widget_Model;
use Trasweb\Plugins\MyDesktop\Widgets\Widget;
use const Trasweb\Plugins\MyDesktop\MY_DESKTOP_URL;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class Note
 */
class Note extends Widget {

	/**
	 * @var string VIEW Appearance of view.
	 */
	protected const VIEW = 'Widgets/Note/note';

	/**
	 * Show a note widget into desktop.
	 *
	 * @param array $settings Custom settings for widget.
	 * @param array $vars     Custom data for widget.
	 *
	 * @return never-return
	 */
	#[NoReturn]
	public function show( array $settings, array $vars ): void
	{
		$widget = ( new  Widget_Model() )->get_by_widget_id( intval( $vars[ 'my_desktop_widget' ] ?? 0 ) );

		if ( empty( $widget ) ) {
			return;
		}

		$config = $widget[ 'meta_value' ][ 'config' ] ?? [];
		$data = $widget[ 'meta_value' ][ 'data' ] ?? [];
		$color = $config[ 'color' ] ?? '';

		$vars[ 'bgcolor' ] = $settings[ 'colors' ][ $color ] ?? $settings[ 'default_color' ];
		$vars[ 'text' ] = $this->i18n_note( $data[ 'text' ] ?? '' );

		// This will be only in My-Desktop
		@header( 'X-Frame-Options: SAMEORIGIN' );

		echo $this->render( $vars + $settings[ 'vars' ] );
		die();
	}

	/**
	 * I18n of text and replace some vars.
	 *
	 * @param string $text Saved text for note.
	 *
	 * @return string
	 */
	private function i18n_note( string $text ): string
	{
		$text = __( $text, PLUGIN_NAME );

		return str_replace( '{{plugin_url}}', MY_DESKTOP_URL, $text ) ?: '';
	}

	/**
	 * Save note config and note data( written text )
	 *
	 * @param $data
	 *
	 * @return void
	 */
	public function save( $data ): void
	{
		$widget_id = intval( $data[ 'my_desktop_widget' ] ?? 0 );

		if ( empty( $widget_id ) ) {
			return;
		}

		$widget_obj = new  Widget_Model();
		$widget = $widget_obj->get_by_widget_id( $widget_id );

		if ( empty( $widget ) ) {
			return;
		}

		$widget_data = $widget[ 'meta_value' ];

		if ( empty( $widget_data ) ) {
			return;
		}

		if ( empty( $widget_data[ 'config' ][ 'type' ] ) || 'note' !== $widget_data[ 'config' ][ 'type' ] ) {
			return;
		}

		$widget_data[ 'data' ][ 'text' ] = \sanitize_textarea_field( wp_strip_all_tags( stripslashes( $data[ 'text' ] ?? '' ) ) );

		$widget_obj->update( $widget_id, $widget_data );
	}
}