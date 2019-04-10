<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline"><?=t_('affiliate', 'title') ?></div>
        </div>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p><?=t_('affiliate', 'para_1', DOMAIN)?></p>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=t_('affiliate', 'payout', (AFFILIATE_AMOUNT/10000)) ?></p>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> <?=t_('affiliate', 'notice') ?></p>
        <p><?=t_('affiliate', 'min') ?></p>
        <p><?=t_('affiliate', 'follow') ?></p>
        <table>
            <thead>
            <tr>
                <th><?=t_('affiliate', 'param') ?></th>
                <th><?=t_('affiliate', 'desc') ?></th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%"><code>afac</code></td>
                    <td><?=t_('affiliate', 'afac_desc')?></td>
                </tr>
                <tr>
                    <td><code>origin</code></td>
                    <td><?=t_('affiliate', 'origin_desc')?></td>
                </tr>
            </tbody>
        </table>
        <h3><?=t_('affiliate', 'lb')?></h3>
        <div id="link-builder">
            <div class="row">
                <div class="six columns">
                    <div class="row">
                        <div class="twelve columns">
                            <label for="origin"><?=t_('affiliate', 'aan')?></label>
                            <input type="text" class="u-full-width" name="afac" id="afac" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="origin"><?=t_('affiliate', 'origin')?></label>
                            <input type="text" class="u-full-width" name="origin" id="origin" value="">
                        </div>
                    </div>
                </div>
                <div class="six columns">
                    <code id="link"><?=DOMAIN?></code>
                    <button id="copy-to-clipboard" class="button button-primary"><?=t_('affiliate', 'copy')?></button>
                    <a class="button button-primary" href="<?=DOMAIN?>?lang=<?=$_GET['lang']?>" target="_blank" id="try-it"><?=t_('affiliate', 'try')?></a>
                </div>
            </div>
        </div>

        <script>
            var $inputs = document.getElementById('link-builder').querySelectorAll('input, textarea');
            var linkBase = '<?=DOMAIN?>';
            var linkParams = {};
            for (var $input of $inputs) {
                linkParams[$input.getAttribute('id')] = '';
                $input.addEventListener('keyup', function(e) {
                    linkParams[e.currentTarget.getAttribute('id')] = e.currentTarget.value;
                    updateLink();
                });
            }

            function updateLink() {
                var query = Object.keys(linkParams)
                    .filter(function(k) {
                        return linkParams[k] !== ''
                    })
                    .map(function(k) {
                        return encodeURIComponent(k) + '=' + encodeURIComponent(linkParams[k]);
                    }).join('&');
                document.getElementById('link').innerText = linkBase + '?' + query;
                document.getElementById('try-it').setAttribute('href', linkBase + '?' + query);
            }

            var buttonText = document.getElementById('copy-to-clipboard').innerText;
            document.getElementById('copy-to-clipboard').addEventListener('click', function() {
                const el = document.createElement('textarea');
                el.value = document.getElementById('link').innerText;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                document.getElementById('copy-to-clipboard').innerText = 'Copied!';
                setTimeout(function() {
                    document.getElementById('copy-to-clipboard').innerText = buttonText;
                }, 3000);

                return false;
            });
        </script>
    </div>

<?php include __DIR__ . '/include/foot.php'?>
