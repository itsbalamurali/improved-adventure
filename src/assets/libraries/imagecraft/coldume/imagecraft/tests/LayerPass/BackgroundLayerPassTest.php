<?php



namespace Imagecraft\LayerPass;

use Imagecraft\Exception\BadMethodCallException;

/**
 * @covers \Imagecraft\LayerPass\BackgroundLayerPass
 *
 * @internal
 */
final class BackgroundLayerPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected $layer;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\LayerPass\\BackgroundLayerPass', null);
        $this->layer = $this->createPartialMock('Imagecraft\\Layer\\BackgroundLayer', null);
    }

    public function testProcessWhenNoBackgroundLayerIsFound(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->pass->process([]);
    }
}
