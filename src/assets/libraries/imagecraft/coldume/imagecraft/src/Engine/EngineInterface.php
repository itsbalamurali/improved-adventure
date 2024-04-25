<?php



namespace Imagecraft\Engine;

use Imagecraft\AbstractContext;
use Imagecraft\Image;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
interface EngineInterface
{
    /**
     * @param \Imagecraft\Layer\LayerInterface[] $layers
     * @param mixed[]                            $options
     *
     * @return Image
     */
    public function getImage(array $layers, array $options);

    /**
     * @param mixed[] $options
     *
     * @return AbstractContext
     */
    public function getContext(array $options);
}
