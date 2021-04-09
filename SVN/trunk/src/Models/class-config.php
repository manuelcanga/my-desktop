<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Models;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;
use const Trasweb\Plugins\MyDesktop\_PLUGIN_;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class Config  retrieve data from config files.
 */
class Config {

	/**
	 * @var string
	 */
	private const PATH = _PLUGIN_ . '/config';

	/**
	 * Retrieve deault widgets from config
	 *
	 * @param string $widget_config_file File where default widgets are stored.
	 *
	 * @return array<string, array>
	 */
	public static function widgets( string $widget_config_file = self::PATH . '/widgets.ini' ): array
	{
		$sites_list = \parse_ini_file( $widget_config_file, $with_sections = true );

		return WP_Hooks::apply_filters( PLUGIN_NAME . '/widgets', $sites_list ) ?: [];
	}

	/**
	 * Retrieve plugin hooks from config
	 *
	 * @param string $hook_list_file File where hooks( not request type  ) are defined.
	 *
	 * @return Hook[]
	 */
	public static function hooks( string $hook_list_file = self::PATH . '/hooks.php' ): array
	{
		$hooks = include $hook_list_file;

		return WP_Hooks::apply_filters( PLUGIN_NAME . '/hooks/list', $hooks ) ?: [];
	}

	/**
	 * Retrieve plugin requests from config
	 *
	 * @param string $request_list_file File where hooks( request type ) are defined.
	 *
	 * @return Hook[]
	 */
	public static function requests( string $request_list_file = self::PATH . '/requests.php' ): array
	{
		$requests = include $request_list_file;

		return WP_Hooks::apply_filters( PLUGIN_NAME . '/requests/list', $requests ) ?: [];
	}

	/**
	 * Retrieve plugin services from config
	 *
	 * @param string $service_config_file File where services are defined.
	 *
	 * @return Service[]
	 */
	public static function services( string $service_config_file = self::PATH . '/services.php' ): array
	{
		$services = include $service_config_file;

		return apply_filters( PLUGIN_NAME . '/services/list', $services );
	}
}