<?php namespace DataStaging\Exceptions;

use Exception;

class RowValidationException extends \Exception
{
    /**
     * @var string
     */
    protected $validationCode;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var array
     */
    private $currentRow;

    /**
     * Row Exception - this arrangement removes the dependency on argument order
     * @param string $message [optional] The Exception message to throw.
     * @param array  $args  see arg list
     *
     * @arg array  $currentRow
     * @arg string $validationCode The validation code - must be one of the constants defined on RowValidator and FileValidator
     * @arg array  $context
     * @arg int $code [optional] The Exception code.
     * @arg Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = "", $args = [])
    {
        // custome attributes with GETTERS
        $this->currentRow = isset($args['currentRow']) ? $args['currentRow'] : '';
        $this->validationCode = isset($args['validationCode']) ? $args['validationCode'] : '';
        $this->context = isset($args['context']) ? $args['context'] : [];

        // setup for parent exception constructor
        $code = isset($args['code']) ? $args['code'] : 0;
        $previous = isset($args['previous']) ? $args['previous'] : null;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getCurrentRow()
    {
        return $this->currentRow;
    }

    /**
     * @return string
     */
    public function getValidationCode()
    {
        return $this->validationCode;
    }

}