<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Core\CoreExtension
 *
 * @internal
 */
final class CoreExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testBoot(): void
    {
        $dispatcher = $this->createMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects(self::atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\CoreExtension', null);
        $extension->boot($dispatcher);
    }
}
