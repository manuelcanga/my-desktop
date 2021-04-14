<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework\Client;

use Trasweb\Plugins\MyDesktop\Mocks\WP_User;

class User {

	/**
	 * @var string $needed_capability Capability which user needs in order to use this plugin.
	 */
	public $needed_capability;

	/**
	 * Retrieve instance of service.
	 *
	 * @param array{project: string, current_token: string} $args Service arguments.
	 *
	 * @return static
	 */
	public static function get_instance( array $args ): self
	{
		$token = new self();
		$token->needed_capability = sanitize_title( $args[ 'needed_capability' ] );

		return $token;
	}

	/**
	 * Check if user is allowed to use plugin or not.
	 *
	 * @return boolean
	 */
	public function is_allowed(): bool
	{
		// Administrators are always allowed.
		if ( WP_User::is_admin() ) {
			return true;
		}

		return WP_User::current_user_can( $this->needed_capability );
	}
}