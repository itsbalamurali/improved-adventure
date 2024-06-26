<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Exception\RuntimeException;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class MemoryRequirementListener implements EventSubscriberInterface
{
    /**
     * @var PhpGdContext
     */
    protected $context;

    /**
     * @var mixed[]
     */
    protected $extras = [];

    public function __construct(PhpGdContext $context)
    {
        $this->context = $context;
    }

    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE => ['verifyMemoryLimit', 9_869],
            PhpGdEvents::FINISH_IMAGE => ['addImageExtras', 889],
        ];
    }

    /**
     * @throws RuntimeException
     */
    public function verifyMemoryLimit(PhpGdEvent $event): void
    {
        $options = $event->getOptions();
        $layers = $event->getLayers();
        $limit = $this->context->getMemoryLimit($options['memory_limit']);
        $fixed = memory_get_usage(true);
        $peak = 0;
        foreach ($layers as $key => $layer) {
            if (!$layer instanceof ImageAwareLayerInterface) {
                continue;
            }
            $width = $layer->get('image.width');
            $height = $layer->get('image.height');
            $finalWidth = $layer->get('final.width');
            $finalHeight = $layer->get('final.height');
            $constant = $this->getMemoryConstant($layer->get('image.format'));
            $dynamic = $width * $height * $constant;
            if (0 === $key) {
                $fixed += $finalWidth * $finalHeight * $constant;
                if ($limit < $fixed + $dynamic) {
                    if ($finalWidth * $finalHeight > $width * $height) {
                        throw new RuntimeException(
                            'output.image.dimensions.exceed.limit.%cp_dimensions%',
                            ['%cp_dimensions%' => $finalWidth.'x'.$finalHeight]
                        );
                    }

                    throw new RuntimeException(
                        'image.dimensions.exceed.limit.%cp_dimensions%',
                        ['%cp_dimensions%' => $width.'x'.$height]
                    );
                }
            } elseif ($limit < $fixed + $dynamic) {
                throw new RuntimeException('not.enough.memory.to.process.image');
            }
            $peak = ($dynamic > $peak) ? $dynamic : $peak;
        }
        $this->extras['memory_approx'] = number_format(($fixed + $peak) / (1_024 * 1_024), 2).' MB';
    }

    public function addImageExtras(PhpGdEvent $event): void
    {
        if ($this->extras) {
            $image = $event->getImage();
            $image->addExtras($this->extras);
        }
    }

    /**
     * @param string $format
     *
     * @return float|int
     */
    protected function getMemoryConstant($format)
    {
        switch ($format) {
            case PhpGdContext::FORMAT_JPEG:
                $constant = 6.2;

                break;

            case PhpGdContext::FORMAT_GIF:
                $constant = 3;

                break;

            case PhpGdContext::FORMAT_PNG:
                $constant = 9.5;

                break;

            case PhpGdContext::FORMAT_WEBP:
                $constant = 6;

                break;

            default:
                $constant = 4;
        }

        return $constant;
    }
}
