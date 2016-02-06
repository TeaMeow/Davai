<?php
function e($shit)
{
    exit(var_dump($shit));
}
include '../src/davai.php';

$davai = new Davai();

class A
{
    function B()
    {
        echo 'asdasd';
    }
}


$davai->get('/test/user/[i:userId]/[i:page?]', 'A#B', 'A');

echo $davai->generate('A', ['userId' => 3,
                           'page'   => 123123]);
?>