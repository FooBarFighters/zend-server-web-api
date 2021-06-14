<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

//== lets use the core client to fetch a mocked raw API response
$response = runExample(static function (bool $useMock): array {
    $client = ClientFactory::createClient(
        getConfig(),
        getGuzzleClient(!$useMock, $useMock ? '200.applicationGetDetails.json' : null)
    );
    return $client->applicationGetDetails(44);
});

//== print partial response
echo '<pre>' . print_r($response['responseData'], true) . '</pre>';
?>
</body>
</html>
