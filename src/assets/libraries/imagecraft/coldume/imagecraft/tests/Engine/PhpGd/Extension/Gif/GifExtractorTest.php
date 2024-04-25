<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\GifExtractor
 *
 * @internal
 */
final class GifExtractorTest extends \PHPUnit\Framework\TestCase
{
    protected $extractor;

    protected function setUp(): void
    {
        $this->extractor = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
    }

    /**
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $validity
     */
    public function testExtractFromFilePointer($filename, $validity): void
    {
        $fp = fopen($filename, 'r');
        $extracted = $this->extractor->extractFromFilePointer($fp);
        self::assertSame($validity, $extracted->isValid());
    }

    /**
     * @depends      testExtractFromFilePointer
     *
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $validity
     */
    public function testExtractFromStream($filename, $validity): void
    {
        $extracted = $this->extractor->extractFromStream($filename, $validity);
        self::assertSame($validity, $extracted->isValid());
    }

    /**
     * @depends      testExtractFromFilePointer
     *
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $validity
     */
    public function testExtractFromContents($filename, $validity): void
    {
        $extracted = $this->extractor->extractFromContents(file_get_contents($filename), $validity);
        self::assertSame($validity, $extracted->isValid());
    }

    public static function imageDataProvider(): iterable
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', true],
            [__DIR__.'/../../../../Fixtures/gif_87a_palette_250x297.gif', true],
            [__DIR__.'/../../../../Fixtures/zz_gif_89a_palette_animated_no_graphic_control_550x296.gif', true],
            [__DIR__.'/../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif', false],
            [__DIR__.'/../../../../Fixtures/png_palette_alpha_3000x1174.png', false],
        ];
    }
}
