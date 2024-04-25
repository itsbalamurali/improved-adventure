<?php



namespace Imagecraft\Engine\PhpGd;

/**
 * Contains all events thrown in the PhpGd engine.
 *
 * @author Xianghan Wang <coldume@gmail.com>
 *
 * @since  1.0.0
 */
final class PhpGdEvents
{
    public const PRE_IMAGE = 'php_gd.pre_image';
    public const IMAGE = 'php_gd.image';
    public const FINISH_IMAGE = 'php_gd.finish_image';
}
