<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop;

use Trasweb\Plugins\MyDesktop\Framework\Service;
use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;
use Trasweb\Plugins\MyDesktop\Models\Config;
use const PHP_URL_PATH;

/***
 * Class MyDesktop
 *
 * Initialize and configure plugin
 *
 */
class My_Desktop {

	/** @var string _CLASSES_ Plugin base directory. */
	public const  _CLASSES_ = __DIR__;

	/** @var string NAMESPACE Plugin base namespace. */
	public const NAMESPACE  = __NAMESPACE__;

	/** @var string LANG_DIR Directory for .po/.mo files. */
	private const LANG_DIR = '/languages';

	/**
	 * @var string[] Plugin tasks to do.
	 */
	private $task_list = [
		'initialization',
		'check_access_to_my_desktop_without_login',
		'register_services',
		'register_autoload',
		'set_environment',
		'add_i18n',
		'check_user_is_allowed',
		'enqueue_hooks',
		'enqueue_requests',
		'load',
	];

	/**
	 * Define basic of plugin in order to can be loaded.
	 *
	 * @return void
	 */
	public function __invoke()
	{
		foreach ( $this->task_list as &$task_to_do ) {
			$this->$task_to_do();
		}
	}

	/**
	 * Basic constants used by plugins.
	 *
	 * @const string _PLUGIN_  Base project dir
	 * @const string PLUGIN_NAME Name of plugin( my-desktop )
	 * @const string PLUGIN_TITLE Label for plugin( My Desktop )
	 * @const string PLUGIN_URL Url to plugin directory( https://my-domain/wp-content/plugins/my-desktop/ )
	 *
	 * @return void
	 */
	private function initialization()
	{
		define( __NAMESPACE__ . '\_PLUGIN_', dirname( __DIR__ ) );
		define( __NAMESPACE__ . '\PLUGIN_NAME', basename( _PLUGIN_ ) );
		define( __NAMESPACE__ . '\PLUGIN_ID', PLUGIN_NAME . '/' . PLUGIN_NAME . '.php' );
		define( __NAMESPACE__ . '\PLUGIN_TITLE', __( 'My Desktop', PLUGIN_NAME ) );
		define( __NAMESPACE__ . '\PLUGIN_URL', plugins_url( PLUGIN_NAME ) );
		define( __NAMESPACE__ . '\SITE_PATH', $this->get_site_path() );
		define( __NAMESPACE__ . '\MY_DESKTOP_URL', admin_url( PLUGIN_NAME ) );

		include_once self::_CLASSES_ . '/class-my-desktop.php';
		include_once self::_CLASSES_ . '/Framework/class-service.php';
		include_once self::_CLASSES_ . '/Models/class-config.php';
	}

	/**
	 * Check if user is trying my_desktiop witouth being logged.
	 *
	 * @return void
	 */
	private function check_access_to_my_desktop_without_login(): void
	{
		// Try access to my_desktop without log-in.
		if ( !is_user_logged_in() && get_self_link() === admin_url( 'my-desktop' ) ) {
			$this->task_list = [];

			$this->redirecto_to_login( $_REQUEST[ 'redirect_to' ] ?? '' );
		}
	}

	/**
	 * Redirect to login page when user is not logged and she/he isn't in login page.
	 *
	 * @param string $redirect_to_param It's a param used in login page.
	 *
	 * @return void
	 */
	private function redirecto_to_login( string $redirect_to_param ): void
	{
		if ( empty( $redirect_to ) ) {
			wp_safe_redirect( wp_login_url( admin_url( '/my-desktop' ), true ) );
			exit();
		}
	}

	/**
	 * Register plugin services. Services are loaded in this way in order to other developers can customize them.
	 *
	 * @return void
	 */
	final private function register_services(): void
	{
		/*
		 * @var Service[] $service_list
		 */
		$service_list = Config::services();

		foreach ( $service_list as $service ) {
			$service->register();
		}
	}

	/**
	 * Register plugin autoload.
	 *
	 * @return void
	 */
	final private function register_autoload(): void
	{
		$autoload = Service::get( 'Autoload' );

		spl_autoload_register( [ $autoload, 'find_class' ], $throw_exception = false );
	}

	/**
	 * Initialize environment and validate it.
	 *
	 * @return void
	 */
	final private function set_environment(): void
	{
		$environment = Service::get( 'Token' );

		if ( $environment->exists() && !$environment->is_OK() ) {
			$warning_msg = 'Other desktop was opened with your user. Please switch to other desktop or reload current page';
			die( __( $warning_msg, PLUGIN_NAME ) );
		}
	}

	/**
	 * Check if user is allowed to load plugin.
	 *
	 * @return void
	 */
	final private function check_user_is_allowed(): void
	{
		if ( !Service::get( 'User' )->is_allowed() ) {
			$this->task_list = [];
		}
	}

	/**
	 * Config action
	 *
	 * @return void
	 */
	final private function load(): void
	{
		WP_Hooks::do_action( PLUGIN_NAME . '-loaded' );
	}

	/**
	 * Add support to i18n for plugin.
	 *
	 * @return void
	 */
	final private function add_i18n(): void
	{
		load_plugin_textdomain( PLUGIN_NAME, false, PLUGIN_NAME . self::LANG_DIR );
	}

	/**
	 * Enqueue hooks used in plugin.
	 *
	 * @return void
	 */
	final private function enqueue_hooks(): void
	{
		/*
		 * @var Hook[] $hooks_list
		 */
		$hooks_list = Config::hooks();

		foreach ( $hooks_list as $hook ) {
			$hook->enqueue();
		}
	}

	/**
	 * Enqueue requests used in plugin.
	 *
	 * @return void
	 */
	final private function enqueue_requests(): void
	{
		/*
		 * @var Hook[] $request_list
		 */
		$request_list = Config::requests();

		foreach ( $request_list as $request ) {
			$request->enqueue();
		}
	}

	/**
	 * Helper: Retrieve path from site_url and untrailing slash.
	 *
	 * @example https://my_domain/wordpress/  => /wordpress
	 *
	 * @return string
	 */
	private function get_site_path(): string
	{
		$site_path = (string) parse_url( site_url(), PHP_URL_PATH );

		return untrailingslashit( $site_path );
	}
}