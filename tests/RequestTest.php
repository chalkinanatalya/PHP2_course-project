<?php
namespace Test;

use Project\Http\Request\Request;
class RequestTEst extends Request 
{
    public function __construct(
        private array $get,
        private array $server,
        private string $body,
        private string $header
    ) {
    }

    public function header(string $header): string
    {
        return $this->header;
    }
};