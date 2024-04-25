<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\DelegatingOptionPass
 *
 * @internal
 */
final class DelegatingOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\DelegatingOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertIsArray($option);
    }
}
