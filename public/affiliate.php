<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline">Affiliate &amp; Integration</div>
        </div>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p><?=DOMAIN?> runs an affiliate program that you can use to earn some PascalCoin by linking to this page.</p>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> The payout amount is <?=(AFFILIATE_AMOUNT/10000)?> PASC for each successfully distributed pasa right now. This value might vary in the future without any further notice.</p>
        <p class="error-info"><i class="fas fa-exclamation-circle"></i> The affiliate program is active as long as there are funds available to run it. It can be closed down without any further notice.</p>
        <p>
            The minimum required parameter to earn money is the <code style="display: inline-block">afac</code> parameter. This the account number
            where the reward will be sent to, as soon as a PASA was successfully distributed.
        </p>
        <p>The following table describes all available parameters that can be used for a request.</p>
        <table>
            <thead>
            <tr>
                <th>Parameter</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%"><code>afac</code></td>
                    <td>
                        The account number where a certain amount of pasc will be
                        sent to if the account transfer was successful. Read it as
                        affiliate account number. <strong>Please omit the checksum</strong>.</td>
                </tr>
                <tr>
                    <td><code>origin</code></td>
                    <td>
                        This value is for internal use to create statistics. You can
                        use whatever value you want, but please stay with the same
                        origin value in each request.
                    </td>
                </tr>
            </tbody>
        </table>
        <h3>Link Builder</h3>
        <div id="link-builder">
            <div class="row">
                <div class="six columns">
                    <div class="row">
                        <div class="twelve columns">
                            <label for="origin">Affiliate account number</label>
                            <input type="text" class="u-full-width" name="afac" id="afac" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="origin">Origin</label>
                            <input type="text" class="u-full-width" name="origin" id="origin" value="">
                        </div>
                    </div>
                </div>
                <div class="six columns">
                    <code id="link"><?=DOMAIN?></code>
                    <button id="copy-to-clipboard" class="button button-primary">Copy to clipboard</button>
                    <a class="button button-primary" href="<?=DOMAIN?>" target="_blank" id="try-it">Try it</a>
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
