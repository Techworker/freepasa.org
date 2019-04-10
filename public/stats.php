<?php include './../bootstrap.php'; ?>

<?php
$records = \Database\Verifications\getDisbursed();
?>

<?php include __DIR__ . '/include/head.php'?>
    <div class="info-top">
        <div class="container">
            <div class="headline">Statistics</div>
        </div>
    </div>
    <div class="container content">
        <p>Disbursed a total of <strong><?=count($records); ?></strong> PASA to users.</p>
        <p><?=\Pascal\getWalletAccountsCount(); ?> accounts available.
        <table>
            <thead>
            <tr>
                <th>PASA</th>
                <th>Date</th>
                <th>Block</th>
                <th>Affiliate Account</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record) : ?>
                <tr>
    <!-- id <?= $record->id ?> -->
    <!-- origin <?= $record->origin ?> -->
    <!-- pasa <?= $record->pasa ?> -->
    <!-- state <?= $record->state ?> -->
    <!-- redirect <?= $record->redirect ?> -->
    <!-- ophash <?= $record->ophash ?> -->
    <!-- block <?= $record->block ?> -->
    <!-- phone_formatted <?= $record->phone_formatted ?> -->
    <!-- phone_number <?= $record->phone_number ?> -->
    <!-- phone_enc <?= $record->phone_enc ?> -->
    <!-- phone_last4 <?= $record->phone_last4 ?> -->
    <!-- country_number <?= $record->country_number ?> -->
    <!-- affiliate_account <?= $record->affiliate_account ?> -->
    <!-- affiliate_ophash <?= $record->affiliate_ophash ?> -->
    <!-- affiliate_amount <?= $record->affiliate_amount ?> -->
    <!-- tries <?= $record->tries ?> -->
    <!-- country_iso <?= $record->country_iso ?> -->
    <!-- b58_pubkey <?= $record->b58_pubkey ?> -->
    <!-- twilio_uuid <?= $record->twilio_uuid  ?> -->
    <!-- twilio_expires <?= $record->twilio_expires ?> -->
    <!-- verification_code <?= $record->verification_code ?> -->
    <!-- verification_success <?= $record->verification_success ?> -->
                    <!-- dt <?= $record->dt ?> -->
                    <!-- id <?= \Helper\decodeId($record->id) ?> -->
                    <td><?=\Pascal\withChecksum($record->pasa) ?></td>
                    <td><?=date('Y-m-d H:i:s', $record->dt)?></td>
                    <td><a href="http://explore.pascalcoin.org/blocks/<?=$record->block?>" target="_blank">&raquo; <?=$record->block?></a></td>
                    <td>
                        <?php if($record->affiliate_account > 0) : ?>
                        <a href="http://explore.pascalcoin.org/operations/<?=$record->affiliate_ophash?>" target="_blank">&raquo;<?=\Pascal\withChecksum($record->affiliate_account)?></a>
                        <?php else: ?>
                        [x]
                        <?php endif; ?>
                    </td>
                    <td><a href="http://explore.pascalcoin.org/operations/<?=$record->ophash?>" target="_blank">&raquo; view</a></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php include __DIR__ . '/include/foot.php'?>
