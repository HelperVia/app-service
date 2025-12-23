<?php
namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{

    protected $statusCode;
    protected $errorData;

    public function __construct(string $message = "", int $code = 400, array $errorData = [])
    {
        $this->statusCode = $code;
        $this->errorData = $errorData;
        parent::__construct($message, $code);
    }


    public function getStatusCode(): int
    {

        return $this->statusCode;
    }

    public function getErrorData()
    {
        return $this->errorData;
    }


}

