<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\EngineOptionPass
 *
 * @internal
 */
final class EngineOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\EngineOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame('php_gd', $option['engine']);
    }
}
