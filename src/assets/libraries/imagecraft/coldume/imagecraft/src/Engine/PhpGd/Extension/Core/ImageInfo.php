<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Exception\InvalidImageException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class ImageInfo
{
    /**
     * @var PhpGdContext
     */
    protected $context;

    public function __construct(PhpGdContext $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $stream
     *
     * @return (int|string)[]
     */
    public function resolveFromStream($stream)
    {
        $fp = fopen($stream, 'r');
        $info = $this->resolveFromFilePointer($fp);
        @fclose($fp);

        return $info;
    }

    /**
     * @param string $contents
     *
     * @return (int|string)[]
     */
    public function resolveFromContents($contents)
    {
        $fp = fopen('php://temp', 'r+');
        fwrite($fp, $contents);
        $info = $this->resolveFromFilePointer($fp);
        @fclose($fp);

        return $info;
    }

    /**
     * @param resource $fp
     *
     * @return (int|string)[]
     */
    public function resolveFromFilePointer($fp)
    {
        $methods = ['resolveWebp', 'resolveGif', 'resolvePng', 'resolveJpeg'];
        foreach ($methods as $method) {
            if (false !== $info = \call_user_func_array([$this, $method], [$fp])) {
                break;
            }
        }
        if (false === $info || !$this->context->isImageFormatSupported($info['format'])) {
            $this->handleException($fp);
        }

        return $info;
    }

    /**
     * @param resource $fp
     *
     * @return false|(int|string)[]
     */
    protected function resolveWebp($fp)
    {
        rewind($fp);
        $contents = fread($fp, 34);
        if (preg_match('/(?s)\\ARIFF.{4}WEBPVP8(X|L)/s', $contents, $matches)) {
            $supported = $this->context->getSupportedImageFormatsToString();

            throw new InvalidImageException(
                'unsupported.image.format.or.file.corrupted.%unsupported%.%supported%',
                ['%unsupported%' => '"WEBP (VP8'.$matches[1].')"', '%supported%' => $supported]
            );
        }
        $pattern = '/(?s)\\ARIFF.{4}WEBPVP8\\s.{10}(?<width>.{2})(?<height>.{2})/s';
        if (preg_match($pattern, $contents, $matches)) {
            $width = unpack('v', $matches['width'])[1];
            $height = unpack('v', $matches['height'])[1];

            return [
                'format' => PhpGdContext::FORMAT_WEBP,
                'width' => $width,
                'height' => $height,
            ];
        }

        return false;
    }

    /**
     * @param resource $fp
     *
     * @return false|(int|string)[]
     */
    protected function resolveGif($fp)
    {
        rewind($fp);
        $contents = fread($fp, 10);
        if (preg_match('/(?s)\\AGIF8(7|9)a(?<width>.{2})(?<height>.{2})/s', $contents, $matches)) {
            $width = unpack('v', $matches['width'])[1];
            $height = unpack('v', $matches['height'])[1];

            return [
                'format' => PhpGdContext::FORMAT_GIF,
                'width' => $width,
                'height' => $height,
            ];
        }

        return false;
    }

    /**
     * @param resource $fp
     *
     * @return false|(int|string)[]
     */
    protected function resolvePng($fp)
    {
        rewind($fp);
        $contents = fread($fp, 25);
        if (preg_match(
            '/\\A\\x89PNG\\x0d\\x0a\\x1a\\x0a(?:.{4})IHDR(?<width>.{4})(?<height>.{4})/s',
            $contents,
            $matches
        )) {
            $width = unpack('N', $matches['width'])[1];
            $height = unpack('N', $matches['height'])[1];

            return [
                'format' => PhpGdContext::FORMAT_PNG,
                'width' => $width,
                'height' => $height,
            ];
        }

        return false;
    }

    /**
     * @param resource $fp
     *
     * @return false|(int|string)[]
     */
    protected function resolveJpeg($fp)
    {
        rewind($fp);
        if ("\xff\xd8" === fread($fp, 2)) {
            while (!feof($fp)) {
                if (isset($c)) {
                    $char = $c;
                    unset($c);
                } else {
                    if ("\xff" !== fread($fp, 1)) {
                        break;
                    }
                    $char = fread($fp, 1);
                }
                if ("\xc0" === $char || "\xc2" === $char) {
                    fread($fp, 3);
                    $height = unpack('n', fread($fp, 2))[1];
                    $width = unpack('n', fread($fp, 2))[1];

                    return [
                        'format' => PhpGdContext::FORMAT_JPEG,
                        'width' => $width,
                        'height' => $height,
                    ];
                }
                if (preg_match('/[\\xd0-\\xd7]|\\x01/', $char)) {
                    continue;
                }
                if ("\xda" === $char) {
                    $p = null;
                    while (!feof($fp)) {
                        $c = fread($fp, 1);
                        if ("\x00" !== $c && "\xff" === $p) {
                            break;
                        }
                        $p = $c;
                    }
                } else {
                    $length = fread($fp, 2);
                    $length = unpack('n', $length)[1];
                    $remainder = ($length - 2) % 1_024;
                    if ($remainder > 0) {
                        fread($fp, $remainder);
                    }
                    $quotient = floor(($length - 2) / 1_024);
                    if ($quotient) {
                        for ($i = 0; $i < $quotient; ++$i) {
                            fread($fp, 1_024);
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param resource $fp
     *
     * @throws InvalidImageException
     */
    protected function handleException($fp): void
    {
        rewind($fp);
        $contents = fread($fp, 2_048);
        $supported = $this->context->getSupportedImageFormatsToString();
        $mime = null;
        if ($this->context->isFileinfoExtensionEnabled()) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = @$finfo->buffer($contents);
            $mime = ($mime && 'binary' !== $mime) ? $mime : null;
        }
        if ($mime) {
            $unsupported = explode('/', $mime);
            $unsupported = strtoupper(array_pop($unsupported));

            throw new InvalidImageException(
                'unsupported.image.format.or.file.corrupted.%unsupported%.%supported%',
                ['%unsupported%' => '"'.$unsupported.'"', '%supported%' => $supported]
            );
        }

        throw new InvalidImageException(
            'unknown.image.format.or.file.corrupted.%supported%',
            ['%supported%' => $supported]
        );
    }
}
