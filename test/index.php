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


$davai->get('POST', '/test/user/[i:userId]/[i:page?]', 'A#B', 'A');

echo $davai->reverse('A', ['userId' => 3,
                           'page'   => 123123]);
?>