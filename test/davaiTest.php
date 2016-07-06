<?php
require 'davai.php';

class A
{
    static function b()
    {
        $GLOBALS['SUCCESS'] = true;
    }
}

function a()
{
    $GLOBALS['SUCCESS'] = true;
}

class DavaiTest extends \PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->Davai = new Davai();
        $this->Davai->method = 'GET';
    }

    function testRoute()
    {
        $this->Davai->url    = '/test/user';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testRouteWithClass()
    {
        $this->Davai->url    = '/test/user';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user', 'A#b');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testRouteWithAnonymouseFunction()
    {
        $this->Davai->url    = '/test/user';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user', function()
        {
            $GLOBALS['SUCCESS'] = true;
        });

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testCaptureRoute()
    {
        $this->Davai->url    = '/test/user/123';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[i:userId]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testFalseCaptureRoute()
    {
        $this->Davai->url    = '/test/user/abc';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[i:userId]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testDoubleCaptureRoute()
    {
        $this->Davai->url    = '/test/user/123/456';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[i:userId]/[i:postId]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testDoubleFalseCaptureRoute()
    {
        $this->Davai->url    = '/test/user/abc/abc';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[i:userId]/[i:postId]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testLazyCaptureRoute()
    {
        $this->Davai->url    = '/test/user';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[a:username?]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testDoubleLazyCaptureRoute()
    {
        $this->Davai->url    = '/test/user/abc';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user/[a:username?]/[a:postName?]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testRecord()
    {
        $this->Davai->record(['foo' => 'foo',
                              'bar' => 'bar']);
    }

    function testRecordPath()
    {
        $this->Davai->record(['foo' => 'foo',
                              'bar' => 'bar']);

        $this->Davai->recordGet('foo', '/test/user');
    }

    function testAddRule()
    {
        $this->Davai->addRule('s', '[0-9a-z]++');
    }

    function testReverseRouting()
    {
        $this->Davai->get('/test/user/[a:username?]/[a:postName?]', 'a', 'test');

        $url = $this->Davai->generate('test', ['username' => 'test',
                                               'postName' => 'test']);

        $this->assertEquals('test/user/test/test/', $url);
    }

    function testSetBasePath()
    {
        $this->Davai->setBasePath('/public');
        $this->Davai->url    = '/public/test/user';
        $GLOBALS['SUCCESS']  = false;

        $this->Davai->get('/test/user', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }
}
?>