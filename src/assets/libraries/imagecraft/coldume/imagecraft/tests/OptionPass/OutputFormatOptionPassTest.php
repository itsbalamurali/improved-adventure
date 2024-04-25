<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\OutputFormatOptionPass
 *
 * @internal
 */
final class OutputFormatOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\OutputFormatOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame('default', $option['output_format']);
    }
}
