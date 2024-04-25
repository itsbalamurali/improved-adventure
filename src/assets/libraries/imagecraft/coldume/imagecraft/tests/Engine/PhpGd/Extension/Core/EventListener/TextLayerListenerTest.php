<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Exception\RuntimeException;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\TextLayerListener
 *
 * @internal
 */
final class TextLayerListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    protected $layer;

    protected function setUp(): void
    {
        $this->context = $this->createMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext');
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\TextLayerListener',
            null,
            [$this->context]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\TextLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
    }

    public function testVerifyFreeType(): void
    {
        $this->expectException(RuntimeException::class);

        $this->context
            ->expects(self::once())
            ->method('isFreeTypeSupported')
            ->willReturn(false)
        ;
        $this->listener->verifyFreeType($this->event);
    }
}
