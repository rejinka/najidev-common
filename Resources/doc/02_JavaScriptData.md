# NajiDev\Common\JavaScriptData Namespace

## Introduction

In nearly every webapplication, i was involved in, i asked myself how to use variables in JavaScript, which are stored
in the database.

Imagine geocordinates, stored in a database (or fetched from a geocoding-API). Those are variables, which exist in PHP,
but not in the clients browser. There are several ways to access this variables in JavaScript-Code. For example
rendering each variable in a hidden span or render the js code, containing the necessary variables, in php.

## Usage

	use \NajiDev\Common\JavaScriptData\Container;

	// initialize container
	$container   = new Container();

	// set some variables
	$container->set('global.autocompletion.url', '...');
	$container->set('global.another_variable', '...');
	$container->set('another_namespace.variable, true);

	// anywhere in the template:
	<div id="data" style="display: hidden">
	  <?php echo $container->getTransformedData(); ?>
	</div>

This will get you something like this (in a more compressed form), in your markup:

	<div id="data" style="display: hidden">
		{
			  global : {
				  autocompletion : {
					  url : '...'
				  },
				  another_variable : '...'
			  },
			  another_namespace : {
				  variable : true
			  }
		}
	</div>

So the div contains a valid json-encoded object, which you can parse with JSON.parse() in JavaScript. Supposed you did
something like this on the js code

	var data = JSON.parse($('#data').html());

You can access the variables with the same keys, as specified in php:

	console.log(data.global.autocompletion.url);