<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php require '_bootstrap.php'; ?>
</head>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;
use FooBarFighters\ZendServer\WebApi\Model\App;
use FooBarFighters\ZendServer\WebApi\Util\PackageBuilder;

$app = runExample(static function (bool $useMock): ?App {
    //== app we want to update
    $appName = 'FooU2';

    $config = getConfig();
    $guzzle = getGuzzleClient(!$useMock, $useMock ? '200.getApplicationStatus.json' : null);

    //== create a Zend Server API client
    $zs = ClientFactory::createExtendedClient($config, $guzzle);

    //== request the app data first so we can find the id
    if ($app = $zs->getApps()->filterByName($appName)) {

        //== create a basic package for testing
        $zipPath = PackageBuilder::createDummy($appName, ROOT, 'test_');

        //== in case of a mocked response we need to create a new client
        if ($useMock) {
            $guzzle = getGuzzleClient(false, '202.applicationUpdate.json');
            $zs = ClientFactory::createExtendedClient($config, $guzzle);
        }

        //== deploy package
        return $zs->updateApp($app->getId(), $zipPath);
    }
    return null;
});
?>

<?php if ($app): ?>
    <p>Updated <?php echo $app->getName(); ?> to version <b><?php echo $app->getDeployedVersion(); ?></b></p>
<?php else: ?>
    <p>uh oh, something went wrong.</p>
<?php endif; ?>

</body>
</html>
