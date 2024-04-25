<?php



namespace Imagecraft\Layer;

use Imagecraft\ImageBuilderInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
interface LayerInterface extends ParameterBagInterface
{
    /**
     * @return null|ImageBuilderInterface
     *
     * @api
     */
    public function done();
}
