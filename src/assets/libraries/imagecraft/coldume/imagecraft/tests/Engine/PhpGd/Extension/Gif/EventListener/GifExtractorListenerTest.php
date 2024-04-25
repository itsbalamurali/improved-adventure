<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\GifExtractorListener
 *
 * @internal
 */
final class GifExtractorListenerTest extends \PHPUnit\Framework\TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    protected function setUp(): void
    {
        $extractor = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\EventListener\\GifExtractorListener',
            null,
            [$extractor]
        );
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->layer->add(['final.format' => PhpGdContext::FORMAT_GIF, 'image.format' => PhpGdContext::FORMAT_GIF]);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->willReturn([$this->layer])
        ;
        $this->image = $this->createPartialMock('Imagecraft\\Image', null);
        $this->event
            ->method('getImage')
            ->willReturn($this->image)
        ;
    }

    /**
     * @dataProvider provideInitExtractedCases
     *
     * @param mixed $filename
     */
    public function testInitExtracted($filename): void
    {
        $this->event
            ->method('getOptions')
            ->willReturn(['gif_animation' => true])
        ;
        $this->layer->set('image.fp', fopen($filename, 'r'));
        $this->listener->initExtracted($this->event);
        self::assertTrue($this->layer->has('gif.extracted'));
    }

    /**
     * @dataProvider provideInitExtractedWhenGifIsInvalidCases
     *
     * @param mixed $filename
     */
    public function testInitExtractedWhenGifIsInvalid($filename): void
    {
        $this->event
            ->method('getOptions')
            ->willReturn(['gif_animation' => true])
        ;
        $this->layer->set('image.fp', fopen($filename, 'r'));
        $this->listener->initExtracted($this->event);
        self::assertFalse($this->layer->has('gif.extracted'));
    }

    /**
     * @depends      testInitExtractedWhenGifIsInvalid
     *
     * @dataProvider provideAddImageExtrasCases
     *
     * @param mixed $filename
     */
    public function testAddImageExtras($filename): void
    {
        $this->event
            ->method('getOptions')
            ->willReturn(['gif_animation' => true])
        ;
        $this->layer->set('image.fp', fopen($filename, 'r'));
        $this->listener->initExtracted($this->event);
        $this->listener->addImageExtras($this->event);
        self::assertNotEmpty($this->image->getExtras()['gif_error']);
    }

    public static function provideInitExtractedCases(): iterable
    {
        return [
            [__DIR__.'/../../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif'],
        ];
    }

    public static function provideInitExtractedWhenGifIsInvalidCases(): iterable
    {
        return [
            [__DIR__.'/../../../../../Fixtures/gif_87a_palette_250x297.gif'],
            [__DIR__.'/../../../../../Fixtures/gif_89a_palette_alpha_206x205.gif'],
            [__DIR__.'/../../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif'],
            [__DIR__.'/../../../../../Fixtures/jpeg_jfjf_truecolor_1920x758.jpg'],
        ];
    }

    public static function provideAddImageExtrasCases(): iterable
    {
        return [
            [__DIR__.'/../../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif'],
        ];
    }
}
