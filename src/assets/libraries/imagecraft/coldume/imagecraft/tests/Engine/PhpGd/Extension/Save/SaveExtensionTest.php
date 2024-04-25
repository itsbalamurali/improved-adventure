<?php



namespace Imagecraft\Engine\PhpGd\Extension\Save;

/**
 * @covers \Imagecraft\Engine\PhpGd\Extension\Save\SaveExtension
 *
 * @internal
 */
final class SaveExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testBoot(): void
    {
        $dispatcher = $this->createMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects(self::atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->createPartialMock('Imagecraft\\Engine\\PhpGd\\Extension\\Save\\SaveExtension', null);
        $extension->boot($dispatcher);
    }
}
