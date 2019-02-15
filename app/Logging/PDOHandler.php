<?php namespace DataStaging\Logging;

use Carbon\Carbon;
use DataStaging\Models\School;
use DataStaging\Util;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use PDO;

/**
 * Class PDOHandler
 * @package DataStaging\Logging
 * @requires DataStaging\Logging\ExtendedDbLoggingProcessor
 */
class PDOHandler extends AbstractProcessingHandler
{

    protected $school;
    private $initialized = false;
    private $table = 'process_log';
    private $pdo;
    private $statement;
    private $processToken;

    public function __construct(PDO $pdo, $level = Logger::DEBUG, $bubble = true)
    {
        $this->pdo = $pdo;
        $this->processToken = Util::createProcessToken();

        parent::__construct($level, $bubble);
    }

    public function setSchool(School $school)
    {
        $this->school = $school;
    }

    /**
     * Requires as context key: "process_token"
     * Optional context key: "context"
     * @param array $record
     */
    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $bindings = [
            'process_token' => $this->processToken,
            'school_code' => $this->school->getCode(),
            'file_type' => $record['extra']['fileType'],
            'validation_code' => $record['extra']['validationCode'],
            'level' => $record['level_name'],
            'level_code' => $record['level'],
            'message' => $record['message'],
            'context' => $this->stringify($record['context']),
            'channel' => $record['channel'],
        ];

        $this->statement->execute($bindings);
    }

    private function initialize()
    {
        $this->statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} (process_token, school_code, file_type, validation_code, channel, level, level_code, message, context) ".
                                "VALUES (:process_token, :school_code, :file_type, :validation_code, :channel, :level, :level_code, :message, :context)"
        );

        $this->initialized = true;
    }

    private function stringify($thing)
    {
        return $this->getFormatter()->stringify($thing);
    }
}