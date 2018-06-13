<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

/*-------------------------------------------------------------------
|                  Public/Home Pages Routes                          |
---------------------------------------------------------------------*/

require __DIR__ . '/public/index.php';


/*-------------------------------------------------------------------
|                  Admin Section Routes                              |
---------------------------------------------------------------------*/


require __DIR__ . '/admin/index.php';
