<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework;

use Trasweb\Plugins\MyDesktop\Mocks\WP_Hooks;

/**
 * Class Parser. Analyze a text and execute specific code.
 * Allow code: {{var}}, {% block %}{% endblock %}, {'string'} and {$ hook }
 */
final class Parser {

    /**
     * @var string
     */
    private const FIND_VAR = '/{{(?P<var>.*?)}}/';

    /**
     * @var string
     */
    private const FIND_BLOCKS = '/' . // start regex
                                '\{\%\s?(?<block_name>[\w\!\_\.]+)' . // start block:  {% block_name
                                '(\s(?<var>[^{}]*))?' . // var ?
                                '\ \%\}' . // %}
                                '(?<content>(([^%]+)|(?R))*)' . // content with not %
                                '\{\%\s?end\1\s?\%\}' . // end macro:  {% end{macro_name} %}
                                '/mU';                                  // end regex.

    /**
     * @var string
     */
    private const FIND_GETTEXT_STRINGS = '/\{\'(?P<string>.*?)\'\}/';

    /**
     * @var string
     */
    private const FIND_HOOK = '/{\$(?P<hook>.*?)}/';

    /**
     * @var array
     */
    private $vars = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * Generate an instance of Parser using services.php config file.
     *
     * @param $options
     *
     * @return static
     */
    final public static function get_instance( $options ): self
    {
        $parser = new self();
        $parser->options = $options;
        $parser->vars = [];

        return $parser;
    }

    /**
     * Parse a text using as vars $vars
     *
     * @param string $content
     * @param array  $vars
     * @param array  $options
     *
     * @return string
     */
    final public function parse( string $content = '', array $vars = [], array $options = [] ): string
    {
        $this->vars += $vars;
        $this->options += $options;

        $patterns_and_callbacks = [
            self::FIND_BLOCKS          => [ $this, 'do_block' ],
            self::FIND_VAR             => [ $this, 'return_var_value_from_tokens' ],
            self::FIND_GETTEXT_STRINGS => [ $this, 'replace_gettext_strings' ],
            self::FIND_HOOK            => [ $this, 'run_hook' ],
        ];

        return preg_replace_callback_array( $patterns_and_callbacks, $content );
    }

    /**
     * Check if a content has a var inside or not.
     *
     * @param string $maybe_inside_var
     *
     * @return boolean
     */
    final public function has_var( string $maybe_inside_var ): bool
    {
        $matches = [];

        preg_match( '%' . self::FIND_VAR . '%us', $maybe_inside_var, $matches );

        return ! empty( $matches[ 'var' ] );
    }

    /**
     * Replace a {{var}} for its value
     *
     * @param array $tokens Tokens from parsing.
     *
     * @return string
     */
    final protected function return_var_value_from_tokens( array $tokens ): string
    {
        $var = strtok( $tokens[ 'var' ], $this->options[ 'FILTER_SEPARATOR' ] ) ?: '';
        $filters = explode( $this->options[ 'FILTER_SEPARATOR' ], strtok( '' ) ?: '' );
        $filters = array_map( 'trim', array_filter( $filters ) );

        $value = $this->get_value_of_var_name( $var );

        foreach ( $filters as $filter_name ) {
            $filter = $this->vars[ 'filters' ][ $filter_name ] ?? '';

            if ( empty( $filter ) ) {
                continue;
            }

            $value = $filter( $value, $this->vars );
        }

        return (string)$value;
    }

    /**
     * Parse a block statement
     *
     * @param array{block_name: string, var: string, content: string} $tokens
     *
     * @return string
     */
    private function do_block( array $tokens ): string
    {
        $block_name = $tokens[ 'block_name' ];
        $var = $tokens[ 'var' ] ?? '';
        $content = $tokens[ 'content' ] ?? '';

        if ( 'foreach' === $block_name ) {
            return $this->do_foreach( $var, $content );
        }

        if ( 'if' === $block_name ) {
            return $this->do_if( $var, $content );
        }

        return (string)WP_Hooks::apply_filters( $this->options[ 'PLUGIN_NAME' ] . '/blocks/' . $block_name, $content, $var, $this );
    }

