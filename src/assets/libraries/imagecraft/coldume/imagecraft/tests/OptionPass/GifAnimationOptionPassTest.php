<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\GifAnimationOptionPass
 *
 * @internal
 */
final class GifAnimationOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\GifAnimationOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertTrue($option['gif_animation']);
    }
}
