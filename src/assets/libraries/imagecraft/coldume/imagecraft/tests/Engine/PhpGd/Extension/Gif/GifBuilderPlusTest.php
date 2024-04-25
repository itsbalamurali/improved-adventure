<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\GifBuilderPlus
 *
 * @internal
 */
final class GifBuilderPlusTest extends \PHPUnit\Framework\TestCase
{
    protected $extractor;

    protected $builder;

    protected function setUp(): void
    {
        $this->extractor = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->builder = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilderPlus', null);
    }

    /**
     * @dataProvider provideGetContentsCases
     *
     * @param mixed $filename
     * @param mixed $outputName
     */
    public function testGetContents($filename, $outputName): void
    {
        $extracted = $this->extractor->extractFromStream($filename);

        $this->builder
            ->canvasWidth($extracted->getCanvasWidth())
            ->canvasHeight($extracted->getCanvasHeight())
            ->loop($extracted->getTotalLoops())
        ;
        for ($i = 0; $i < \count($extracted); ++$i) {
            $this->builder->addFrame();
            $this->builder
                ->imageWidth($extracted->getImageWidth())
                ->imageHeight($extracted->getImageHeight())
                ->imageLeft($extracted->getImageLeft())
                ->imageTop($extracted->getImageTop())
                ->dispose($extracted->getDisposalMethod())
                ->delayTime($extracted->getDelayTime())
                ->interlaceFlag($extracted->getInterlaceFlag())
                ->colorTable($extracted->getColorTable())
                ->imageData($extracted->getImageData())
            ;
            if ($extracted->getTransparentColorFlag()) {
                $this->builder->transparentColorIndex($extracted->getTransparentColorIndex());
            }
            $extracted->next();
        }
        file_put_contents(__DIR__.'/TestOutput/'.$outputName, $this->builder->getContents());
    }

    public static function provideGetContentsCases(): iterable
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', 'gif_builder_plus_image_should_be_animated_01.gif'],
        ];
    }
}
