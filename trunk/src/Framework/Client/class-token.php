<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework\Client;

/**
 * Class Token. Add security to desktops based on token.
 */
final class Token {

    /**
     * @var string $request_token .
     */
    private $request_token;

    /**
     * @var string $desktop_token
     */
    private $desktop_token;

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
        $token->desktop_token =&$token->get_desktop_token( $args[ 'project' ] );
        $token->request_token = sanitize_title( $args[ 'request_token' ] );

        return $token;
    }

    /**
     * Retrieve token used in current desktop.
     *
     * @param string $project Current project.
     *
     * @return string
     */
    private function &get_desktop_token( string $project ): string
    {
        if ( ! session_id() ) {
            session_start();
        }

        if ( empty( $_SESSION[ $project ][ 'token' ] ) ) {
            $_SESSION[ $project ] = [];
            $_SESSION[ $project ][ 'token' ] = '';
        }

        $_SESSION[ $project ][ 'token' ] = sanitize_title( $_SESSION[ $project ][ 'token' ] );

        return $_SESSION[ $project ][ 'token' ];
    }

    /**
     * Retrieve if environment has a valid token( token is equal to session token ).
     *
     * @return boolean
     */
    public function is_OK(): bool
    {
        return $this->is_active() && $this->request_token === $this->desktop_token;
    }

    /**
     * Check if there is some active token.
     *
     * @return boolean
     */
    public function is_active(): bool
    {
        return ! empty( $this->desktop_token );
    }

    /**
     * Retrieve if environment has a token
     *
     * @return boolean
     */
    public function exists(): bool
    {
        return ! empty( $this->request_token );
    }

    /**
     * Generate an unique new token.
     *
     * @return string
     */
    public function generate(): string
    {
        return $this->desktop_token = wp_generate_uuid4();
    }

    /**
     * Retrieve current token.
     *
     * @return string
     */
    public function get(): string
    {
        return $this->desktop_token;
    }

    /**
     * Remove all current token.
     *
     * @return void
     */
    public function remove(): void
    {
        $this->desktop_token = [];
    }
}