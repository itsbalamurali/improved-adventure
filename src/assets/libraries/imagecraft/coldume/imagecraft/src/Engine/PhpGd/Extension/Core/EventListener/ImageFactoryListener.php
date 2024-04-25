<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\Extension\Core\ImageFactory;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class ImageFactoryListener implements EventSubscriberInterface
{
    /**
     * @var ImageFactory
     */
    protected $factory;

    public function __construct(ImageFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::IMAGE => ['createImage', 99],
        ];
    }

    public function createImage(PhpGdEvent $event): void
    {
        $image = $this->factory->createImage($event->getLayers(), $event->getOptions());
        $event->setImage($image);
        $event->stopPropagation();
    }
}
