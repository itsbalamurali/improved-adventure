<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\MemoryRequirementListener
 *
 * @internal
 */
final class MemoryRequirementListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    protected function setUp(): void
    {
        $this->context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\EventListener\\MemoryRequirementListener',
            null,
            [$this->context]
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

    public function testVerifyMemoryLimitWhenImageIsCompatible(): void
    {
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn(['memory_limit' => '120'])
        ;
        $this->layer->add([
            'gif.extracted' => [0, 0, 0, 0],
            'image.width' => 100,
            'image.height' => 100,
            'final.width' => 10,
            'final.height' => 10,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        self::assertArrayHasKey('memory_approx', $this->image->getExtras());
    }

    public function testVerifyMemoryLimitWhenImageIsIncompatible(): void
    {
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn(['memory_limit' => '120'])
        ;
        $this->layer->add([
            'gif.extracted' => [0, 0, 0, 0],
            'image.width' => 10_000_000_000_000_000_000_000,
            'image.height' => 10_000_000_000_000_000_000_000,
            'final.width' => 10,
            'final.height' => 10,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        self::assertArrayHasKey('gif_error', $this->image->getExtras());
    }
}
