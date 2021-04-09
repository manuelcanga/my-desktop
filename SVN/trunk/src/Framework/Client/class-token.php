<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework\Client;

/**
 * Class Token. Add security to desktops based on token.
 */
final class Token {

	/**
	 * @var string $current_token .
	 */
	private $current_token;
	/**
	 * @var array $repository
	 */
	private $repository;

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
		$token->repository =& $token->get_repository( $args[ 'project' ] );
		$token->current_token = sanitize_title( $args[ 'current_token' ] );

		return $token;
	}

	/**
	 * Retrieve data form session data.
	 *
	 * @param string $project Current project.
	 *
	 * @return array
	 */
	private function &get_repository( string $project ): array
	{
		if ( !session_id() ) {
			session_start();
		}

		if ( empty( $_SESSION[ $project ] ) ) {
			$_SESSION[ $project ] = [];
			$_SESSION[ $project ][ 'token' ] = '';
		}

		return $_SESSION[ $project ];
	}

	/**
	 * Retrieve if environment has a valid token( token is equal to session token ).
	 *
	 * @return boolean
	 */
	public function is_OK(): bool
	{
		return $this->is_active() && $this->current_token === $this->repository[ 'token' ];
	}

	/**
	 * Check if there is some active token.
	 *
	 * @return boolean
	 */
	public function is_active(): bool
	{
		return !empty( $this->repository[ 'token' ] );
	}

	/**
	 * Retrieve if environment has a token
	 *
	 * @return boolean
	 */
	public function exists(): bool
	{
		return !empty( $this->current_token );
	}

	/**
	 * Generate an unique new token.
	 *
	 * @return string
	 */
	public function generate(): string
	{
		return $this->repository[ 'token' ] = wp_generate_uuid4();
	}

	/**
	 * Retrieve current token.
	 *
	 * @return string
	 */
	public function get(): string
	{
		return $this->repository[ 'token' ];
	}

	/**
	 * Remove all current token.
	 *
	 * @return void
	 */
	public function remove(): void
	{
		$this->repository = [];
	}
}