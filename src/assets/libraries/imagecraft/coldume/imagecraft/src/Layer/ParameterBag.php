<?php



namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
class ParameterBag implements ParameterBagInterface
{
    protected $parameters = [];

    public function set($name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function add(array $parameters): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    public function get($name)
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }

    public function has($name)
    {
        return \array_key_exists($name, $this->parameters);
    }

    public function remove($name): void
    {
        if ($this->has($name)) {
            unset($this->parameters[$name]);
        }
    }

    public function clear(): void
    {
        $this->parameters = [];
    }
}
