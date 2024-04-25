<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\JpegQualityOptionPass
 *
 * @internal
 */
final class JpegQualityOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\JpegQualityOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame(100, $option['jpeg_quality']);

        $option = $this->pass->process(['jpeg_quality' => 200]);
        self::assertSame(100, $option['jpeg_quality']);

        $option = $this->pass->process(['jpeg_quality' => -100]);
        self::assertSame(100, $option['jpeg_quality']);
    }
}
