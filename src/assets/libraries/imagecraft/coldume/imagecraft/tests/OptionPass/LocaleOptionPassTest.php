<?php



namespace Imagecraft\OptionPass;

/**
 * @covers \Imagecraft\OptionPass\LocaleOptionPass
 *
 * @internal
 */
final class LocaleOptionPassTest extends \PHPUnit\Framework\TestCase
{
    protected $pass;

    protected function setUp(): void
    {
        $this->pass = $this->createPartialMock('Imagecraft\\OptionPass\\LocaleOptionPass', null);
    }

    public function testProcess(): void
    {
        $option = $this->pass->process([]);
        self::assertSame('en', $option['locale']);

        $option = $this->pass->process(['locale' => 'foo']);
        self::assertSame('en', $option['locale']);
    }
}
