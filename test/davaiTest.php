<?php
require 'davai.php';

class DavaiTest extends \PHPUnit_Framework_TestCase
{
    function testRoute()
    {
        $davai     = new Davai();
        $dava->url = 'http://www.example.com/test/user';
        $isSuccess = false;

        function a()
        {
            $isSuccess = true;
        }

        $davai->get('/test/user/[i:userId]/[i:page?]', 'a');

        $this->assertEquals(true, $isSuccess);
    }
}
?>