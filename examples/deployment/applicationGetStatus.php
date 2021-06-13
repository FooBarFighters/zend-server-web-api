<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

//== lets use the core client to fetch a mocked raw API response
$response = runExample(static function (): array {
    $client = ClientFactory::createClient(getConfig(), getGuzzleClient(false, '200.applicationGetStatus.json'));
    return $client->applicationGetStatus();
});

//== print partial response
echo '<pre>' . print_r($response['responseData'], true) . '</pre>';
?>
</body>
</html>
