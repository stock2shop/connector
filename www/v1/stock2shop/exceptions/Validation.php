<?php
namespace stock2shop\exceptions;

class Validation extends \Exception
{
    var $message = "Bad request";
    var $statusCode = 400;
}