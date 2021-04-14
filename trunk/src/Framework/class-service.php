<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Framework;

/**
 * Class Service. Register services like a Dependency Injection Container.
 */
class Service {

    /**
     * @var string
     */
    private const OPTIONS_METHOD = 'get_instance';

    /**
     * @var array
     */
    private static $services = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $config;

    /**
     * @var boolean
     */
    private $only_once;

    /**
     * @var null|object
     */
    private $instance;

    /**
     * @var string
     */
    private $bootstrap;

    /**
     * Create a new hook.
     *
     * @param string $service_id
     * @param string $service_class
     * @param array  $instance_options
     *
     * @return Service
     */
    final public static function new( string $service_id, string $service_class, array $instance_options = [] ): self
    {
        $service = new self();
        $service->id = $service_id;
        $service->class = $service_class;
        $service->only_once = $instance_options[ 'only_once' ] ?? false;
        $service->config = $instance_options[ 'config' ] ?? [];
        $service->bootstrap = $instance_options[ 'bootstrap' ] ?? '';

        return $service;
    }

    /**
     * Retrieve a service according to its $service_id
     *
     * @param string $service_id
     * @param mixed  ...$args
     *
     * @return object|null
     */
    final public static function get( string $service_id, ...$args ): ?object
    {
        $service = self::$services[ $service_id ] ?? null;

        if ( ! $service ) {
            return null;
        }

        return $service->get_instance( $args );
    }

    /**
     * Register current service.
     *
     * @return void
     */
    final public function register(): void
    {
        static::$services[ $this->id ] = $this;
    }

    /**
     * Create an instance of a service.
     *
     * @param array $args
     *
     * @return ?object
     */
    final private function get_instance( array $args ): ?object
    {
        if ( $this->only_once && $this->instance ) {
            return $this->instance;
        }

        if ( $this->bootstrap ) {
            $bootstrap_file = $this->bootstrap;

            include_once $bootstrap_file;
        }

        $class_name = $this->class;
        $service = null;

        if ( ! class_exists( $class_name ) ) {
            return $service;
        }

        if ( ! $this->config ) {
            $service = new $class_name( ...$args );
        } else {
            $options_method = self::OPTIONS_METHOD;
            if ( method_exists( $class_name, $options_method ) ) {
                $service = $class_name::$options_method( $this->config, $args );
            }
        }

        return $this->instance = $service;
    }
}
