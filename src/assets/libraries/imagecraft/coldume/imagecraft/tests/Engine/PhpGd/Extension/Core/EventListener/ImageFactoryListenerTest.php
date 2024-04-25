<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageFactoryListener
 *
 * @internal
 */
final class ImageFactoryListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $factory;

    protected function setUp(): void
    {
        $this->factory = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageFactory', [], [], '', false);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\ImageFactoryListener',
            null,
            [$this->factory]
        );
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
    }

    public function testCreateImage(): void
    {
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
        $this->event
            ->expects(self::once())
            ->method('getLayers')
            ->willReturn([])
        ;
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn([])
        ;
        $this->listener->createImage($this->event);
    }
}
