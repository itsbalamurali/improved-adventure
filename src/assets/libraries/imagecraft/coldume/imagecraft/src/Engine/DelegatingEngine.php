<?php



namespace Imagecraft\Engine;

use Imagecraft\Engine\PhpGd\PhpGdEngine;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class DelegatingEngine implements EngineInterface
{
    public function getImage(array $layers, array $options)
    {
        $engines = $this->getRegisteredEngines();
        if (!\array_key_exists($options['engine'], $engines)) {
            $engine = $engines['php_gd'];
        } else {
            $engine = $engines[$options['engine']];
        }

        return $engine->getImage($layers, $options);
    }

    public function getContext(array $options)
    {
        $engines = $this->getRegisteredEngines();
        if (!\array_key_exists($options['engine'], $engines)) {
            $engine = $engines['php_gd'];
        } else {
            $engine = $engines[$options['engine']];
        }

        return $engine->getContext($options);
    }

    /**
     * @return string[]
     */
    protected function getRegisteredEngines()
    {
        return [
            'php_gd' => new PhpGdEngine(),
        ];
    }
}
