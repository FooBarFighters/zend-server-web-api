<!DOCTYPE html>
<?php require '../bootstrap.php'; ?>
<body>
<?php

use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;
use FooBarFighters\ZendServer\WebApi\Client\Core\Method\LogName;

$response = runExample(static function (bool $useMock)  {

    $client = ClientFactory::createClient(
        getConfig(),
        getGuzzleClient(!$useMock, $useMock ? '200.logsReadLines.json' : null)
    );

    return $client->logsReadLines(LogName::PHP, null, 10, 'Deprecated');
});

echo '<pre>' . print_r($response['responseData']['logLines'], true) . '</pre>';
?>

</body>
</html>
