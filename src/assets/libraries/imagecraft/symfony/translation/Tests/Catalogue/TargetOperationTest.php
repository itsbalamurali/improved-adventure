<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Catalogue;

use Symfony\Component\Translation\Catalogue\TargetOperation;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class TargetOperationTest extends AbstractOperationTest
{
    public function testGetMessagesFromSingleDomain(): void
    {
        $operation = $this->createOperation(
            new MessageCatalogue('en', ['messages' => ['a' => 'old_a', 'b' => 'old_b']]),
            new MessageCatalogue('en', ['messages' => ['a' => 'new_a', 'c' => 'new_c']])
        );

        self::assertSame(
            ['a' => 'old_a', 'c' => 'new_c'],
            $operation->getMessages('messages')
        );

        self::assertSame(
            ['c' => 'new_c'],
            $operation->getNewMessages('messages')
        );

        self::assertSame(
            ['b' => 'old_b'],
            $operation->getObsoleteMessages('messages')
        );
    }

    public function testGetResultFromSingleDomain(): void
    {
        self::assertSame(
            new MessageCatalogue('en', [
                'messages' => ['a' => 'old_a', 'c' => 'new_c'],
            ]),
            $this->createOperation(
                new MessageCatalogue('en', ['messages' => ['a' => 'old_a', 'b' => 'old_b']]),
                new MessageCatalogue('en', ['messages' => ['a' => 'new_a', 'c' => 'new_c']])
            )->getResult()
        );
    }

    public function testGetResultWithMetadata(): void
    {
        $leftCatalogue = new MessageCatalogue('en', ['messages' => ['a' => 'old_a', 'b' => 'old_b']]);
        $leftCatalogue->setMetadata('a', 'foo', 'messages');
        $leftCatalogue->setMetadata('b', 'bar', 'messages');
        $rightCatalogue = new MessageCatalogue('en', ['messages' => ['b' => 'new_b', 'c' => 'new_c']]);
        $rightCatalogue->setMetadata('b', 'baz', 'messages');
        $rightCatalogue->setMetadata('c', 'qux', 'messages');

        $diffCatalogue = new MessageCatalogue('en', ['messages' => ['b' => 'old_b', 'c' => 'new_c']]);
        $diffCatalogue->setMetadata('b', 'bar', 'messages');
        $diffCatalogue->setMetadata('c', 'qux', 'messages');

        self::assertSame(
            $diffCatalogue,
            $this->createOperation(
                $leftCatalogue,
                $rightCatalogue
            )->getResult()
        );
    }

    protected function createOperation(MessageCatalogueInterface $source, MessageCatalogueInterface $target)
    {
        return new TargetOperation($source, $target);
    }
}
