<?php
// Properly handle error logging, as well as a fatal error workaround
//require_once(__DIR__ . '/autoload.php');
require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/autoload.php');
error_reporting(0);
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function('fatalErrorShutdownHandler');

$userContext = null;

function handle404() {
    http_response_code(404);
    print json_encode('404 found');
}

function before() {
    assertApiIsEnabled();

    $token = \BusinessLogic\Helpers::getHeader('X-AUTH-TOKEN');
    buildUserContext($token);
}

function assertApiIsEnabled() {
    return true;
}

function buildUserContext($xAuthToken) {
    global $applicationContext, $userContext, $hesk_settings;

    /* @var $userContextBuilder \BusinessLogic\Security\UserContextBuilder */
    $userContextBuilder = $applicationContext->get['UserContextBuilder'];

    $userContext = $userContextBuilder->buildUserContext($xAuthToken, $hesk_settings);
}

function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine) {
    throw new Exception(sprintf("%s:%d\n\n%s", $errorFile, $errorLine, $errorMessage));
}

/**
 * @param $exception Exception
 */
function exceptionHandler($exception) {
    if (exceptionIsOfType($exception, 'ApiFriendlyException')) {
        /* @var $castedException \BusinessLogic\Exceptions\ApiFriendlyException */
        $castedException = $exception;

        print_error($castedException->title, $castedException->getMessage(), $castedException->httpResponseCode);
    } else {
        if (exceptionIsOfType($exception, 'SQLException')) {
            /* @var $castedException \Core\Exceptions\SQLException */
            $castedException = $exception;
            print_error("Fought an uncaught Exceptions", sprintf("%s\n\n%s", $castedException->failingQuery, $exception->getTraceAsString()));
        } else {
            print_error("Fought an uncaught Exceptions", sprintf("%s\n\n%s", $exception->getMessage(), $exception->getTraceAsString()));
        }

    }
    // Log more stuff to logging table if possible; we'll catch any exceptions from this
    die();
}

/**
 * @param $exception Exception thrown Exceptions
 * @param $class string The name of the expected Exceptions type
 * @return bool
 */
function exceptionIsOfType($exception, $class) {
    return strpos(get_class($exception), $class) !== false;
}

function fatalErrorShutdownHandler() {
    $last_error = error_get_last();
    if ($last_error['type'] === E_ERROR) {
        // fatal error
        errorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
    }
}

Link::before('before');

Link::all(array(
    // Categories
    '/v1/categories' => '\Controllers\Category\CategoryController::printAllCategories',
    '/v1/categories/{i}' => '\Controllers\Category\CategoryController',
    // Tickets
    '/v1/tickets/{i}' => '\Controllers\Tickets\TicketController',

    // Any URL that doesn't match goes to the 404 handler
    '404' => 'handle404'
));