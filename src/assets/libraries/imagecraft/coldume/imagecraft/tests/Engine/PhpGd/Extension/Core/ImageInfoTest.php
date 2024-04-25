<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Exception\InvalidImageException;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\ImageInfo
 *
 * @internal
 */
final class ImageInfoTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected $info;

    protected function setUp(): void
    {
        $this->context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', []);
        $this->info = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageInfo', null, [$this->context]);
    }

    /**
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $format
     * @param mixed $width
     * @param mixed $height
     */
    public function testResolveFromFilePointer($filename, $format, $width, $height): void
    {
        $this->context
            ->expects(self::atLeastOnce())
            ->method('isImageFormatSupported')
            ->willReturn(true)
        ;
        $fp = fopen($filename, 'r');
        $info = $this->info->resolveFromFilePointer($fp);
        self::assertSame($format, $info['format']);
        self::assertSame($width, $info['width']);
        self::assertSame($height, $info['height']);
    }

    /**
     * @depends      testResolveFromFilePointer
     *
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $format
     * @param mixed $width
     * @param mixed $height
     */
    public function testResolveFromStream($filename, $format, $width, $height): void
    {
        $this->context
            ->expects(self::atLeastOnce())
            ->method('isImageFormatSupported')
            ->willReturn(true)
        ;
        $info = $this->info->resolveFromStream($filename);
        self::assertSame($format, $info['format']);
        self::assertSame($width, $info['width']);
        self::assertSame($height, $info['height']);
    }

    /**
     * @depends      testResolveFromFilePointer
     *
     * @dataProvider imageDataProvider
     *
     * @param mixed $filename
     * @param mixed $format
     * @param mixed $width
     * @param mixed $height
     */
    public function testResolveFromContents($filename, $format, $width, $height): void
    {
        $this->context
            ->expects(self::atLeastOnce())
            ->method('isImageFormatSupported')
            ->willReturn(true)
        ;
        $info = $this->info->resolveFromContents(file_get_contents($filename));
        self::assertSame($format, $info['format']);
        self::assertSame($width, $info['width']);
        self::assertSame($height, $info['height']);
    }

    /**
     * @requires          extension fileinfo
     *
     * @depends           testResolveFromFilePointer
     */
    public function testResolveInvalidImage(): void
    {
        $this->expectException(InvalidImageException::class);

        $this->context
            ->expects(self::atLeastOnce())
            ->method('isFileinfoExtensionEnabled')
            ->willReturn(true)
        ;
        $fp = fopen(__FILE__, 'r');
        $this->info->resolveFromFilePointer($fp);
    }

    /**
     * @depends           testResolveFromFilePointer
     *
     * @dataProvider      provideResolveInvalidWebpCases
     *
     * @param mixed $filename
     */
    public function testResolveInvalidWebp($filename): void
    {
        $this->expectException(InvalidImageException::class);

        $this->info->resolveFromFilePointer(fopen($filename, 'r'));
    }

    public static function imageDataProvider(): iterable
    {
        return [
            [__DIR__.'/../../../../Fixtures/webp_vp8_lossy_truecolor_550x368.webp', PhpGdContext::FORMAT_WEBP, 550, 368],
            [__DIR__.'/../../../../Fixtures/gif_87a_palette_250x297.gif', PhpGdContext::FORMAT_GIF, 250, 297],
            [__DIR__.'/../../../../Fixtures/png_truecolor_alpha_300x395.png', PhpGdContext::FORMAT_PNG, 300, 395],
            [__DIR__.'/../../../../Fixtures/png_palette_alpha_3000x1174.png', PhpGdContext::FORMAT_PNG, 3_000, 1_174],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_truecolor_1920x758.jpg', PhpGdContext::FORMAT_JPEG, 1_920, 758],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_grayscale_480x361.jpg', PhpGdContext::FORMAT_JPEG, 480, 361],
            [__DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg', PhpGdContext::FORMAT_JPEG, 480, 360],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_sos_truecolor_1920x1200.jpg', PhpGdContext::FORMAT_JPEG, 1_920, 1_200],
        ];
    }

    public static function provideResolveInvalidWebpCases(): iterable
    {
        return [
            [__DIR__.'/../../../../Fixtures/webp_vp8l_lossless_truecolor_alpha_800x600.webp'],
            [__DIR__.'/../../../../Fixtures/webp_vp8x_lossy_truecolor_alpha_421x163.webp'],
        ];
    }
}
