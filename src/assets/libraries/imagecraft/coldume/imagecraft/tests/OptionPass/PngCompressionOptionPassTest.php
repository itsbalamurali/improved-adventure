<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\PngCompressionOptionPass
 *
 * @internal
 */
final class PngCompressionOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\PngCompressionOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame(100, $option['png_compression']);

        $option = $this->pass->process(['png_compression' => 200]);
        self::assertSame(100, $option['png_compression']);

        $option = $this->pass->process(['png_compression' => -100]);
        self::assertSame(100, $option['png_compression']);
    }
}
