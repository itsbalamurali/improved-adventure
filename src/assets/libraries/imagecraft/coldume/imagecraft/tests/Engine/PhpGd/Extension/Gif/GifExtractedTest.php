<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\GifExtracted
 *
 * @internal
 */
final class GifExtractedTest extends \PHPUnit\Framework\TestCase
{
    protected $extractor;

    protected function setUp(): void
    {
        $this->extractor = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
    }

    /**
     * @dataProvider provideGettersForAnimatedGifCases
     *
     * @param mixed $filename
     * @param mixed $specs
     */
    public function testGettersForAnimatedGif($filename, $specs): void
    {
        $fp = fopen($filename, 'r');
        $extracted = $this->extractor->extractFromFilePointer($fp);
        self::assertTrue($extracted->isValid());
        self::assertTrue($extracted->isAnimated());

        foreach ($extracted as $key => $frame) {
            if (0 === $key) {
                continue;
            }
            self::assertNotEmpty($extracted->getImageData());
            self::assertNotEmpty($extracted->getColorTable());
        }

        $extracted->seek(2);
        self::assertSame($specs['total_frames'], \count($extracted));
        self::assertSame($specs['canvas_width'], $extracted->getCanvasWidth());
        self::assertSame($specs['canvas_height'], $extracted->getCanvasHeight());
        self::assertSame($specs['global_flag'], $extracted->getGlobalColorTableFlag());
        if ($specs['global_flag']) {
            self::assertSame($specs['total_globals'], $extracted->getTotalGlobalColors());
        }
        self::assertSame($specs['total_loops'], $extracted->getTotalLoops());
        self::assertSame($specs['disposal_method'], $extracted->getDisposalMethod());
        self::assertSame($specs['transparent_flag'], $extracted->getTransparentColorFlag());
        if ($specs['transparent_flag']) {
            self::assertSame($specs['transparent_index'], $extracted->getTransparentColorIndex());
        }
        self::assertSame($specs['delay_time'], $extracted->getDelayTime());
        self::assertSame($specs['image_left'], $extracted->getImageLeft());
        self::assertSame($specs['image_top'], $extracted->getImageTop());
        self::assertSame($specs['image_width'], $extracted->getImageWidth());
        self::assertSame($specs['image_height'], $extracted->getImageHeight());
        self::assertSame($specs['local_flag'], $extracted->getLocalColorTableFlag());
        if ($specs['local_flag']) {
            self::assertSame($specs['total_locals'], $extracted->getTotalLocalColors());
        }
        self::assertSame($specs['interlace_flag'], $extracted->getInterlaceFlag());
        self::assertNotEmpty($extracted->getLinkedKey());
        self::assertNotEmpty($extracted->getLinkedDisposalMethod());
    }

    public static function provideGettersForAnimatedGifCases(): iterable
    {
        $filename1 = __DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif';
        $specs1 = [
            'total_frames' => 19,
            'canvas_width' => 339,
            'canvas_height' => 473,
            'global_flag' => true,
            'total_globals' => 256,
            'total_loops' => 0,
            'disposal_method' => 2,
            'transparent_flag' => true,
            'transparent_index' => 53,
            'delay_time' => 10,
            'image_left' => 0,
            'image_top' => 0,
            'image_width' => 339,
            'image_height' => 473,
            'local_flag' => false,
            'interlace_flag' => false,
        ];

        return [
            [$filename1, $specs1],
        ];
    }
}
