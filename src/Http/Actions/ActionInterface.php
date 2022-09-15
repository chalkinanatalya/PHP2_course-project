<?php
namespace Project\Http\Actions;

use Project\Http\Request\Request;
use Project\Http\Response\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
