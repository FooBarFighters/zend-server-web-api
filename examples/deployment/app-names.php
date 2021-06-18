<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;
use FooBarFighters\ZendServer\WebApi\Repository\AppList;

$appList = runExample(static function (bool $useMock): ?AppList {
    $client = ClientFactory::createExtendedClient(
        getConfig(),
        getGuzzleClient(!$useMock, $useMock ? '200.applicationGetStatus.json' : null)
    );
    return $client->getApps();
});
?>

<?php if (empty($appList)): ?>
    <p>no results</p>
<?php else: ?>
    <h1>Apps</h1>
    <table>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Version</th>
            <th>Updated</th>
        </tr>
        <?php foreach ($appList as $app): ?>
            <tr>
                <td><?php echo $app->getId(); ?></td>
                <td><?php echo $app->getName(); ?></td>
                <td><?php echo $app->getDeployedVersion(); ?></td>
                <td><?php echo $app->getTimestampAsString(); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
