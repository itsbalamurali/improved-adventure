<?php



namespace Imagecraft\Engine\PhpGd\Extension\Save\EventListener;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Save\EventListener\ImageFactoryListener
 *
 * @internal
 */
final class ImageFactoryListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $factory;

    protected $layer;

    protected function setUp(): void
    {
        $this->factory = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Save\\ImageFactory', [], [], '', false);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Save\\EventListener\\ImageFactoryListener',
            null,
            [$this->factory]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\ImageLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn([])
        ;
    }

    public function testCreateImage(): void
    {
        $this->layer->add([
            'image.width' => 10,
            'image.height' => 100,
            'final.width' => 10,
            'final.height' => 100,
            'image.format' => 'foo',
            'final.format' => 'foo',
        ]);
        $image = $this->createPartialMock('Imagecraft\\Image', null);
        $this->factory
            ->expects(self::once())
            ->method('createImage')
            ->willReturn($image)
        ;
        $this->event
            ->expects(self::once())
            ->method('setImage')
            ->with($image)
        ;
        $this->listener->createImage($this->event);
    }
}
