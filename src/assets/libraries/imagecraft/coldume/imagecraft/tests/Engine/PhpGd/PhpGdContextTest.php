<?php



namespace Imagecraft\Engine\PhpGd;

/**
 * @requires extension gd
 * @requires function imagegif
 * @requires function imagecreatefromgif
 * @requires function imagecreatefromwebp
 * @requires function imagecreatefromjpeg
 * @requires function imagecreatefrompng
 * @requires function imagefttext
 *
 * @covers   \Imagecraft\Engine\PhpGd\PhpGdContext
 *
 * @internal
 */
final class PhpGdContextTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected function setUp(): void
    {
        $this->context = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
    }

    public function testIsImageFormatSupported(): void
    {
        self::assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_WEBP));
        self::assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_PNG));
        self::assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_JPEG));
        self::assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_GIF));
        self::assertFalse($this->context->isImageFormatSupported('foo'));
    }

    public function testGetSupportedImageFormatsToString(): void
    {
        self::assertSame('"WEBP (VP8)", "PNG", "JPEG", "GIF"', $this->context->getSupportedImageFormatsToString());
    }

    public function testGetImageMime(): void
    {
        self::assertSame('image/webp', $this->context->getImageMime(PhpGdContext::FORMAT_WEBP));
    }

    public function testGetImageExtension(): void
    {
        self::assertSame('webp', $this->context->getImageExtension(PhpGdContext::FORMAT_WEBP));
    }

    public function testIsEngineSupported(): void
    {
        self::assertTrue($this->context->isEngineSupported());
    }

    public function testIsFreeTypeSupported(): void
    {
        self::assertTrue($this->context->isFreeTypeSupported());
    }

    public function testGetSupportedFontFormatsToString(): void
    {
        self::assertIsString($this->context->getSupportedFontFormatsToString());
    }
}
