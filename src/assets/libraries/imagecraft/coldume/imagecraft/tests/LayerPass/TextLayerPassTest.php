<?php



namespace Imagecraft\LayerPass;

use Imagecraft\Exception\BadMethodCallException;

/**
 * @covers \Imagecraft\LayerPass\TextLayerPass
 *
 * @internal
 */
final class TextLayerPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected $layer;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\LayerPass\\TextLayerPass', null);
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\TextLayer', null);
    }

    public function testProcessFont(): void
    {
        $this->layer->add([
            'text.font.filename' => 'foo',
            'text.font.size' => 15,
            'text.font.hex_color' => '#000',
        ]);
        $this->pass->processFont($this->layer);
        self::assertSame('foo', $this->layer->get('text.font.filename'));
        self::assertSame(15, $this->layer->get('text.font.size'));
        self::assertSame('#000', $this->layer->get('text.font.hex_color'));
        self::assertSame([0, 0, 0], $this->layer->get('text.font.rgb_color'));
    }

    public function testProcessFontWhenNoFontIsAdded(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->pass->processFont($this->layer);
    }

    public function testProcessLabel(): void
    {
        $this->pass->processLabel($this->layer);
        self::assertSame('', $this->layer->get('text.label'));

        $this->layer->clear();
        $this->layer->set('text.label', 'foo');
        $this->pass->processLabel($this->layer);
        self::assertSame('foo', $this->layer->get('text.label'));
    }

    public function testProcessAngle(): void
    {
        $this->pass->processAngle($this->layer);
        self::assertSame(0, $this->layer->get('text.angle'));

        $this->layer->clear();
        $this->layer->set('text.angle', 10.1);
        $this->pass->processAngle($this->layer);
        self::assertSame(10, $this->layer->get('text.angle'));
    }

    public function testProcessLineSpacing(): void
    {
        $this->pass->processLineSpacing($this->layer);
        self::assertSame(0.5, $this->layer->get('text.line_spacing'));

        $this->layer->clear();
        $this->layer->set('text.line_spacing', 10.1);
        $this->pass->processLineSpacing($this->layer);
        self::assertSame(10.1, $this->layer->get('text.line_spacing'));
    }

    public function testProcessBox(): void
    {
        $this->pass->processBox($this->layer);
        self::assertSame([0, 0, 0, 0], $this->layer->get('text.box.paddings'));
        self::assertNull($this->layer->get('text.box.hex_color'));

        $this->layer->clear();
        $this->layer->add([
            'text.box.paddings' => [10, 1],
            'text.box.hex_color' => '#000',
        ]);
        $this->pass->processBox($this->layer);
        self::assertSame([10, 1, 0, 0], $this->layer->get('text.box.paddings'));
        self::assertSame('#000', $this->layer->get('text.box.hex_color'));
        self::assertSame([0, 0, 0], $this->layer->get('text.box.rgb_color'));
    }
}
