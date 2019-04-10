<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Benjamin Ansbach">
    <meta name="description" content="<?=t_('global', 'page_description')?>">
    <title><?=t_('global', 'page_title')?></title>
    <link rel="shortcut icon" href="https://www.pascalcoin.org/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:700|Roboto:300,300i,400,400i" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <link rel="stylesheet" href="<?=DOMAIN?>/assets/skeleton.css?id=1">
    <link rel="stylesheet" href="<?=DOMAIN?>/assets/style.css?id=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <?php foreach($supportedLanguages['all'] as $key => $supportedLanguage) :?>
        <?php if(!isset($supportedLanguage['hidden']) || $supportedLanguage['hidden'] === false) : ?>
            <link rel="alternate" hreflang="<?= $key ?>" href="<?=str_replace('--LANG--', $key, $link) ?>" />
        <?php endif; ?>
    <?php endforeach; ?>

</head>

<body>
<div class="head-outer">
    <div class="container">
        <div class="head<?=isset($headTitle) ? ' head-large' : ''?>">
            <?php if(isset($_GET['lang'])) : ?>
                <a href="<?=DOMAIN?>?lang=<?=$_GET['lang'] ?>"><img src="<?=DOMAIN?>/assets/pascalcoin.png" /></a>
            <?php else: ?>
                <a href="<?=DOMAIN?>"><img src="<?=DOMAIN?>/assets/pascalcoin.png" /></a>
            <?php endif; ?>


            <div class="title">
                freepasa.org
                <small><small>
                        <?=t_('global', 'block')?>: <?=$nodeStatus['blocks']?>
                        <?php if(DEBUG): ?><?=$nodeStatus['version']?><?php endif; ?>
                    </small></small>
            </div>
            <?php if(isset($headTitle)) : ?>
            <div class="intro"><?=$headTitle?></div>
            <div class="intro-subtitle"><?=$headSubTitle?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="container" style="text-align: right">


    <?php foreach($supportedLanguages['all'] as $key => $supportedLanguage) {
        if(!isset($supportedLanguage['hidden']) || $supportedLanguage['hidden'] === false) {
            echo '<a href="' . str_replace('--LANG--', $key, $link) . '"><img src="' . DOMAIN . '/assets/flags/' . $supportedLanguage['iso'] . '.svg" height="20" style="margin-right: 3px;margin-left: 3px;"/></a>';
        }
    }
    ?>
</div>
