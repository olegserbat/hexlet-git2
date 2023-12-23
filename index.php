<?php
require_once __DIR__ . '/vendor/autoload.php';

// Файл не включается напрямую
// Он загрузится автоматически благодаря автозагрузке
use HexletGit\Runner;

// print_r(Runner\run());
$a=new Oleg\HexletGit\A();
print_r($a->sum(2,5));