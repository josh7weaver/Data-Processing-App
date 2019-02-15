<?php namespace DataStaging\Logging;

/**
 * Process logging meant for the DB
 */
class ExtendedDbLoggingProcessor
{
    /**
     * @param array $record - supports the following keys:
     *      'fileType' => (string) base class name - WITHOUT namespace
     *      'validationType' => (string) file|row
     * @return array
     */
    public function __invoke(array $record)
    {
        if(!is_array($record['context'])) return $record;

        // move key/values from context to 'extra'
        $record['extra'] = [
            'fileType' => array_pull($record['context'], 'fileType'),
            'validationCode' => array_pull($record['context'], 'validationCode'),
        ];

        return $record;
    }
}