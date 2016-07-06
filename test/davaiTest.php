<?php
require 'davai.php';

class DavaiTest extends \PHPUnit_Framework_TestCase
{
    function testRoute()
    {
        $Davai      = new Davai();
        $Davai->url = '/test/user';
        $isSuccess  = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user', 'a');

        $this->assertEquals(true, $isSuccess);
    }
}
?>