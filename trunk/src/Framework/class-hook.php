<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework;

/**
 * Class Hook. Abstraction for WP Hooks.
 */
class Hook {

    /**
     * @var string
     */
    private const COMMON_UP_METHOD = 'configure';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $listener;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ?array
     */
    private $settings;

    /**
     * Create a new hook.
     *
     * @param string $hook_id      WP tag.
     * @param array  $hook_options
     *
     * @return Hook
     */
    final public static function new( string $hook_id, array $hook_options ): self
    {
        $hook = new Hook();

        $hook->id = $hook_id;
        $hook->listener = $hook_options[ 'listener' ];
        $hook->method = $hook_options[ 'filter' ] ?? $hook_options[ 'action' ];
        $hook->type = isset( $hook_options[ 'filter' ] ) ? 'filter' : 'action';
        $hook->priority = $hook_options[ 'priority' ] ?? 10;
        $hook->config = $hook_options[ 'config' ] ?? [];
        $hook->settings = $hook_options[ 'settings' ] ?? null;

        return $hook;
    }

    /**
     * Generate a new Hook callback on the fly.
     *
     * @param mixed      $listener Object or name of class.
     * @param string     $method
     * @param array|null $settings
     *
     * @return callable
     */
    final public static function callback( $listener, string $method, ?array $settings = null ): callable
    {
        $hook = new Hook();

        $hook->listener = $listener;
        $hook->method = $method;
        $hook->config = [];
        $hook->settings = $settings;

        return [ $hook, 'invoke' ];
    }

    /**
     * Register hook in WP.
     *
     * @return void
     */
    final public function enqueue(): void
    {
        $to_register = 'add_' . $this->type;

        $to_register( $this->id, [ $this, 'invoke' ], $this->priority, 99 );
    }

    /**
     * Invoke hook.
     *
     * @param mixed ...$args Arguments will be passed to method.
     *
     * @return mixed
     */
    final public function invoke( ...$args )
    {
        $class = $this->listener;
        $method = $this->method;

        return $this->invoke_hook( $class, $method, $args, $this->config, $this->settings );
    }

    /**
     * Helper: Invoke hook in low level.
     *
     * @param mixed      $class       Object or name of class.
     * @param string     $method      Method which will be called.
     * @param array      $method_args Arguments will be passed to method.
     * @param array      $config      Arguments will be passed to class( "configure" method ).
     * @param array|null $settings    Arguments will be passed to method( specific config ).
     *
     * @return mixed
     */
    private function invoke_hook( $class, string $method, array $method_args = [], array $config = [], ?array $settings = null )
    {
        $object = is_object( $class ) ? $class : new $class();

        $common_task_method = self::COMMON_UP_METHOD;
        if ( method_exists( $object, $common_task_method ) ) {
            $object->$common_task_method( $config );
        }

        if ( ! isset( $settings ) ) {
            return $object->$method( ...$method_args );
        }

        return $object->$method( $settings, ...$method_args );
    }
}
