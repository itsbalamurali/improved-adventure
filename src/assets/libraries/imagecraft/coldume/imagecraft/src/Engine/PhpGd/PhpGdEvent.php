<?php



namespace Imagecraft\Engine\PhpGd;

use Imagecraft\Image;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class PhpGdEvent extends Event
{
    /**
     * @var \Imagecraft\Layer\LayerInterface[]
     */
    protected $layers;

    /**
     * @var mixed[]
     */
    protected $options;

    /**
     * @var Image
     */
    protected $image;

    /**
     * @param \Imagecraft\Layer\LayerInterface[] $layers
     * @param mixed[]                            $options
     */
    public function __construct(array $layers, array $options)
    {
        $this->layers = $layers;
        $this->options = $options;
    }

    /**
     * @return \Imagecraft\Layer\LayerInterface[]
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * @return mixed[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function setImage(Image $image): void
    {
        $this->image = $image;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
