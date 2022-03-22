<?php
namespace stock2shop\exceptions;

class UnprocessableEntity extends \Exception
{
    // HTTP Status Codes For Invalid Data: 400 vs. 422
    // https://www.bennadel.com/blog/2434-http-status-codes-for-invalid-data400-vs422.htm
    var $message = "Unprocessable Entity";
    var $statusCode = 422;
}