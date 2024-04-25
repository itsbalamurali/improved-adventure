<?php



namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

use Imagecraft\Engine\PhpGd\Extension\Gif\GifExtractor;
use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Exception\TranslatedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class GifExtractorListener implements EventSubscriberInterface
{
    /**
     * @var GifExtractor
     */
    protected $extractor;

    /**
     * @var mixed[]
     */
    protected $extras = [];

    public function __construct(GifExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @return inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE => ['initExtracted', 829],
            PhpGdEvents::FINISH_IMAGE => ['addImageExtras', 879],
        ];
    }

    public function initExtracted(PhpGdEvent $event): void
    {
        $options = $event->getOptions();
        $layers = $event->getLayers();

        if (
            !$options['gif_animation']
            || PhpGdContext::FORMAT_GIF !== $layers[0]->get('final.format')
            || PhpGdContext::FORMAT_GIF !== $layers[0]->get('image.format')
        ) {
            return;
        }

        $fp = $layers[0]->get('image.fp');
        rewind($fp);
        if ('GIF89a' !== fread($fp, 6)) {
            return;
        }
        rewind($fp);
        $extracted = $this->extractor->extractFromFilePointer($fp);
        if (!$extracted->isAnimated()) {
            return;
        }
        if (!$extracted->isValid()) {
            $e = new TranslatedException('gif.animation.may.lost.due.to.corrupted.frame.data');
            $this->extras['gif_error'] = $e->getMessage();

            return;
        }

        $layers[0]->set('gif.extracted', $extracted);
    }

    public function addImageExtras(PhpGdEvent $event): void
    {
        if ($this->extras) {
            $image = $event->getImage();
            $image->addExtras($this->extras);
        }
    }
}
