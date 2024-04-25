<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\ImageFactoryListener
 *
 * @internal
 */
final class ImageFactoryListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $factory;

    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    protected function setUp(): void
    {
        $this->factory = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\ImageFactory', [], [], '', false);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\EventListener\\ImageFactoryListener',
            null,
            [$this->factory]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
        $this->image = $this->createPartialMock('Imagecraft\\Image', null);
        $this->event
            ->method('getImage')
            ->willReturn($this->image)
        ;
    }

    public function testCreateImage(): void
    {
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn([])
        ;
        $this->factory
            ->expects(self::once())
            ->method('createImage')
            ->willReturn($this->image)
        ;
        $this->event
            ->expects(self::once())
            ->method('setImage')
            ->with($this->image)
        ;
        $this->layer->add(['gif.extracted' => []]);
        $this->listener->createImage($this->event);
    }

    public function testAddImageExtras(): void
    {
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn([])
        ;
        $this->factory
            ->expects(self::once())
            ->method('createImage')
            ->will(self::throwException(new \Exception()))
        ;
        $this->layer->add(['gif.extracted' => []]);
        $this->listener->createImage($this->event);
        $this->listener->addImageExtras($this->event);
        self::assertNotEmpty($this->image->getExtras()['gif_error']);
    }
}
