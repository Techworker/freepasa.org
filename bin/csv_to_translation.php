<?php

$language = trim($_SERVER['argv'][1]);

$translation = [];

$first = true;
if (($handle = fopen($language . '.csv', 'r')) !== false) {
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        if($first) {
            $first = false;
            continue;
        }

        if(!isset($translation[$data[0]])) {
            $translation[$data[0]] = [];
        }
        $translation[$data[0]][$data[1]] = $data[2];

    }
    fclose($handle);
}

foreach($translation as $area => $translations) {
    $exp = var_export($translations, true);
    file_put_contents(__DIR__ . '/../lang/' . $language . '/' . $area . '.php', '<?php return ' . $exp . ';');
}
