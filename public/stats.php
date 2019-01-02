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
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record) : ?>
                <tr>
                    <td><?=\Pascal\withChecksum($record->pasa) ?></td>
                    <td><?=date('y-m-d H:i:s', $record->dt)?></td>
                    <td><a href="http://explorer.pascalcoin.org/findoperation.php?ophash=<?=$record->ophash?>" target="_blank">&raquo; view</a></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php include __DIR__ . '/include/foot.php'?>