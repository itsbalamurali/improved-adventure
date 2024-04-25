<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Exception\RuntimeException;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\MemoryRequirementListener
 *
 * @internal
 */
final class MemoryRequirementListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    protected function setUp(): void
    {
        $context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\MemoryRequirementListener',
            null,
            [$context]
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

    public function testVerifyMemoryLimit(): void
    {
        $this->expectException(RuntimeException::class);

        $this->event
            ->method('getOptions')
            ->willReturn(30 * 1_024 * 1_024)
        ;
        $this->layer->add([
            'image.format' => PhpGdContext::FORMAT_JPEG,
            'image.width' => 1_000_000,
            'image.height' => 1_000_000,
            'final.width' => 200,
            'final.height' => 200,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
    }

    public function testAddImageExtras(): void
    {
        $this->event
            ->method('getOptions')
            ->willReturn(50 * 1_024 * 1_024)
        ;
        $this->layer->add([
            'image.format' => PhpGdContext::FORMAT_JPEG,
            'image.width' => 100,
            'image.height' => 100,
            'final.width' => 100,
            'final.height' => 100,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        self::assertNotEmpty($this->image->getExtras()['memory_approx']);
    }
}
