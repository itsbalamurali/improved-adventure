<?php



namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class ImageLayer extends AbstractLayer implements ImageLayerInterface
{
    public function http($url, $dataLimit = -1, $timeout = -1)
    {
        $this->add([
            'image.http.url' => $url,
            'image.http.data_limit' => $dataLimit,
            'image.http.timeout' => $timeout,
        ]);

        return $this;
    }

    public function filename($filename)
    {
        $this->set('image.filename', $filename);

        return $this;
    }

    public function contents($contents)
    {
        $this->set('image.contents', $contents);

        return $this;
    }

    public function resize($width, $height, $option = ImageAwareLayerInterface::RESIZE_SHRINK)
    {
        $this->add([
            'image.resize.width' => $width,
            'image.resize.height' => $height,
            'image.resize.option' => $option,
        ]);

        return $this;
    }

    public function move($x, $y, $gravity = RegularLayerInterface::MOVE_TOP_LEFT)
    {
        $this->add([
            'regular.move.x' => $x,
            'regular.move.y' => $y,
            'regular.move.gravity' => $gravity,
        ]);

        return $this;
    }
}
