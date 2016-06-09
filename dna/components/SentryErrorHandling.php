<?php

class SentryErrorHandling
{

    static public $client;

    public static function activate($sentryDsn)
    {

        // Init sentry
        static::$client = new \Raven_Client($sentryDsn);

        /*
        // Capture a message
        $event_id = $client->getIdent($client->captureMessage('my log message'));
        if ($client->getLastError() !== null) {
            printf('There was an error sending the event to Sentry: %s', $client->getLastError());
        }

        // Capture an exception
        $event_id = $client->getIdent($client->captureException($ex));

        // Provide some additional data with an exception
        $event_id = $client->getIdent($client->captureException($ex, array(
            'extra' => array(
                'php_version' => phpversion()
            ),
        )));

        // Give the user feedback
        echo "Sorry, there was an error!";
        echo "Your reference ID is " . $event_id;
        */

        // Install error handlers and shutdown function to catch fatal errors
        $error_handler = new \Raven_ErrorHandler(static::$client);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();

    }

    public static function logException(\Exception $e, $previous = false)
    {
        error_log(
            sprintf(
                '%s%s logged: "%s", file "%s", line "%s", backtrace: %s',
                ($previous ? '[previous] ' : ''),
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            )
        );
        if ($e->getPrevious()) {
            static::logException($e->getPrevious(), true);
        }
    }

    public static function captureException(\Exception $e)
    {
        $event_id = static::$client->getIdent(static::$client->captureException($e));
        if (static::$client->getLastError() !== null) {
            error_log(sprintf('There was an error sending the event to Sentry: %s', static::$client->getLastError()));
        }
    }

}

