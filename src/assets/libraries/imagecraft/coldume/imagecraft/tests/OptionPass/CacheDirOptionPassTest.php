<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\CacheDirOptionPass
 *
 * @internal
 */
final class CacheDirOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\CacheDirOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertNull($option['cache_dir']);
    }
}
