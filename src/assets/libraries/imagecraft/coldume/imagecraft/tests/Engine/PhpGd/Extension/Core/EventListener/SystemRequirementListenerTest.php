<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Exception\InvalidArgumentException;
use Imagecraft\Exception\RuntimeException;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\EventListener\SystemRequirementListener
 *
 * @internal
 */
final class SystemRequirementListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    protected function setUp(): void
    {
        $this->context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', []);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\SystemRequirementListener',
            null,
            [$this->context]
        );
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
    }

    public function testVerifyEngine(): void
    {
        $this->expectException(RuntimeException::class);

        $this->context
            ->expects(self::once())
            ->method('isEngineSupported')
            ->willReturn(false)
        ;
        $this->listener->verifyEngine();
    }

    public function testVerifySavedFormatWhenFormatIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->context
            ->expects(self::once())
            ->method('isImageFormatSupported')
            ->willReturn(false)
        ;
        $this->event
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn(['output_format' => 'foo'])
        ;
        $this->listener->verifySavedFormat($this->event);
    }
}
