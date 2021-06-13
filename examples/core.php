<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php require '_bootstrap.php'; ?>
</head>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

//== lets use the core client to fetch a mocked raw API response
$response = runExample(static function (): array {
    $client = ClientFactory::createClient(getConfig(), getGuzzleClient(false, '200.getApplicationStatus.json'));
    return $client->getApplicationStatus();
});

//== print partial response
echo '<pre>' . print_r($response['responseData'], true) . '</pre>';
?>
</body>
</html>

