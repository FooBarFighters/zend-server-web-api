<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

/**
 * Rollback an application to the previous version.
 */

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

$response = runExample(static function (bool $useMock): array {
    $client = ClientFactory::createClient(getConfig(), getGuzzleClient(!$useMock, $useMock ? '202.applicationRollback.json' : null));

    return $client->applicationRollback(44);
});

//== print partial response
echo '<pre>' . print_r($response['responseData'], true) . '</pre>';
?>
</body>
</html>
