<?php



namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class EngineOptionPass implements OptionPassInterface
{
    public function process(array $options)
    {
        if (!isset($options['engine'])) {
            $options['engine'] = 'php_gd';
        }

        return $options;
    }
}
