<?php



namespace Imagecraft\Engine\DelegatingEngine;

use Imagecraft\Engine\Fixtures\FooEngine;

/**
 * @covers \Imagecraft\Engine\DelegatingEngine
 *
 * @internal
 */
final class DelegatingEngineTest extends \PHPUnit\Framework\TestCase
{
    protected $engine;

    protected function setUp(): void
    {
        $this->engine = $this->createPartialMock('Imagecraft\\Engine\\DelegatingEngine', ['getRegisteredEngines']);
        $this->engine
            ->method('getRegisteredEngines')
            ->willReturn(['foo' => new FooEngine()])
        ;
    }

    public function testGetImage(): void
    {
        self::assertSame('foo', $this->engine->getImage([], ['engine' => 'foo']));
    }

    public function testGetContext(): void
    {
        self::assertSame('bar', $this->engine->getContext(['engine' => 'foo']));
    }
}
