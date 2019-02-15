<?php namespace DataStaging\Exceptions;

class FileValidationException extends \Exception
{
    /**
     * @var string
     */
    private $validationCode;

    /**
     * @var array
     */
    private $context;

    /**
     * FileValidationException constructor.
     * @param string $message
     * @param array  $args see arg list
     *
     * @arg param string $validationCode
     * @arg param array $context
     * @arg param int $code [optional] The Exception code.
     * @arg param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = "", $args = [])
    {
        // custome attributes with GETTERS
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
     * @return int
     */
    public function getValidationCode()
    {
        return $this->validationCode;
    }
}