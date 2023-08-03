<?php

require 'vendor/autoload.php';

use HichemtabTech\LangifyLaravel\LangGeneratingType;
use HichemtabTech\LangifyLaravel\Langify;

$langify = new Langify();
$langify->setGeneratingMode(LangGeneratingType::COMPLETE_THE_MISSING);
$langify->setLanguages(['fr', 'ar']);
$langify->setDefaultLanguage('en');
$langify->setDefaultData([
    'hello' => 'Hello',
    'world' => 'World',
    'welcome' => 'Welcome',
    'general' => [
        'unknown_error' => 'Erreur inconnue',
        'errDuringRequest' => 'Une erreur est survenu lors du traitement de la demande.',
        'retry' => 'Réessayer',
        'unknown_error_require_refresh' => 'Une erreur s\'est produite, veuillez actualiser la page et réessayer.',
        'throttle-with-time' => 'Trop de tentatives. Veuillez réessayer dans :seconds secondes.',
        'throttle' => 'Trop de tentatives. Veuillez réessayer plus tard.',
    ],
    'langify' => 'Langify',
    'example' => 'Example',
    ]);

$langify->setExistedData([
    "ar" => [
        'hello' => 'kayna 1',
        'world' => 'kayna 2',
        'welcome' => 'kayna 3',
        'general' => [
            'errDuringRequest' => 'no no no',
        ]
    ]
]);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
$langify->setProgress(function ($all, $done, $progress) {
    echo "data: {$progress}%\n";
    echo "done: {$done}\n";
    echo "all: {$all}\n\n\n\n";
    ob_flush();
    flush();
});
$langify->generate();
echo "<br><br>";
echo "<pre>";
print_r($langify->getResults());