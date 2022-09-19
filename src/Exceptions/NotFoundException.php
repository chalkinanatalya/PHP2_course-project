<?php
namespace Project\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use Exception;
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}