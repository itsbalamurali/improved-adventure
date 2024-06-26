<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Exception\RuntimeException;
use Imagecraft\Layer\TextLayerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class TextLayerListener implements EventSubscriberInterface
{
    /**
     * @var PhpGdContext
     */
    protected $context;

    public function __construct(PhpGdContext $context)
    {
        $this->context = $context;
    }

    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE => ['verifyFreeType', 849],
        ];
    }

    /**
     * @param  PhpGdEvent
     *
     * @throws RuntimeException
     */
    public function verifyFreeType(PhpGdEvent $event): void
    {
        $layers = $event->getLayers();
        foreach ($layers as $key => $layer) {
            if (!$layer instanceof TextLayerInterface) {
                continue;
            }
            if (!$this->context->isFreeTypeSupported()) {
                throw new RuntimeException('adding.text.not.supported');
            }

            break;
        }
    }
}
