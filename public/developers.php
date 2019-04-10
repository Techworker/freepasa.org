<?php

include './../bootstrap.php';
?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline"><?=t_('developers', 'title')?></div>
        </div>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p><?=t_('developers', 'intro', DOMAIN)?></p>
        <p><?=t_('developers', 'following', DOMAIN)?></p>
        <table>
            <thead>
            <tr>
                <th><?=t_('developers', 'column_param')?></th>
                <th><?=t_('developers', 'column_description')?></th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%"><code>afac</code></td>
                    <td><?= t_('developers', 'afac', DOMAIN . '/affiliate.php?lang=' . $_GET['lang']) ?></td>
                </tr>
                <tr>
                    <td><code>origin</code></td>
                    <td><?= t_('developers', 'origin') ?></td>
                </tr>
                <tr>
                    <td><code>phone_country_iso</code></td>
                    <td><?= t_('developers', 'phone_country_iso') ?></td>
                </tr>
                <tr>
                    <td><code>phone_country_number</code></td>
                    <td><?= t_('developers', 'phone_country_number') ?></td>
                </tr>
                <tr>
                    <td colspan="2"><?= t_('developers', 'please_use') ?></td>
                </tr>
                <tr>
                    <td><code>phone</code></td>
                    <td><?= t_('developers', 'phone') ?></td>
                </tr>
                <tr>
                    <td><code>public_key</code></td>
                    <td><?= t_('developers', 'public_key') ?></td>
                </tr>
                <tr>
                    <td><code>state</code></td>
                    <td><?= t_('developers', 'state') ?></td>
                </tr>
                <tr>
                    <td><code>redirect</code></td>
                    <td><?= t_('developers', 'redirect') ?></td>
                </tr>
            </tbody>
        </table>
        <h3><?= t_('developers', 'link_builder') ?></h3>
        <div id="link-builder">
            <div class="row">
                <div class="six columns">
                    <div class="row">
                        <div class="twelve columns">
                            <label for="origin"><?= t_('developers', 'label_afac') ?></label>
                            <input type="text" class="u-full-width" name="afac" id="afac" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="origin"><?= t_('developers', 'label_origin') ?></label>
                            <input type="text" class="u-full-width" name="origin" id="origin" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="phone_country_iso"><?= t_('developers', 'label_phone_country_iso') ?></label>
                            <input type="text" class="u-full-width" name="phone_country_iso" id="phone_country_iso" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="phone_country_number"><?= t_('developers', 'label_phone_country_number') ?></label>
                            <input type="text" class="u-full-width" name="phone_country_number" id="phone_country_number" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="phone"><?= t_('developers', 'label_phone_number') ?></label>
                            <input type="text" class="u-full-width" name="phone" id="phone" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="public_key"><?= t_('developers', 'label_public_key') ?></label>
                            <textarea class="u-full-width" id="public_key" name="public_key"></textarea>
                        </div>
                        <div class="twelve columns">
                            <label for="state"><?= t_('developers', 'label_state') ?></label>
                            <input type="text" class="u-full-width" name="state" id="state" value="">
                        </div>
                        <div class="twelve columns">
                            <label for="redirect"><?= t_('developers', 'label_redirect') ?></label>
                            <input type="text" class="u-full-width" name="redirect" id="redirect" value="">
                        </div>
                    </div>
                </div>
                <div class="six columns">
                    <code id="link"><?=DOMAIN?></code>
                    <button id="copy-to-clipboard" class="button button-primary"><?= t_('developers', 'clipboard') ?></button>
                    <a class="button button-primary" href="<?=DOMAIN?>?lang=<?=$_GET['lang']?>" target="_blank" id="try-it"><?= t_('developers', 'try_it') ?></a>
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
