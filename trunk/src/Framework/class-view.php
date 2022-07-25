<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;
use const Trasweb\Plugins\MyDesktop\PLUGIN_NAME;

/**
 * Class View. Manage text files which will be passed to parser.
 */
class View {

	/**
	 * @var string
	 */
	private $view_name;
	/**
	 * @var string
	 */
	private $view_base_path;
	/**
	 * @var array
	 */
	private $vars;

	/**
	 * Generate an instance of Parser using services.php config file.
	 *
	 * @param array $options
	 * @param array $args
	 *
	 * @return static
	 */
	final public static function get_instance( array $options, array $args ): self
	{
		$view = new self( ...$args );
		$view->view_base_path = $options[ 'base_path' ];

		return $view;
	}

	/**
	 * Parse a view using some variables.
	 *
	 * @param string $view_name View to parse.
	 * @param array  $vars      Variables to use in parsing.
	 *
	 * @return string
	 */
	final public static function get( string $view_name, array $vars = [] ): string
	{
		$view_engine_class = Service::get( 'View', $view_name, $vars );

		return $view_engine_class->parse();
	}

	/**
	 * View constructor.
	 *
	 * @param string $view_name View to parse.
	 * @param array  $vars      Variables to use in parsing.
	 */
	final public function __construct( string $view_name, array $vars = [] )
	{
		$this->view_name = $view_name;
		$this->vars = $vars;
	}

	/**
	 * Retrieve content of a view.
	 *
	 * @param string $view_name View where content will be extract.
	 *
	 * @return string
	 */
	private function get_view_content( string $view_name ): string
	{
		static $views_content_cache = [];

		if ( !empty( $views_content_cache[ $view_name ] ) ) {
			return $views_content_cache[ $view_name ];
		}

		$view_content = file_get_contents( $this->view_base_path . '/' . $view_name . '.tpl' );

		$views_content_cache[ $view_name ] = WP_Hooks::apply_filters( PLUGIN_NAME . '/view-' . $view_name, $view_content );

		return $views_content_cache[ $view_name ];
	}

	/**
	 * Parse current view using a Parser service.
	 *
	 * @return mixed
	 */
	final public function parse()
	{
		$view_content = $this->get_view_content( $this->view_name );

		$parser_engine_class = Service::get( 'Parser' );

		return $parser_engine_class->parse( $view_content, $this->vars );
	}
}
