<?php


    use ImcStream\ImcStream;
    use TranslatedException\TranslatedException;

    $loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('Imagecraft\\', __DIR__);

TranslatedException::init();
ImcStream::register();

date_default_timezone_set('UTC');
