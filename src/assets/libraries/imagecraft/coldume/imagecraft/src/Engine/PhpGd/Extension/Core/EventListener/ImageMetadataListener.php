<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class ImageMetadataListener implements EventSubscriberInterface
{
    /**
     * @var PhpGdContext
     */
    protected $context;

    /**
     * @var PhpGdContext
     */
    public function __construct(PhpGdContext $context)
    {
        $this->context = $context;
    }

    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::FINISH_IMAGE => ['addImageMetadatas', 899],
        ];
    }

    public function addImageMetadatas(PhpGdEvent $event): void
    {
        $image = $event->getImage();
        $layers = $event->getLayers();
        $format = $layers[0]->get('final.format');

        $image->setMime($this->context->getImageMime($format));
        $image->setExtension($this->context->getImageExtension($format));
        $image->setWidth($layers[0]->get('final.width'));
        $image->setHeight($layers[0]->get('final.height'));
        $image->addExtras([
            'original_width' => $layers[0]->get('image.width'),
            'original_height' => $layers[0]->get('image.height'),
        ]);
    }
}
