Paymill-Codeigniter
===================

Paymill library for Codeigniter

# Usage

1. Copy /config/paymill.php, libraries/Paymill and libraries/paymill.php in your project.

2. Put your keys in /config/paymill.php

	$config['paymill_test'] = TRUE;
	
	$config['paymill_apiKey_test'] = '';
	$config['paymill_apiEndPoint_test'] = 'https://api.paymill.de/v2/';
	
	$config['paymill_apiKey'] = '';
	$config['paymill_apiEndPoint'] = 'https://api.paymill.de/v2/';
	
3. Include paymill.php library in your controller and enjoy!

## Bugs

Please report bugs at https://github.com/Saldum/Paymill-Codeigniter/issues

## Changelog

### 1.0 - 27st December 2012

- Hello world :D

## Author

* [Ignacio Soriano](http://twitter.com/isocano)

## TODO

- More Test
- Add new functions like Preauthorizations (when Paymill support this)

## License

MIT