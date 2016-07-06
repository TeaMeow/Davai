<?php
require 'davai.php';

class A
{
    function b()
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
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', 'a');

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }

    function testRouteWithClass()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', 'b#A');

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }

    function testRouteWithAnonymouseFunction()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user', function()
        {
            $GLOBALS['SUCCESS'] = true;
        });

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }

    function testCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/123';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }

    function testFalseCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertEquals(false, $GLOBALS['SUCCESS']);
    }

    function testDoubleCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/123/456';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]/[i:userId]', 'a');

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }

    function testDoubleFalseCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId]/[i:postId]', 'a');

        $this->assertEquals(false, $GLOBALS['SUCCESS']);
    }

    function testLazyCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $GLOBALS['SUCCESS']        = false;

        $Davai->get('/test/user/[i:userId?]', 'a');

        $this->assertEquals(true, $GLOBALS['SUCCESS']);
    }
}
?>