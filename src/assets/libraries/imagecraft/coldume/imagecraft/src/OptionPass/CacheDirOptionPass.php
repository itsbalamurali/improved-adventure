<?php



namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class CacheDirOptionPass implements OptionPassInterface
{
    public function process(array $options)
    {
        if (!isset($options['cache_dir'])) {
            $options['cache_dir'] = null;
        }

        return $options;
    }
}
