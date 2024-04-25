<?php



namespace Imagecraft\Engine\PhpGd\Helper;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Exception\InvalidFontException;
use Imagecraft\Exception\InvalidImageException;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Layer\RegularLayerInterface;

/**
 * @requires extension gd
 * @requires function imagegif
 * @requires function imagecreatefromgif
 * @requires function imagecreatefromwebp
 * @requires function imagecreatefromjpeg
 * @requires function imagecreatefrompng
 * @requires function imagefttext
 *
 * @covers   \Imagecraft\Engine\PhpGd\Helper\ResourceHelper
 *
 * @internal
 */
final class ResourceHelperTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->rh = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
    }

    public function testGetEmptyGdResource(): void
    {
        $resource = $this->rh->getEmptyGdResource(100, 100);
        self::assertIsResource($resource);
        self::assertSame(100, imagesx($resource));
        self::assertSame(100, imagesy($resource));
        imagepng($resource, __DIR__.'/TestOutput/resource_helper_image_should_be_blank.png');
        imagedestroy($resource);
    }

    public function testGetPalettizedGdResource(): void
    {
        $resource = imagecreatetruecolor(100, 100);
        $resource = $this->rh->getPalettizedGdResource($resource);
        self::assertIsResource($resource);
        self::assertFalse(imageistruecolor($resource));
        imagedestroy($resource);
    }

    /**
     * @dataProvider imageDataProvider
     *
     * @param mixed $format
     * @param mixed $uri
     */
    public function testGetGdResourceFromStream($format, $uri): void
    {
        $resource = $this->rh->getGdResourceFromStream($format, $uri);
        self::assertIsResource($resource);
    }

    public function testGetGdResourceFromStreamWhenStreamIsInvalid(): void
    {
        $this->expectException(InvalidImageException::class);

        $this->rh->getGdResourceFromStream(PhpGdContext::FORMAT_PNG, __FILE__, true);
    }

    /**
     * @dataProvider imageDataProvider
     *
     * @param mixed $format
     * @param mixed $stream
     */
    public function testGetGdResourceFromContents($format, $stream): void
    {
        self::assertIsResource($this->rh->getGdResourceFromContents($format, file_get_contents($stream))
        );
    }

    public function testGetGdResourceFromContentsWhenContentsIsInvalid(): void
    {
        $this->expectException(InvalidImageException::class);

        $this->rh->getGdResourceFromContents(PhpGdContext::FORMAT_WEBP, file_get_contents(__FILE__), true);
    }

    /**
     * @depends      testGetGdResourceFromContents
     * @depends      testGetPalettizedGdResource
     *
     * @dataProvider imageDataProvider
     *
     * @param mixed $format
     * @param mixed $uri
     */
    public function testGetContentsFromGdResource($format, $uri): void
    {
        $contents = file_get_contents($uri);
        $resource = $this->rh->getGdResourceFromContents($format, $contents, true);
        $contents = $this->rh->getContentsFromGdResource(
            $format,
            $resource,
            ['jpeg_quality' => 77, 'png_compression' => 46],
            true
        );
        self::assertNotEmpty($contents);
        imagedestroy($resource);
    }

    public function testGetContentsFromGdResourceWhenResourceIsInvalid(): void
    {
        $this->expectException(InvalidImageException::class);

        $this->rh->getContentsFromGdResource(PhpGdContext::FORMAT_WEBP, 'foo', [], true);
    }

    /**
     * @dataProvider resizeDataProvider
     *
     * @param mixed $originalWidth
     * @param mixed $originalHeight
     * @param mixed $resizeWidth
     * @param mixed $resizeHeight
     * @param mixed $resizeOption
     * @param mixed $expectedDstW
     * @param mixed $expectedDstH
     */
    public function testGetResizeArguments(
        $originalWidth,
        $originalHeight,
        $resizeWidth,
        $resizeHeight,
        $resizeOption,
        $expectedDstW,
        $expectedDstH
    ): void {
        $args = $this->rh->getResizeArguments($originalWidth, $originalHeight, $resizeWidth, $resizeHeight, $resizeOption);
        self::assertIsArray($args);
        self::assertSame($expectedDstW, $args['dst_w']);
        self::assertSame($expectedDstH, $args['dst_h']);
    }

    public function testGetResizeArgumentsWhenNoResizeIsNeeded(): void
    {
        $args = $this->rh->getResizeArguments(
            100,
            100,
            100,
            100,
            ImageAwareLayerInterface::RESIZE_SHRINK
        );
        self::assertFalse($args);

        $args = $this->rh->getResizeArguments(
            100,
            100,
            200,
            200,
            ImageAwareLayerInterface::RESIZE_SHRINK
        );
        self::assertFalse($args);
    }

    /**
     * @depends      testGetResizeArguments
     * @depends      testGetEmptyGdResource
     *
     * @dataProvider resizeDataProvider
     *
     * @param mixed $originalWidth
     * @param mixed $originalHeight
     * @param mixed $resizeWidth
     * @param mixed $resizeHeight
     * @param mixed $resizeOption
     * @param mixed $expectedDstW
     * @param mixed $expectedDstH
     */
    public function testGetResizedGdResource(
        $originalWidth,
        $originalHeight,
        $resizeWidth,
        $resizeHeight,
        $resizeOption,
        $expectedDstW,
        $expectedDstH
    ): void {
        $resource = imagecreate($originalWidth, $originalHeight);
        $resource = $this->rh->getResizedGdResource($resource, $resizeWidth, $resizeHeight, $resizeOption, true);
        self::assertSame($expectedDstW, imagesx($resource));
        self::assertSame($expectedDstH, imagesy($resource));
        imagedestroy($resource);

        $resource = imagecreate($originalWidth, $originalHeight);
        $resource = $this->rh->getResizedGdResource($resource, $resizeWidth, $resizeHeight, $resizeOption, false);
        self::assertSame($expectedDstW, imagesx($resource));
        self::assertSame($expectedDstH, imagesy($resource));
        imagedestroy($resource);

        $resource1 = imagecreate($originalWidth, $originalHeight);
        $resource2 = $this->rh->getResizedGdResource($resource1, $originalWidth, $originalHeight, $resizeOption, false);
        self::assertTrue($resource1 === $resource2);
        imagedestroy($resource1);
    }

    /**
     * @depends      testGetEmptyGdResource
     *
     * @dataProvider provideGetMergedGdResourceCases
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $gravity
     */
    public function testGetMergedGdResource($x, $y, $gravity): void
    {
        $srcResource = imagecreatetruecolor(100, 100);
        $dstResource = imagecreate(300, 300);
        imagecolorallocate($dstResource, 255, 0, 0);
        $resource = $this->rh->getMergedGdResource($dstResource, $srcResource, $x, $y, $gravity);
        self::assertIsResource($resource);
        imagepng($resource, __DIR__.'/TestOutput/resource_helper_image_should_be_merged_'.$x.'_'.$y.'_'.$gravity.'.png');
        imagedestroy($resource);
    }

    /**
     * @depends testGetEmptyGdResource
     * @depends testGetMergedGdResource
     * @depends testGetPalettizedGdResource
     */
    public function testGetClonedGdResource(): void
    {
        $resource = imagecreate(100, 100);
        $clonedResource = $this->rh->getClonedGdResource($resource);
        self::assertIsResource($clonedResource);
        self::assertFalse($resource === $clonedResource);
        self::assertFalse(imageistruecolor($clonedResource));
    }

    /**
     * @depends      testGetEmptyGdResource
     *
     * @dataProvider provideGetTextGdResourceCases
     *
     * @param mixed $filename
     */
    public function testGetTextGdResource($filename): void
    {
        $resource = $this->rh->getTextGdResource(
            $filename,
            25,
            [0, 0, 0],
            'Hello World',
            1.5,
            60,
            [0, 0, 0, 0],
            [133, 133, 133]
        );
        self::assertIsResource($resource);
        imagepng($resource, __DIR__.'/TestOutput/resource_helper_text_should_be_valid.png');
        @imagedestroy($resource);
    }

    public function testGetTextGdResourceWhenFontIsInvalid(): void
    {
        $this->expectException(InvalidFontException::class);

        $this->rh->getTextGdResource(
            __FILE__,
            12,
            [0, 0, 0],
            'Hello World',
            1.5,
            60,
            [0, 0, 0, 0],
            [133, 133, 111]
        );
    }

    public static function imageDataProvider(): iterable
    {
        return [
            [PhpGdContext::FORMAT_PNG, __DIR__.'/../../../Fixtures/png_palette_alpha_3000x1174.png'],
            [PhpGdContext::FORMAT_WEBP, __DIR__.'/../../../Fixtures/webp_vp8_lossy_truecolor_550x368.webp'],
            [PhpGdContext::FORMAT_JPEG, __DIR__.'/../../../Fixtures/jpeg_jfjf_truecolor_1920x758.jpg'],
            [PhpGdContext::FORMAT_GIF, __DIR__.'/../../../Fixtures/gif_87a_palette_250x297.gif'],
        ];
    }

    public static function resizeDataProvider(): iterable
    {
        return [
            [200, 100, 100, 100, ImageAwareLayerInterface::RESIZE_SHRINK, 100, 50],
            [100, 200, 100, 100, ImageAwareLayerInterface::RESIZE_SHRINK, 50, 100],
            [200, 100, 50, 50, ImageAwareLayerInterface::RESIZE_FILL_CROP, 50, 50],
            [100, 200, 50, 50, ImageAwareLayerInterface::RESIZE_FILL_CROP, 50, 50],
        ];
    }

    public static function provideGetMergedGdResourceCases(): iterable
    {
        return [
            [50, 50, RegularLayerInterface::MOVE_TOP_LEFT],
            [50, 50, RegularLayerInterface::MOVE_TOP_CENTER],
            [50, 50, RegularLayerInterface::MOVE_TOP_RIGHT],
            [50, 50, RegularLayerInterface::MOVE_CENTER_LEFT],
            [50, 50, RegularLayerInterface::MOVE_CENTER],
            [50, 50, RegularLayerInterface::MOVE_CENTER_RIGHT],
            [50, 50, RegularLayerInterface::MOVE_BOTTOM_LEFT],
            [50, 50, RegularLayerInterface::MOVE_BOTTOM_CENTER],
            [50, 50, RegularLayerInterface::MOVE_BOTTOM_RIGHT],
            [-50, -50, RegularLayerInterface::MOVE_CENTER],
            [-500, -500, RegularLayerInterface::MOVE_CENTER],
        ];
    }

    public static function provideGetTextGdResourceCases(): iterable
    {
        return [
            [__DIR__.'/../../../Fixtures/pfa_truecolor_alpha.pfa'],
        ];
    }
}
