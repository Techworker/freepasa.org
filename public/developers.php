<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline">Developer Integration</div>
        </div>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p><?=DOMAIN?> can be used to obtain a free PASA for your users.
        It can be used by exchanges or wallet developers or any other PascalCoin
        related software that needs their users to have a PascalCoin account.
        </p>
        <p>The following table describes all available parameters that can be used for a request to <?=DOMAIN?>.</p>
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
                        affiliate account number. <strong>Please omit the checksum</strong>. See <a href="<?=DOMAIN?>/affiliate.php">Affilitate Page</a> for more info.</a>
                    </td>
                </tr>
                <tr>
                    <td><code>origin</code></td>
                    <td>
                        This value is for internal use to create statistics. You can
                        use whatever value you want, but please stay with the same
                        origin value in each request.
                    </td>
                </tr>
                <tr>
                    <td><code>phone_country_iso</code></td>
                    <td>
                        A 2 letter upper case ISO 3166 country code of the user
                        related to the phone number (to select the correct phone
                        region code).
                        Click <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">here</a>
                        for a list of available codes.
                    </td>
                </tr>
                <tr>
                    <td><code>phone_country_number</code></td>
                    <td>
                        The international country calling code for the phone number. For example 1 for USA, 49 for germany and so on.
                        Click <a href="https://en.wikipedia.org/wiki/List_of_country_calling_codes">here</a> for a complete list of codes.
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Please either use <code style="display: inline-block">phone_country_iso</code> or <code style="display: inline-block">phone_country_number</code> Do not use both at the same time.</td>
                </tr>
                <tr>
                    <td><code>phone</code></td>
                    <td>
                        The phone number of the user, <strong>without</strong> any country information.
                    </td>
                </tr>
                <tr>
                    <td><code>public_key</code></td>
                    <td>The public key of the user. The PASA account will be transferred to this key.</td>
                </tr>
                <tr>
                    <td><code>state</code></td>
                    <td>An internal state value to make sure the returning request is from you, just like OAuth2 states.</td>
                </tr>
                <tr>
                    <td><code>redirect</code></td>
                    <td>An Url where the user will be redirected to after the PASA account was assigned to him.</td>
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
                        <div class="twelve columns">
                            <label for="phone_country_iso">Phone country ISO</label>
                            <input type="text" class="u-full-width" name="phone_country_iso" id="phone_country_iso" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="phone_country_number">Phone international calling code</label>
                            <input type="text" class="u-full-width" name="phone_country_number" id="phone_country_number" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="phone">Phone number</label>
                            <input type="text" class="u-full-width" name="phone" id="phone" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="public_key">Public Key</label>
                            <textarea class="u-full-width" placeholder="Public key value.." id="public_key" name="public_key"></textarea>
                        </div>
                        <div class="twelve columns">
                            <label for="state">State</label>
                            <input type="text" class="u-full-width" name="state" id="state" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="redirect">Redirect URL</label>
                            <input type="text" class="u-full-width" name="redirect" id="redirect" value="">
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
