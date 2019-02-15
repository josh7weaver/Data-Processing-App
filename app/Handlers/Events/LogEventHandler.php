<?php namespace DataStaging\Handlers\Events;

class LogEventHandler {

    public function handle($level, $message, $context)
    {
        // this just prints errors to the screen in the command
        if ($level == 'error' || $level == 'warning') {
            echo strtoupper($level) . " :: "; // prepend level
        }

        echo "$message";
        if ( !empty($context) ) {
            var_dump( $context );
        }
    }
}