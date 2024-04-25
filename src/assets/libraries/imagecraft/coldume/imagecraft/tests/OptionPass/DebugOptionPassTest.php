<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\DebugOptionPass
 *
 * @internal
 */
final class DebugOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\DebugOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertTrue($option['debug']);
    }
}
