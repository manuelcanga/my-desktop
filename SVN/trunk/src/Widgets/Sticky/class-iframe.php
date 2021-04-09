<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\MyDesktop\Widgets\Sticky;

/**
 * Class Iframe
 */
class Iframe extends Sticky {
    /**
     * @var: string TYPE class of widget.
     */
    protected const TYPE = 'iframe';

    /**
     * @var string VIEW Appearance of view.
     */
    protected const VIEW = 'Widgets/Iframe/iframe';

    /**
     * Show a iframe widget into desktop.
     *
     * @param array $vars Custom data for widget.
     *
     * @return string
     */
    public function show( $default_output, $vars ): string
    {
        return $this->render( [ 'widget' => $vars['widget'], 'position' => $vars['index'] ]);
    }
}