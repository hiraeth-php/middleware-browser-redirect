<?php

namespace Hiraeth\Middleware;

use InvalidArgumentException;
use Sinergi\BrowserDetector\Browser;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

/**
 * A middleware for redirecting browsers
 */
class BrowserRedirect implements Middleware
{
	/**
	 * Create a new instacne of the middleware
	 */
	public function __construct(Browser $browser, ResponseFactory $factory)
	{
		$this->browser = $browser;
		$this->factory = $factory;
	}


	/**
	 *
	 */
	public function addRule(array $rule)
	{
		foreach (['target', 'browser', 'version', 'redirect'] as $param) {
			if (!isset($rule[$param])) {
				throw new InvalidArgumentException(sprintf(
					'Invalid rule, you must specify a %s value',
					$param
				));
			}
		}

		$this->rules[] = $rule;

		return $this;
	}


	/**
	 *
	 */
	public function addRules(array ...$rules)
	{
		foreach ($rules as $rule) {
			$this->addRule($rule);
		}

		return $this;
	}


	/**
	 * {@inheritDoc}
	 */
	public function process(Request $request, Handler $handler): Response
	{
		$browser = $this->browser->getName();
		$version = $this->browser->getVersion();
		$target  = $request->getUri()->getPath();
		$rules   = array_filter($this->rules, function($rule) use ($browser, $version, $target) {

			//
			// Check Browser
			//

			if ($browser != $rule['browser']) {
				return FALSE;
			}

			//
			// Check Version
			//

			$match = FALSE;

			foreach ((array) $rule['version'] as $vcondition) {
				if ($vcondition[0] == '>' && $version > substr($vcondition, 1)) {
					$match = TRUE;
				} elseif ($vcondition[0] == '<' && $version < substr($vcondition, 1)) {
					$match = TRUE;
				} elseif ($version == $vcondition) {
					$match = TRUE;
				}
			}

			if (!$match) {
				return FALSE;
			}

			//
			// Check Path
			//

			if (!preg_match('#' . $rule['target'] . '#', $target)) {
				return FALSE;
			}

			return TRUE;
		});

		if (count($rules)) {
			return $this->factory->createResponse(303)->withHeader('Location', $rules[0]['redirect']);
		}

		return $handler->handle($request);
	}

}
