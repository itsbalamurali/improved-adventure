<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\BackgroundLayerListener
 *
 * @internal
 */
final class BackgroundLayerListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected function setUp(): void
    {
        $this->listener = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\EventListener\\BackgroundLayerListener', null);
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
    }

    public function testInitFinalFormat(): void
    {
        $this->event
            ->method('getOptions')
            ->willReturn(['output_format' => 'default'])
        ;
        $this->layer->add(['image.format' => 'png']);
        $this->listener->initFinalFormat($this->event);
        self::assertIsString($this->layer->get('final.format'));
    }
}
