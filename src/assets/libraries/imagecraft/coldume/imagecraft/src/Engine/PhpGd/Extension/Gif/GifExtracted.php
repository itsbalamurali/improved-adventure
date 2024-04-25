<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class GifExtracted implements \Countable, \Iterator
{
    public const DISPOSAL_METHOD_NONE = 1;
    public const DISPOSAL_METHOD_BACKGROUND = 2;
    public const DISPOSAL_METHOD_PREVIOUS = 3;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $logicalScreenDescriptor;

    /**
     * @var null|string
     */
    protected $globalColorTable;

    /**
     * @var null|string
     */
    protected $netscapeExtenstion;

    /**
     * @var null|string[]
     */
    protected $graphicControlExtensions = [];

    /**
     * @var string[]
     */
    protected $imageDescriptors = [];

    /**
     * @var null|string[]
     */
    protected $localColorTables = [];

    /**
     * @var string[]
     */
    protected $imageDatas = [];

    /**
     * @var (null|int)[]
     */
    protected $linkedKeys = [];

    /**
     * @var int[]
     */
    protected $linkedDisposalMethods = [];

    /**
     * @var int
     */
    protected $fp = 0;

    /**
     * @var bool
     */
    protected $valid = true;

    /**
     * @param string $contents
     */
    public function setHeader($contents): void
    {
        $this->header = $contents;
    }

    /**
     * @param string $contents
     */
    public function setLogicalScreenDescriptor($contents): void
    {
        $this->logicalScreenDescriptor = $contents;
    }

    /**
     * @param string $contents
     */
    public function setGlobalColorTable($contents): void
    {
        $this->globalColorTable = $contents;
    }

    /**
     * @param string $contents
     */
    public function setNetscapeExtension($contents): void
    {
        $this->netscapeExtenstion = $contents;
    }

    /**
     * @return bool
     */
    public function hasNetscapeExtension()
    {
        return isset($this->netscapeExtenstion);
    }

    /**
     * @param string $contents
     */
    public function setGraphicControlExtension($contents): void
    {
        $this->graphicControlExtensions[$this->fp] = $contents;
    }

    /**
     * @return bool
     */
    public function hasGraphicControlExtension()
    {
        return isset($this->graphicControlExtensions[$this->fp]);
    }

    /**
     * @param string $contents
     */
    public function setImageDescriptor($contents): void
    {
        $this->imageDescriptors[$this->fp] = $contents;
    }

    /**
     * @param string $contents
     */
    public function setLocalColorTable($contents): void
    {
        $this->localColorTables[$this->fp] = $contents;
    }

    /**
     * @param string $contents
     */
    public function setImageData($contents): void
    {
        $this->imageDatas[$this->fp] = $contents;
    }

    /**
     * @param int $key
     */
    public function setLinkedKey($key): void
    {
        $this->linkedKeys[$this->fp] = $key;
    }

    /**
     * @param int $method
     */
    public function setLinkedDisposalMethod($method): void
    {
        $this->linkedDisposalMethods[$this->fp] = $method;
    }

    /**
     * @return int
     */
    public function getCanvasWidth()
    {
        return unpack('v', substr($this->logicalScreenDescriptor, 0, 2))[1];
    }

    /**
     * @return int
     */
    public function getCanvasHeight()
    {
        return unpack('v', substr($this->logicalScreenDescriptor, 2, 2))[1];
    }

    /**
     * @return bool
     */
    public function getGlobalColorTableFlag()
    {
        $packed = substr($this->logicalScreenDescriptor, 4, 1);

        return (bool) (unpack('C', $packed)[1] & 0b10000000);
    }

    /**
     * @return int
     */
    public function getTotalGlobalColors()
    {
        $packed = substr($this->logicalScreenDescriptor, 4, 1);

        return 2 ** ((unpack('C', $packed)[1] & 0b00000111) + 1);
    }

    /**
     * @return int
     */
    public function getTotalLoops()
    {
        return unpack('v', substr($this->netscapeExtenstion, 14, 2))[1];
    }

    /**
     * @return int
     */
    public function getDisposalMethod()
    {
        $unpack = unpack('C', substr($this->graphicControlExtensions[$this->fp], 1, 1))[1] >> 2 & 0b00000111;

        switch ($unpack) {
            case 2:
                $method = static::DISPOSAL_METHOD_BACKGROUND;

                break;

            case 3:
                $method = static::DISPOSAL_METHOD_PREVIOUS;

                break;

            default:
                $method = static::DISPOSAL_METHOD_NONE;
        }

        return $method;
    }

    /**
     * @return bool
     */
    public function getTransparentColorFlag()
    {
        return (bool) (unpack('C', substr($this->graphicControlExtensions[$this->fp], 1, 1))[1] & 0b00000001);
    }

    /**
     * @return int
     */
    public function getTransparentColorIndex()
    {
        return unpack('C', substr($this->graphicControlExtensions[$this->fp], 4, 1))[1];
    }

    /**
     * @return int
     */
    public function getDelayTime()
    {
        return unpack('v', substr($this->graphicControlExtensions[$this->fp], 2, 2))[1];
    }

    /**
     * @return int
     */
    public function getImageLeft()
    {
        return unpack('v', substr($this->imageDescriptors[$this->fp], 0, 2))[1];
    }

    /**
     * @return int
     */
    public function getImageTop()
    {
        return unpack('v', substr($this->imageDescriptors[$this->fp], 2, 2))[1];
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return unpack('v', substr($this->imageDescriptors[$this->fp], 4, 2))[1];
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return unpack('v', substr($this->imageDescriptors[$this->fp], 6, 2))[1];
    }

    /**
     * @return bool
     */
    public function getInterlaceFlag()
    {
        $packed = $this->imageDescriptors[$this->fp][8];

        return (bool) (unpack('C', $packed)[1] & 0b01000000);
    }

    /**
     * @return bool
     */
    public function getLocalColorTableFlag()
    {
        $packed = $this->imageDescriptors[$this->fp][8];

        return (bool) (unpack('C', $packed)[1] & 0b10000000);
    }

    /**
     * @return int
     */
    public function getTotalLocalColors()
    {
        $packed = $this->imageDescriptors[$this->fp][8];

        return 2 ** ((unpack('C', $packed)[1] & 0b00000111) + 1);
    }

    /**
     * @return string
     */
    public function getColorTable()
    {
        if (isset($this->localColorTables[$this->fp])) {
            return $this->localColorTables[$this->fp];
        }

        return $this->globalColorTable;
    }

    /**
     * @return string
     */
    public function getImageData()
    {
        return $this->imageDatas[$this->fp];
    }

    /**
     * @return null|int
     */
    public function getLinkedKey()
    {
        return $this->linkedKeys[$this->fp] ?? null;
    }

    /**
     * @return null|int
     */
    public function getLinkedDisposalMethod()
    {
        return $this->linkedDisposalMethods[$this->fp]
            ?? null;
    }

    /**
     * @return bool
     */
    public function isAnimated()
    {
        return 1 < \count($this);
    }

    /**
     * @param bool $valid
     */
    public function setValid($valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function last()
    {
        return (\count($this) - 1) === $this->key();
    }

    /**
     * @param int $position
     */
    public function seek($position): void
    {
        $this->fp = $position;
    }

    /**
     * @return $this
     */
    public function current()
    {
        return $this;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->fp;
    }

    public function next(): void
    {
        ++$this->fp;
    }

    public function rewind(): void
    {
        $this->fp = 0;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return \count($this->imageDatas) > $this->fp;
    }

    /**
     * @return int
     */
    public function count()
    {
        return \count($this->imageDatas);
    }
}
