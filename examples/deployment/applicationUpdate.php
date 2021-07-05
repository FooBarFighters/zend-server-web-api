<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;
use FooBarFighters\ZendServer\WebApi\Model\App;
use FooBarFighters\ZendServer\WebApi\Model\Package;
use FooBarFighters\ZendServer\WebApi\Util\PackageBuilder;

$app = runExample(static function (bool $useMock): ?App {
    //== app we want to update
    $appName = 'FooU2';

    $config = getConfig();
    $guzzle = getGuzzleClient(!$useMock, $useMock ? '200.applicationGetStatus.json' : null);

    //== create a Zend Server API client
    $zs = ClientFactory::createExtendedClient($config, $guzzle);

    //== request the app data first so we can find the id
    if ($app = $zs->getApps()->filterByName($appName)) {

        //== create a basic package for testing
        $package = PackageBuilder::createDummy($appName, ROOT, 'test_');

        //== in case of a mocked response we need to create a new client
        if ($useMock) {
            $guzzle = getGuzzleClient(false, '202.applicationUpdate.json');
            $zs = ClientFactory::createExtendedClient($config, $guzzle);
        }

        //== deploy package
        return $zs->updateApp($app->getId(), $package->getFilePath(), true, ['foo' => 'bar', 'baz' => 'bal']);
    }
    return null;
});
?>

<?php if ($app): ?>
    <p>Updated <?php echo $app->getName(); ?> to version <b><?php echo $app->getDeployedVersion(); ?></b></p>
<?php else: ?>
    <p>Uh oh, something went wrong. Perhaps the app wasn't found.</p>
<?php endif; ?>

</body>
</html>
