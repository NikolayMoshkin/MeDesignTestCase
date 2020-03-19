<?php

use Classes\SentencesMaker;
use Classes\DB;

require_once (__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/functions.php');


$string = '{Пожалуйста,|Просто|Если сможете,} сделайте так, чтобы это {удивительное|крутое|
простое|важное|бесполезное} тестовое предложение {изменялось {быстро|мгновенно|
оперативно|правильно} случайным образом|менялось каждый раз}.';

$sentences = SentencesMaker::init($string);

DB::init('localhost', 'root', '');

echo DB::insertSentences($sentences);

echo 'Success';










