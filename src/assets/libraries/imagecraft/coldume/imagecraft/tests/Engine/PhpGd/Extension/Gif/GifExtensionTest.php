<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Gif\GifExtension
 *
 * @internal
 */
final class GifExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testBoot(): void
    {
        $dispatcher = $this->createMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects(self::atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtension', null);
        $extension->boot($dispatcher);
    }
}
