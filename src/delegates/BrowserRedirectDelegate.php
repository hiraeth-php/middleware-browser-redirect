<?php

namespace Hiraeth\Middleware;

use Hiraeth;
use Sinergi\BrowserDetector\Browser;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * {@inheritDoc}
 */
class BrowserRedirectDelegate extends AbstractDelegate
{
	/**
	 * {@inheritDoc}
	 */
	static public function getClass(): string
	{
		return BrowserRedirect::class;
	}


	/**
	 * {@inheritDoc}
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$configs    = $app->getConfig('*', 'browser.rules', []);
		$redirector = new BrowserRedirect(
			$app->get(Browser::class),
			$app->get(ResponseFactoryInterface::class)
		);

		foreach ($configs as $path => $rules) {
			$redirector->addRules(...$rules);
		}

		return $redirector;
	}
}
