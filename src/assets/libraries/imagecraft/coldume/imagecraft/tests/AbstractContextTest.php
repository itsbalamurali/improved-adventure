<?php



namespace Imagecraft;

/**
 * @covers \Imagecraft\AbstractContext
 *
 * @internal
 */
final class AbstractContextTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    protected function setUp(): void
    {
        $this->context = $this->getMockForAbstractClass('Imagecraft\\AbstractContext');
    }

    public function testGetMemoryLimit(): void
    {
        $previous = \ini_get('memory_limit');

        ini_set('memory_limit', '40M');
        self::assertSame(40 * 1_024 * 1_024, $this->context->getMemoryLimit());

        ini_set('memory_limit', '-1');
        self::assertSame(1_024 * 1_024 * 1_024, $this->context->getMemoryLimit());

        ini_set('memory_limit', '40M');
        self::assertSame(35 * 1_024 * 1_024, $this->context->getMemoryLimit(-5));

        ini_set('memory_limit', '40M');
        self::assertSame(35 * 1_024 * 1_024, $this->context->getMemoryLimit(35));

        ini_set('memory_limit', $previous);
    }

    /**
     * @requires extension fileinfo
     */
    public function testIsFileinfoExtensionEnabled(): void
    {
        self::assertTrue($this->context->isFileinfoExtensionEnabled());
    }
}
