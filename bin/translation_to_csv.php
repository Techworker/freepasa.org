<?php

$language = trim($_SERVER['argv'][1]);

$translationBase = include(__DIR__ . '/../lang/en.php');
$targetFile = __DIR__ . '/../lang/' . $language . '.php';

$translationTarget = [];
if(file_exists($targetFile)) {
    $translationTarget = include($targetFile);
}

$csv = [];
foreach($translationBase as $area => $translations) {
    if(!isset($translationTarget[$area])) {
        $translationTarget[$area] = [];
    }

    foreach($translations as $key => $translation) {
        $csv[] = [
            'area' => $area,
            'key' => $key,
            'original' => $translation,
            'translation' => ''
        ];

        if(isset($translationTarget[$area][$key])) {
            $csv[count($csv) - 1]['translation'] = $translationTarget[$area][$key];
        }
    }
}

$fp = fopen($language . '.csv', 'w');

fputcsv($fp, ["Area", "Key", "Original", "Translation"]);
foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

print_r($csv);
