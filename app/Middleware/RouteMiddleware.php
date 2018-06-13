<?php

namespace App\Middleware;

/**
 * Middleware
 *
 * @copyright    Copyright (c) 
 */
class RouteMiddleware
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}
	
	
}