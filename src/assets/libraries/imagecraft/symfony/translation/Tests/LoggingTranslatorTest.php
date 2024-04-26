<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\Translator;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoggingTranslatorTest extends TestCase
{
    public function testTransWithNoTranslationIsLogged(): void
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $logger->expects(self::exactly(2))
            ->method('warning')
            ->with('Translation not found.')
        ;

        $translator = new Translator('ar');
        $loggableTranslator = new LoggingTranslator($translator, $logger);
        $loggableTranslator->transChoice('some_message2', 10, ['%count%' => 10]);
        $loggableTranslator->trans('bar');
    }

    public function testTransChoiceFallbackIsLogged(): void
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $logger->expects(self::once())
            ->method('debug')
            ->with('Translation use fallback catalogue.')
        ;

        $translator = new Translator('ar');
        $translator->setFallbackLocales(['en']);
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['some_message2' => 'one thing|%count% things'], 'en');
        $loggableTranslator = new LoggingTranslator($translator, $logger);
        $loggableTranslator->transChoice('some_message2', 10, ['%count%' => 10]);
    }
}
