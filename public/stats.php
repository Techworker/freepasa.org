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
                    <td><?=\Pascal\withChecksum($record->pasa) ?></td>
                    <td><?=date('Y-m-d H:i:s', $record->dt)?></td>
                    <td><a href="http://new-explorer.pascalcoin.org/blocks/<?=$record->block?>" target="_blank">&raquo; <?=$record->block?></a></td>
                    <td>
                        <?php if($record->affiliate_account > 0) : ?>
                        <a href="http://new-explorer.pascalcoin.org/operation/<?=$record->affiliate_ophash?>" target="_blank">&raquo;<?=\Pascal\withChecksum($record->affiliate_account)?></a>
                        <?php else: ?>
                        [x]
                        <?php endif; ?>
                    </td>
                    <td><a href="http://new-explorer.pascalcoin.org/operation/<?=$record->ophash?>" target="_blank">&raquo; view</a></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php include __DIR__ . '/include/foot.php'?>
