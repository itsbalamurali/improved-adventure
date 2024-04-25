<?php



namespace Imagecraft\LayerPass;

use Imagecraft\Exception\BadMethodCallException;
use Imagecraft\Layer\ImageAwareLayerInterface;

/**
 * @covers \Imagecraft\LayerPass\ImageAwareLayerPass
 *
 * @internal
 */
final class ImageAwareLayerPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected $layer;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\LayerPass\\ImageAwareLayerPass', null);
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\ImageLayer', null);
    }

    public function testProcessResource(): void
    {
        $this->layer->add(['image.http.url' => 'www.example.com', 'image.http.data_limit' => -10, 'timeout' => -20]);
        $this->pass->processResource($this->layer);
        self::assertSame($this->layer->get('image.http.url'), 'http://www.example.com');
        self::assertSame($this->layer->get('image.http.data_limit'), -1);
        self::assertSame($this->layer->get('image.http.timeout'), -1);

        $this->layer->clear();
        $this->layer->set('image.filename', 'foo');
        $this->pass->processResource($this->layer);
        self::assertSame($this->layer->get('image.filename'), 'foo');

        $this->layer->clear();
        $this->layer->set('image.contents', 'foo');
        $this->pass->processResource($this->layer);
        self::assertSame($this->layer->get('image.contents'), 'foo');
    }

    public function testProcessWhenNoResourceIsFound(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->pass->processResource($this->layer);
    }

    public function testProcessResize(): void
    {
        $this->layer->add([
            'image.resize.width' => -5,
            'image.resize.height' => 0,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_SHRINK,
        ]);
        $this->pass->processResize($this->layer);
        self::assertSame(1, $this->layer->get('image.resize.width'));
        self::assertSame(1, $this->layer->get('image.resize.height'));
        self::assertSame(ImageAwareLayerInterface::RESIZE_SHRINK, $this->layer->get('image.resize.option'));
    }
}
