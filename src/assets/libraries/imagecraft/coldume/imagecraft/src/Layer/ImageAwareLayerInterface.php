<?php



namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
interface ImageAwareLayerInterface extends LayerInterface
{
    public const RESIZE_SHRINK = 'shrink';
    public const RESIZE_FILL_CROP = 'fill_crop';

    /**
     * @param string    $url
     * @param float|int $dataLimit
     * @param float|int $timeout
     *
     * @return $this
     *
     * @api
     */
    public function http($url, $dataLimit = -1, $timeout = -1);

    /**
     * @param string $filename
     *
     * @return $this
     *
     * @api
     */
    public function filename($filename);

    /**
     * @param string $contents
     *
     * @return $this
     *
     * @api
     */
    public function contents($contents);

    /**
     * @param int    $width
     * @param int    $height
     * @param string $option
     *
     * @return $this
     *
     * @api
     */
    public function resize($width, $height, $option = self::RESIZE_SHRINK);
}
