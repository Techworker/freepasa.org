<?php include './../bootstrap.php'; ?>
<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline">About the service</div>
        </div>
    </div>
    <div class="container content">
        <p>
            The PASA service is a free service provided by the PascalCoin foundation.
            It allows you to obtain a free PASA account at no cost.
        </p>
        <p>
            Your phone number will be saved (hashed) on the server after the PASA is distributed
            successfully. If the verification fails for whatever reason, it will be deleted after 1 hour.
        </p>
        <p>
            It uses twilio as a SMS service provider. For more info on this
            service please see the <a href="https://www.twilio.com" target="_blank">twilio homepage.</a>
        </p>
        <p>
            The source code of this service is open sourced and accessible <a href="https://github.com/Techworker/freepasa.org">here</a>.
        </p>

        <button class="button-primary u-pull-right" onclick="window.location.href='<?=DOMAIN?>'">Back to Home</button>
    </div>

<?php include __DIR__ . '/include/foot.php'?>