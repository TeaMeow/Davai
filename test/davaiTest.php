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
    function testRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testRouteWithClass()
    {
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', 'A#b');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testRouteWithAnonymouseFunction()
    {
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', function()
        {
            $GLOBALS['SUCCESS'] = true;
        });

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/123';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testFalseCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testDoubleCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/123/456';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]/[i:userId]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testDoubleFalseCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]/[i:postId]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testLazyCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[a:username?]', 'a');

        $this->assertTrue($GLOBALS['SUCCESS']);
    }

    function testFalseLazyCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[a:username?]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testDoubleLazyCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[a:username?]/[a:postName?]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }

    function testDoubleFalseLazyCaptureRoute()
    {
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Davai                     = new Davai();
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[a:username?]/[a:postName?]', 'a');

        $this->assertFalse($GLOBALS['SUCCESS']);
    }
}
?>