<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets\Sticky;

/**
 * Class Icon
 */
class Icon extends Sticky {
    /**
     * @var: string TYPE class of widget.
     */
    protected const TYPE = 'icon';

    /**
     * @var string VIEW Appearance of view.
     */
    protected const VIEW = 'Widgets/Icon/icon';

    /**
     * Show a icon sticky widget into desktop.
     *
     * @param array $vars Custom data for widget.
     *
     * @return string
     */
    public function show( string $default_output, array $vars ): string
    {
        return $this->render( [ 'widget' => $vars['widget'], 'position' => $vars['index'] ]);
    }
}