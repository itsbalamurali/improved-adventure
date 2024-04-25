<?php



namespace TranslatedException;

/**
 * @covers \TranslatedException\TranslatedException
 *
 * @internal
 */
final class TranslatedExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testTranslateMessage(): void
    {
        TranslatedException::init();
        TranslatedException::init();
        TranslatedException::addResourceDir(__DIR__.'/Fixtures/translations');

        $exception = new TranslatedException('test', 'hello.%cp_name%', ['%cp_name%' => 'foo']);
        self::assertSame('Hello foo!', $exception->getMessage());
        self::assertNotSame('Hello foo!', $exception->getVerboseMessage());

        $exception = new TranslatedException('test', 'hello.%cp_name%', ['%cp_name%' => sprintf('%0100d', 1)]);
        self::assertTrue(60 > \strlen($exception->getMessage()));
        self::assertTrue(60 < \strlen($exception->getVerboseMessage()));

        $exception = new TranslatedException('test', 'eat.apples.%cp_count%', ['%cp_count%' => 0], 0);
        self::assertSame('I eat no apple.', $exception->getMessage());
        self::assertNotSame('I eat no apple.', $exception->getVerboseMessage());

        $exception = new TranslatedException('test', 'eat.apples.%cp_count%', ['%cp_count%' => 1], 1);
        self::assertSame('I eat one apple.', $exception->getMessage());
        self::assertNotSame('I eat one apple.', $exception->getVerboseMessage());

        $exception = new TranslatedException('test', 'eat.apples.%cp_count%', ['%cp_count%' => 5], 5);
        self::assertSame('I eat 5 apples.', $exception->getMessage());
        self::assertNotSame('I eat 5 apples.', $exception->getVerboseMessage());
    }
}
