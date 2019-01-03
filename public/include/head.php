<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Benjamin Ansbach">
    <meta name="description" content="PascalCoin Account distribution service. Get a free PASA and get started with PascalCoin.">
    <title>PascalCoin Account Distribution service</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <link rel="stylesheet" href="<?=DOMAIN?>/assets/skeleton.css">
    <link rel="stylesheet" href="<?=DOMAIN?>/assets/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>

<body>
<div style="background-color: #2f3542">
    <div class="container">
        <div class="head">
            <a href="<?=DOMAIN?>"><img src="<?=DOMAIN?>/assets/pascalcoin.png" /></a>
            <div class="title">
                freepasa.org
                <small><small>
                        Block: <?=$nodeStatus['blocks']?>
                        <?php if(DEBUG): ?><?=$nodeStatus['version']?><?php endif; ?>
                    </small></small>
            </div>
        </div>
    </div>
</div>
