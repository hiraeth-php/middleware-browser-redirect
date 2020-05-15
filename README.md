This is a PSR-15 middleware that can be configured with a series of rules to detect specific browsers, versions, and request paths and redirect them to an alternate URL.

## Basic Usage

```php
//
// The factory must be an actual instance of a response factory, the PSR interface here is
// used as a stand in for any particular PSR compatible implementation.
//

$factory  = new Psr\Http\Message\ResponseFactoryInterface();
$browser  = new Sinergi\BrowserDetector\Browser();
$redirect = new BrowserRedirect($browser, $factory);

$redirect->addRule([
	'target'   => '/.*',
	'browser'  => 'IE',
	'version'  => '<10',
	'redirect' => 'https://getfirefox.com'
]);

//
// This will actually be executed by your middleware stack, so you probably don't need to do this
// yourself.
//

$response = $redirect->process($request, $handler);
```

## Hiraeth Integration

1. Package configuration and middleware configuration will be copied on install
2. Additional rules can be added to any config as an array of objects:

```toml
[browser]

	rules = [
		{
			"target": "/.*",
			"browser": "IE",
			"version": "<10",
			"redirect": "https://getfirefox.com"
		}
	]
```
