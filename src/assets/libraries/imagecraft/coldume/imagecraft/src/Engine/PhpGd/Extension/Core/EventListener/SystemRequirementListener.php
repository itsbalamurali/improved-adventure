<?php



namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Exception\InvalidArgumentException;
use Imagecraft\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class SystemRequirementListener implements EventSubscriberInterface
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
            PhpGdEvents::PRE_IMAGE => [
                ['verifyEngine', 999],
                ['verifySavedFormat', 989],
            ],
        ];
    }

    /**
     * @throws RuntimeException
     */
    public function verifyEngine(): void
    {
        if (!$this->context->isEngineSupported()) {
            throw new RuntimeException('gd.extension.not.enabled');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function verifySavedFormat(PhpGdEvent $event): void
    {
        $format = $event->getOptions()['output_format'];
        if ('default' !== $format && !$this->context->isImageFormatSupported($format)) {
            throw new InvalidArgumentException(
                'output.image.format.not.supported.%cp_unsupported%',
                ['%cp_unsupported%' => '"'.$format.'"']
            );
        }
    }
}
