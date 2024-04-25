<?php



namespace Imagecraft\LayerPass;

use Imagecraft\Exception\InvalidArgumentException;

/**
 * @covers \Imagecraft\LayerPass\AbstractLayerPass
 *
 * @internal
 */
final class AbstractLayerPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->getMockForAbstractClass('Imagecraft\\LayerPass\\AbstractLayerPass');
    }

    public function testSanitizeHexColor(): void
    {
        self::assertSame(['hex' => '#000', 'rgb' => [0, 0, 0]], $this->pass->sanitizeHexColor('000'));
        self::assertSame(['hex' => '#C0C0C0', 'rgb' => [192, 192, 192]], $this->pass->sanitizeHexColor('#c0c0c0'));
    }

    public function testSanitizeHexColorWhenColorIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->pass->sanitizeHexColor('foo');
    }

    public function testSanitizeEnumeration(): void
    {
        self::assertSame(1, $this->pass->sanitizeEnumeration(1, [1, 2, 3]));
    }

    public function testSanitizeEnumerationWhenEnumerationIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->pass->sanitizeEnumeration(1, [2, 3, 4]);
    }

    public function testSanitizeUrl(): void
    {
        self::assertSame('http://www.example.com', $this->pass->sanitizeUrl('www.example.com'));
    }
}
