<!DOCTYPE html>
<?php require 'bootstrap.php'; ?>
<body>
<?php
/**
 * Example of instantiating a core client without using the factory:
 */

use FooBarFighters\ZendServer\WebApi\Client\Core\Client;

$client = new Client(
    'https://your.zend.server.url'
    , 'b8ba2dad94c1eb100eab4bcc1599d8f94d22a96ff0fa2bcfec7d72235f08123e'
    , 'fbf'
    , 1.23
);

ddd($client);

?>
</body>
</html>

