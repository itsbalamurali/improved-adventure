<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\MemoryLimitOptionPass
 *
 * @internal
 */
final class MemoryLimitOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\MemoryLimitOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame(-10, $option['memory_limit']);
    }
}
