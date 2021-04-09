<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Models;

/**
 * Class Desktop. Manage information about number of current desktop.
 */
class Desktop {

	/**
	 * @var string Key used for save each widget data.
	 */
	private const META_KEY = '_my_desktop_%d_widget';

	/**
	 * @var string In the future, My desktop will support virtual desktop. This is max virtual desktop by user.
	 */
	private const MAX_DESKTOP = 1;

	/**
	 * @var string In the future, My desktop will support virtual desktop. While, this is current desktop.
	 */
	private const DEFAULT_DESKTOP = 1;

	/**
	 * @var int $current_desktop Number relative to current desktop.
	 */
	private $current_desktop;

	/**
	 * Retrieve an instance of Desktop class.
	 *
	 * @param array|string} $args
	 *
	 * @return static
	 */
	public static function get_instance( array $args ): self
	{
		$desktop = new self();

		$desktop->current_desktop = (int) $args[ 'current_desktop' ];
		if ( $desktop->current_desktop < 1 || $desktop->current_desktop > self::MAX_DESKTOP ) {
			$desktop->current_desktop = self::DEFAULT_DESKTOP;
		}

		return $desktop;
	}

	/**
	 * Retrieve current desktop.
	 *
	 * @return integer
	 */
	public function get_current(): int
	{
		return $this->current_desktop;
	}

	/**
	 * Retrieve meta_key for widgets according to current desktop.
	 *
	 * @return string
	 */
	public function get_widget_meta_key( int $desktop_id = 0 )
	{
		return \sprintf( self::META_KEY, $desktop_id ?: $this->get_current() );
	}
}