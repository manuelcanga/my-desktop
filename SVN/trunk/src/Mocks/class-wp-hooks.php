<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Mocks;

/**
 * Class WP_Hooks
 */
class WP_Hooks {
	/**
	 * Mock apply_filters
	 *
	 * @param string $tag
	 * @param mixed  ...$args
	 *
	 * @return mixed|void
	 */
	public static function apply_filters( string $tag, ...$args )
	{
		return apply_filters( $tag, ...$args );
	}

	/**
	 * Mock do_action
	 *
	 * @param string $tag
	 * @param mixed  ...$args
	 *
	 * @return mixed|void
	 */
	public static function do_action( string $tag, ...$args ) {
		return do_action( $tag, ...$args );
	}
}