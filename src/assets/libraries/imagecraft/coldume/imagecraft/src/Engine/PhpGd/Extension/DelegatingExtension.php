<?php



namespace Imagecraft\Engine\PhpGd\Extension;

use Imagecraft\Engine\PhpGd\Extension\Core\CoreExtension;
use Imagecraft\Engine\PhpGd\Extension\Gif\GifExtension;
use Imagecraft\Engine\PhpGd\Extension\Save\SaveExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class DelegatingExtension implements ExtensionInterface
{
    public function boot(EventDispatcherInterface $dispatcher): void
    {
        $extensions = $this->getRegisteredExtensions();
        foreach ($extensions as $extension) {
            $extension->boot($dispatcher);
        }
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getRegisteredExtensions()
    {
        return [
            new CoreExtension(),
            new GifExtension(),
            new SaveExtension(),
        ];
    }
}
