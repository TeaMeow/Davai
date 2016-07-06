<?php
require 'davai.php';

class DavaiTest extends \PHPUnit_Framework_TestCase
{
    function testRoute()
    {
        $Davai      = new Davai();
        $Davai->url = 'http://www.example.com/test/user';
        $isSuccess  = false;

        function a()
        {
            $isSuccess = true;
        }

        $Davai->get('/test/user/[i:userId]/[i:page?]', 'a');

        $this->assertEquals(true, $isSuccess);
    }
}
?>