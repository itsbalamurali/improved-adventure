<?php



namespace ImcStream;

use ImcStream\Exception\IOException;

/**
 * @covers \ImcStream\ImcStream
 *
 * @internal
 */
final class ImcStreamTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $existed = \in_array('imc', stream_get_wrappers(), true);
        if (!$existed) {
            stream_wrapper_register('imc', 'ImcStream\\ImcStream');
        }
    }

    public function testRegister(): void
    {
        $existed = \in_array('imc', stream_get_wrappers(), true);
        if ($existed) {
            stream_wrapper_unregister('imc');
        }
        ImcStream::register();
        self::assertTrue(\in_array('imc', stream_get_wrappers(), true));
    }

    public function testOpenInvalidLocalStream(): void
    {
        $this->expectException(IOException::class);

        $arr = ['uri' => 'foo'];
        $str = serialize($arr);
        $fp = @fopen('imc://'.$str, 'r');
    }

    public function testOpenInvalidNetworkStream(): void
    {
        $this->expectException(IOException::class);

        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }

        $arr = ['uri' => 'http://www.example.com/foo'];
        $str = serialize($arr);
        $fp = @fopen('imc://'.$str, 'r');
    }

    public function testSeekSeekableStream(): void
    {
        $arr = ['uri' => __DIR__.'/Fixtures/hello.txt', 'seek' => true];
        $str = serialize($arr);
        $fp = @fopen('imc://'.$str, 'r');

        fseek($fp, 6);
        $data = fread($fp, 5);
        self::assertSame('world', $data);

        fseek($fp, 0);
        $data = fread($fp, 7);
        self::assertSame('hello w', $data);

        fseek($fp, 0);
        $data = '';
        if (!feof($fp)) {
            $data .= fread($fp, 1_024);
        }
        self::assertSame(1, preg_match('/\\Ahello world\\Z/', $data));

        fseek($fp, 1_000);
        $data = fread($fp, 10);
        self::assertEmpty($data);
        self::assertTrue(feof($fp));
        @fclose($fp);
    }

    public function testSeekUnseekableStream(): void
    {
        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }
        @fclose($r);
        $arr = ['uri' => 'http://www.example.com', 'seek' => true];
        $str = serialize($arr);
        $fp = @fopen('imc://'.$str, 'r');
        $data1 = fread($fp, 1_024);
        $data2 = fread($fp, 100);
        $data3 = fread($fp, 500);
        rewind($fp);
        $data4 = fread($fp, 100);
        $data5 = fread($fp, 500);
        $data6 = fread($fp, 1_024);
        self::assertSame($data1.$data2.$data3, $data4.$data5.$data6);

        fseek($fp, 100_000);
        $data = fread($fp, 10);
        self::assertEmpty($data);
        self::assertTrue(feof($fp));
        @fclose($fp);
    }

    /**
     * @depends testSeekUnseekableStream
     */
    public function testGlobal(): void
    {
        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }
        $arr = [
            'uri' => 'http://www.example.com',
            'seek' => true,
            'global' => true,
        ];
        $str = serialize($arr);
        $fp = fopen('imc://'.$str, 'r');
        $str1 = fread($fp, 1_024);
        fclose($fp);

        $fp = fopen('imc://'.$str, 'r');
        $str2 = fread($fp, 1_024);
        fclose($fp);

        self::assertSame($str1, $str2);
        ImcStream::fclose('imc://'.$str);

        $fp = fopen('imc://'.$str, 'r');
        $str2 = fread($fp, 1_024);
        fclose($fp);
        ImcStream::fclose();
    }

    public function testDataLimit(): void
    {
        $this->expectException(IOException::class);

        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }
        $arr = [
            'uri' => 'http://www.example.com',
            'data_limit' => 1,
        ];
        $str = serialize($arr);
        $fp = fopen('imc://'.$str, 'r');
        fread($fp, 1_024);
        fread($fp, 1_024);
    }

    public function testTimeout(): void
    {
        $this->expectException(IOException::class);

        if (!\ini_get('allow_url_fopen') || !$r = @fsockopen('8.8.8.8', 53, $e, $r, 1)) {
            self::markTestSkipped('No internet connection or allow_url_fopen is not enabled.');
        }
        $arr = [
            'uri' => 'http://www.example.com',
            'timeout' => 0.000_001,
        ];
        $str = serialize($arr);
        $fp = fopen('imc://'.$str, 'r');
        fread($fp, 1_024);
        fread($fp, 1_024);
    }
}
