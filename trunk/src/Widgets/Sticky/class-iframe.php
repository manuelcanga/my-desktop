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
     * @param string $default_output Default output from filter.
     * @param array  $vars           Custom data for widget.
     *
     * @return string
     */
    public function show( string $default_output, array $vars ): string
    {
        return $this->render( [ 'widget' => $vars[ 'widget' ], 'position' => $vars[ 'index' ] ] );
    }
}