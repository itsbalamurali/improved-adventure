<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class BackgroundLayerListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE => ['initFinalFormat', 859],
        ];
    }

    /**
     * @param PhpGdEvent
     */
    public function initFinalFormat(PhpGdEvent $event): void
    {
        $layer = $event->getLayers()[0];
        $options = $event->getOptions();
        if ('default' === $options['output_format']) {
            $format = $layer->get('image.format');
        } else {
            $format = $options['output_format'];
        }

        $layer->set('final.format', $format);
    }
}
