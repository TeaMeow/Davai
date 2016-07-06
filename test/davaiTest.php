<?php
require 'davai.php';

class A
{
    function b()
    {
        $GLOBALS['SUCCESS'] = true;
    }
}

class DavaiTest extends \PHPUnit_Framework_TestCase
{
    function testRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user', 'a');

        $this->assertEquals(true, $isSuccess);
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
        $isSuccess                 = false;

        $Davai->get('/test/user', function(use $isSuccess)
        {
            $isSuccess = true;
        });

        $this->assertEquals(true, $isSuccess);
    }

    function testCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/123';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertEquals(true, $isSuccess);
    }

    function testFalseCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId]', 'a');

        $this->assertEquals(false, $isSuccess);
    }

    function testDoubleCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/123/456';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId]/[i:userId]', 'a');

        $this->assertEquals(true, $isSuccess);
    }

    function testDoubleFalseCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId]/[i:postId]', 'a');

        $this->assertEquals(false, $isSuccess);
    }

    function testLazyCaptureRoute()
    {
        $Davai                     = new Davai();
        $_SERVER['REQUEST_URI']    = '/test/user/abc/abc';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isSuccess                 = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId?]', 'a');

        $this->assertEquals(true, $isSuccess);
    }
}
?>