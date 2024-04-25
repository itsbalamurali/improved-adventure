<?php



namespace Imagecraft\Engine\PhpGd\Extension\Save;

use Imagecraft\Engine\PhpGd\Extension\ExtensionInterface;
use Imagecraft\Engine\PhpGd\Extension\Save\EventListener\ImageFactoryListener;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class SaveExtension implements ExtensionInterface
{
    public function boot(EventDispatcherInterface $dispatcher): void
    {
        $rh = new ResourceHelper();
        $factory = new ImageFactory($rh);

        $dispatcher->addSubscriber(new ImageFactoryListener($factory));
    }
}
