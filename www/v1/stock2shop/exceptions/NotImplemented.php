<?php
namespace stock2shop\exceptions;

/**
 * Thrown when the user calls a function that might be available in future,
 * but it has not yet been implemented.
 *
 * @package stock2shop\exceptions
 */
class NotImplemented extends \Exception
{
    var $message = "Not Implemented";
    var $statusCode = 501;
}