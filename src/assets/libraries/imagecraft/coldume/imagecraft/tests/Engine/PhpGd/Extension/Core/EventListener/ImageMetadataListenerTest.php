<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageMetadataListener
 *
 * @internal
 */
final class ImageMetadataListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    protected function setUp(): void
    {
        $context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\ImageMetadataListener',
            null,
            [$context]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\ImageLayer', null);
        $this->image = $this->createPartialMock('Imagecraft\\Image', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
        $this->event
            ->method('getImage')
            ->willReturn($this->image)
        ;
    }

    public function testAddImageMetadatas(): void
    {
        $this->layer->add([
            'final.format' => PhpGdContext::FORMAT_JPEG,
            'final.width' => 100,
            'final.height' => 200,
            'image.width' => 300,
            'image.height' => 400,
        ]);
        $this->listener->addImageMetadatas($this->event);
        self::assertIsString($this->image->getMime());
        self::assertIsString($this->image->getExtension());
        self::assertSame(100, $this->image->getWidth());
        self::assertSame(200, $this->image->getHeight());
        self::assertSame(300, $this->image->getExtras()['original_width']);
        self::assertSame(400, $this->image->getExtras()['original_height']);
    }
}
