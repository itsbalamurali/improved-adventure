<?php



namespace Imagecraft\LayerPass;

use Imagecraft\Layer\RegularLayerInterface;

/**
 * @covers \Imagecraft\LayerPass\RegularLayerPass
 *
 * @internal
 */
final class RegularLayerPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected $layer;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\LayerPass\\RegularLayerPass', null);
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\ImageLayer', null);
    }

    public function testProcessMove(): void
    {
        $this->pass->processMove($this->layer);
        self::assertTrue($this->layer->has('regular.move.x'));
        self::assertTrue($this->layer->has('regular.move.y'));
        self::assertTrue($this->layer->has('regular.move.gravity'));

        $this->layer->clear();
        $this->layer->add([
            'regular.move.x' => 11.1,
            'regular.move.y' => -10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,
        ]);
        $this->pass->processMove($this->layer);
        self::assertSame(11, $this->layer->get('regular.move.x'));
        self::assertSame(-10, $this->layer->get('regular.move.y'));
        self::assertSame(RegularLayerInterface::MOVE_TOP_LEFT, $this->layer->get('regular.move.gravity'));
    }
}
