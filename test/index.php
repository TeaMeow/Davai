<?php
function e($shit)
{
    exit(var_dump($shit));
}
include '../src/davai.php';

$davai = new Davai();

$davai->map('GET', '/test/{i:test}', function($test)
{
    echo "adsasasd";
})
?>