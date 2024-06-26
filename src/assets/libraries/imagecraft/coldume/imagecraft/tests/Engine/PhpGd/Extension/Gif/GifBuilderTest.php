<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\GifBuilder
 *
 * @internal
 */
final class GifBuilderTest extends \PHPUnit\Framework\TestCase
{
    protected $extractor;

    protected function setUp(): void
    {
        $this->extractor = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->builder = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilder', null);
    }

    /**
     * @dataProvider provideGetContentsCases
     *
     * @param mixed $filename
     * @param mixed $outputName
     */
    public function testGetContents($filename, $outputName): void
    {
        @mkdir(__DIR__.'/TestOutput/'.$outputName);
        $extracted = $this->extractor->extractFromStream($filename);
        foreach ($extracted as $key => $frame) {
            $this->builder
                ->imageWidth($extracted->getImageWidth())
                ->imageHeight($extracted->getImageHeight())
                ->colorTable($extracted->getColorTable())
                ->interlaceFlag($extracted->getInterlaceFlag())
                ->imageData($extracted->getImageData())
            ;
            if ($extracted->getTransparentColorFlag()) {
                $index = $extracted->getTransparentColorIndex();
                $this->builder->transparentColorIndex($index);
            }
            $contents = $this->builder->getContents();
            file_put_contents(__DIR__.'/TestOutput/'.$outputName.'/'.$key.'.gif', $contents);
        }
    }

    public static function provideGetContentsCases(): iterable
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', 'gif_builder_frames_should_be_valid_01'],
        ];
    }
}