    /**
     * Parse a foreach statement
     *
     * @param string $var
     * @param string $content_of_foreach
     *
     * @return string
     */
    private function do_foreach( string $var, string $content_of_foreach ): string
    {
        $vars = explode( ' as ', $var ?? '' );
        $var_name = trim( $vars[ 0 ] );
        $var_alias = trim( $vars[ 1 ] ?? 'item' );

        $items_to_iterate = $this->get_value_of_var_name( $var_name );

        if ( empty( $items_to_iterate ) || ! is_iterable( $items_to_iterate ) || '' === $content_of_foreach ) {
            return $this->options[ 'DEFAULT_VALUE' ];
        }

        return $this->parse_content_for_all_items( $items_to_iterate, $content_of_foreach, $var_alias );
    }

    /**
     * Retrieve value of a var
     *
     * @param string $var_name
     *
     * @return mixed|string
     */
    private function get_value_of_var_name( string $var_name )
    {
        $var_name = trim( $var_name );

        $vars_name = explode( '.', $var_name );
        $value = $this->options[ 'DEFAULT_VALUE' ];

        $vars =&$this->vars;
        foreach ( $vars_name as $var_name ) {
            if ( is_array( $vars ) ) {
                $value = $vars[ $var_name ] ?? $this->options[ 'DEFAULT_VALUE' ];
            } elseif ( is_object( $vars ) ) {
                $value = $vars->$var_name ?? $this->options[ 'DEFAULT_VALUE' ];
            } else {
                return $this->options[ 'DEFAULT_VALUE' ];
            }

            $vars =&$value;
        }

        return $value;
    }

    /**
     * Parse foreach content iteratively.
     *
     * @param iterable|Countable $items_to_iterate   Items to iterate.
     * @param string             $content_of_foreach
     * @param string             $var_alias          Alias of each item.
     *
     * @return string
     */
    private function parse_content_for_all_items( iterable $items_to_iterate, string $content_of_foreach, string $var_alias ): string
    {
        $index = 1;
        $max = count( $items_to_iterate );

        $foreach_result = '';
        foreach ( $items_to_iterate as $item ) {
            $vars = $this->vars;

            $vars[ $var_alias ] = $item;
            $vars[ 'index' ] = $index;
            $vars[ 'count' ] = $max;
            $vars[ 'is_first' ] = 1 === $index;
            $vars[ 'is_last' ] = $max === $index;

            $foreach_result .= ( new $this )->parse( $content_of_foreach, $vars, $this->options );
            $index++;
        }

        return $foreach_result;
    }

    /**
     * Parse an if statement
     *
     * @param string $var_of_conditional
     * @param string $content_of_conditional
     *
     * @return string
     */
    private function do_if( string $var_of_conditional, string $content_of_conditional ): string
    {
        $true_with_empty = '!' === $var_of_conditional[ 0 ];
        $var_of_conditional = ltrim( $var_of_conditional, '! ' );

        if ( '' === $var_of_conditional || '' === $content_of_conditional ) {
            return $this->options[ 'DEFAULT_VALUE' ];
        }

        $conditional_is_false = empty( $this->get_value_of_var_name( $var_of_conditional ) );

        if ( $conditional_is_false !== $true_with_empty ) {
            return $this->options[ 'DEFAULT_VALUE' ];
        }

        return ( new $this )->parse( $content_of_conditional, $this->vars );
    }

    /**
     * Replace a {'string'] for its gettext version.
     *
     * @param array{string: string} $tokens Tokens from parsing.
     *
     * @return string
     */
    private function replace_gettext_strings( array $tokens ): string
    {
        $string = $tokens[ 'string' ] ?? '';

        if ( $this->has_var( $string ) ) {
            $string = ( new Parser() )->parse( $string, $this->vars, $this->options );
        }

        return esc_html( __( $string, $this->options[ 'PLUGIN_NAME' ] ) );
    }

    /**
     * Run a {$ hook].
     *
     * @param array{string: string} $tokens Tokens from parsing.
     *
     * @return string
     */
    private function run_hook( array $tokens ): string
    {
        $hook = trim( $tokens[ 'hook' ] ?? '' );

        return WP_Hooks::apply_filters( $this->options[ 'PLUGIN_NAME' ] . '/' . $hook, '', $this->vars );
    }
}
