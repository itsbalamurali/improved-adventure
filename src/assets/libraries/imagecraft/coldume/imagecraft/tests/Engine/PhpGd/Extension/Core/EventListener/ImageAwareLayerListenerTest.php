<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Layer\ImageAwareLayerInterface;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageAwareLayerListener
 *
 * @internal
 */
final class ImageAwareLayerListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected function setUp(): void
    {
        $context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $info = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageInfo', null, [$context]);
        $rh = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\ImageAwareLayerListener',
            null,
            [$info, $rh]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\ImageLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
    }

    public function testInitImcUri(): void
    {
        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }

        $this->layer->add([
            'image.http.url' => 'http://www.example.com',
            'image.http.data_limit' => 500,
            'image.http.timeout' => 20,
        ]);
        $this->listener->initImcUri($this->event);
        self::assertNotEmpty($this->layer->get('image.imc_uri'));

        $this->layer->clear();
        $this->layer->add(['image.filename' => __FILE__]);
        $this->listener->initImcUri($this->event);
        self::assertNotEmpty($this->layer->get('image.imc_uri'));
    }

    public function testInitFilePointer(): void
    {
        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }

        $this->layer->add(['image.imc_uri' => 'http://www.example.com']);
        $this->listener->initFilePointer($this->event);
        self::assertIsResource($this->layer->get('image.fp'));

        $this->layer->clear();
        $this->layer->add(['image.contents' => file_get_contents(__FILE__)]);
        $this->listener->initFilePointer($this->event);
        self::assertIsResource($this->layer->get('image.fp'));
    }

    public function testInitImageInfo(): void
    {
        $this->layer->set('image.fp', fopen(__DIR__.'/../../../../../Fixtures/gif_87a_palette_250x297.gif', 'r'));
        $this->listener->initImageInfo($this->event);
        self::assertIsInt($this->layer->get('image.width'));
        self::assertIsInt($this->layer->get('image.height'));
        self::assertIsString($this->layer->get('image.format'));
    }

    public function testInitFinalDimensions(): void
    {
        $this->layer->add([
            'image.width' => 200,
            'image.height' => 200,
            'image.resize.width' => 100,
            'image.resize.height' => 100,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_FILL_CROP,
        ]);
        $this->listener->initFinalDimensions($this->event);
        self::assertIsInt($this->layer->get('final.width'));
        self::assertIsInt($this->layer->get('final.height'));
    }

    /**
     * @depends testInitFilePointer
     */
    public function testTermFilePointer(): void
    {
        $this->layer->add(['image.contents' => file_get_contents(__FILE__)]);
        $this->listener->initFilePointer($this->event);
        $this->listener->termFilePointer($this->event);
        self::assertFalse($this->layer->has('image.fp'));
    }

    /**
     * @depends testInitImcUri
     */
    public function testTermImcUri(): void
    {
        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }

        $this->layer->add([
            'image.http.url' => 'http://www.example.com',
            'image.http.data_limit' => 500,
            'image.http.timeout' => 20,
        ]);
        $this->listener->initImcUri($this->event);
        $this->listener->termImcUri($this->event);
        self::assertFalse($this->layer->has('image.imc_uri'));
    }
}
