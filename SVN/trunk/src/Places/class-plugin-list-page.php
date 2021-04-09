<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Places;

use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Models\Widgets;
use const Trasweb\Plugins\MyDesktop\MY_DESKTOP_URL;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class Plugin_List
 */
class Plugin_List_Page {

	/**
	 * @var string
	 */
	private const PLUGIN_INIT_FILE = PLUGIN_NAME . '/' . PLUGIN_NAME . '.php';

	/**
	 * Enqueue link to my-desktop page in plugins list page.
	 *
	 * @param array  $settings Custom settings.
	 * @param array  $links
	 * @param string $file
	 *
	 * @filter admin plugin_action_links
	 *
	 * @return array
	 */
	public function show_go_to_desktop_in_plugin_links( array $desktop_link_atts, array $links, string $file ): array
	{
		if ( $file !== self::PLUGIN_INIT_FILE ) {
			return $links;
		}

		if ( Service::get( 'Token' )->is_active() ) {
			return $links;
		}

		if ( !Service::get( 'User' )->is_allowed() ) {
			return $links;
		}

		$desktop_link = sprintf( $desktop_link_atts[ 'format' ], MY_DESKTOP_URL, $desktop_link_atts[ 'label' ] );
		array_unshift( $links, $desktop_link );

		return $links;
	}

	/**
	 * Tasks when  plugin is being disabled :(
	 *
	 * @action admin 'deactivate_' . PLUGIN_ID
	 *
	 * @return void
	 */
	public function disable_plugin()
	{
		Service::get( 'Token' )->remove();
	}

	/**
	 * Tasks of uninstallation  :(
	 *
	 * @action admin 'uninstall_' . PLUGIN_ID
	 *
	 *
	 * @return void
	 */
	public function uninstall_plugin(): void
	{
		if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			( new Widgets() )->delete_all();
		}
	}
}