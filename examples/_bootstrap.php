<?php declare(strict_types=1);

use FooBarFighters\ZendServer\WebApi\Exception\ApiException;
use FooBarFighters\ZendServer\WebApi\Exception\NotAuthorizedException;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Tests\ZendServer\WebApi\TestCase;

//== important, the timezone of the client requesting should match the Zend Server timezone
date_default_timezone_set('Europe/Amsterdam');

const ROOT = __DIR__ . '/..';
const SRC = ROOT . '/src';

require ROOT . '/vendor/autoload.php';

/**
 * Provide an update to var_dump with Kint
 *
 * @url https://github.com/kint-php/kint
 *
 * @param array $v
 */
function ddd(...$v)
{
    !Kint::dump(...$v);
    exit;
}

/**
 * Wrapper function to run examples without having to duplicate all the exception types
 *
 * @param callable $fn
 *
 * @return mixed
 */
function runExample(callable $fn)
{
    try {
        return $fn(isset($_GET['mock']));
    }

    //== connection error
    catch (ConnectException $e) {
        reportError($e, 'Can\'t connect. Check hostname, VPN and internet connection.');
    }

    //== uh oh, check your config
    catch (NotAuthorizedException $e) {
        reportError($e, 'Not authorized to access the Zend Server API, check your credentials.');
    }

    //== ZS API is reporting that something went wrong
    catch (ApiException $e) {
        reportError($e, 'ZS WebApi responded with an error.', $e->getData());
    }

    //== everything else
    catch (Exception $e) {
        reportError($e);
    }

    return null;
}

/**
 * Return a custom Guzzle client, for instance to use for mocking an API response.
 *
 * @param bool        $userLogger
 * @param string|null $jsonFile
 *
 * @return Guzzle
 */
function getGuzzleClient(bool $userLogger = true, ?string $jsonFile = null): Guzzle
{
    if ($jsonFile) {
        return TestCase::getGuzzleClient($jsonFile);
    }

    $handlerStack = HandlerStack::create();
    if ($userLogger) {
        addLogger($handlerStack);
    }
    return new Guzzle(['handler' => $handlerStack]);
}

/**
 * Add Monolog logger for logging requests
 *
 * @param HandlerStack $stack
 * @param string       $logFile
 */
function addLogger(HandlerStack $stack, string $logFile = '../logs/request.log'): void
{
    $logger = new Logger('guzzle');
    $handler = new RotatingFileHandler($logFile, 1);
    $formatter = new LineFormatter("[%datetime%]\n%message%\n\n", "Y-m-d H:i:s", false, true);
    $formatter->includeStacktraces(true);
    $handler->setFormatter($formatter);
    $logger->pushHandler($handler);
    $stack->push(Middleware::log($logger, new MessageFormatter("{uri}\n{req_headers}\n\n{code}\n\n{res_body}\n\n{error}")));
}


/**
 * Return Zend Server web API credentials. Replace this function with a custom solution or use vhost SetEnv to assign
 * params in a similar way.
 *
 * @return array
 */
function getConfig(): array
{
    return [
        'baseUrl' => getenv('zs.api.o.baseUrl') ?: 'https://your.zend.server.url',
        'hash' => getenv('zs.api.o.hash'),
        'username' => getenv('zs.api.o.username'),
        'version' => getenv('zs.api.o.version'),
    ];
}

/**
 * @param Exception   $e
 * @param string|null $msg
 * @param array|null  $data
 */
function reportError(Exception $e, ?string $msg = null, ?array $data = null)
{
    $lines = [];
    $lines['code'] = "code: {$e->getCode()}";
    $lines['msg'] = "msg: {$e->getMessage()}"; //ddd($lines);

    echo '<div class="error">';
        if($msg){
            echo '<h3 class="custom-msg">' . $msg . '</h3>';
        }
        foreach($lines as $class => $value){
            echo "<div class=\"$class\">$value</div>";
        }
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        if($data){
            echo '<div class="data"><pre>' . print_r($data) . '</pre></div>';
        }
    echo '</div>';
    error_log((string)$e);
}
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        td {padding: 3px 15px;}
        th {text-align:left; padding: 3px 15px;}
        .custom-msg{background:red; padding:10px;}
        .error{background: lightsalmon;}
    </style>
</head>
