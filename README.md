# Pipolino
[![Build Status](https://img.shields.io/scrutinizer/build/g/phuria/pipolino.svg?maxAge=3600)](https://scrutinizer-ci.com/g/phuria/pipolino/build-status/master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/phuria/pipolino.svg?maxAge=3600)](https://scrutinizer-ci.com/g/phuria/pipolino/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/phuria/pipolino.svg?maxAge=3600)](https://scrutinizer-ci.com/g/phuria/pipolino/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/phuria/pipolino.svg?maxAge=3600)](https://packagist.org/packages/phuria/pipolino)
[![license](https://img.shields.io/github/license/phuria/pipolino.svg?maxAge=2592000?style=flat-square)]()
[![php](https://img.shields.io/badge/PHP-5.6-blue.svg?maxAge=2592000)]()

The package provides dispatcher that can be used to process
one or more values in sequence. It is a combination of 
[thephpleague/pipeline](https://github.com/thephpleague/pipeline) 
and [PSR-7 middleware dispatcher (eg. Relay)](https://github.com/relayphp/Relay.Relay).

## Installation 

```
composer require phuria/pipolino
```

## Usage

```php
$pipolino = (new Pipolino)->addStage(function(callable $next, $payload) {
    return $next($payload * 2);
});

echo $pipolino->process(20); //output: 40
```

## Immutable

Pipolino are implemented as __immutable__. Any change on object (eg. add new stage)
creates new object.

## Many arguments

Pipolino (in contrast to pipeline) accepts any number of arguments to process.

```php
$pipolino = (new Pipolino)
    ->addStage(function (callable $next, $result, array $options) {
        if (null === $result) {
            return $options['onNull'];
        }
    
        return $next($result, $context);
    })
    ->addStage(function (callable $next, $result, array $options) {
        $formatted = $next(number_format($result, 2).' '.$options['currency'])
    
        // Can be replaced with: "return $forrmated;",
        // however, should call $next().
        return $next($formatted, $options); 
    });

$options = ['onNull' => '-', 'currency' => 'USD'];

echo $pipolino->process(null, $options); // output: -
echo $pipolino->process(10, $options); // output: 10.00 USD
```

## Pipolino composite

Each pipolino can be used as stage. This allows for easy re-use pipolino.

```php
$loadingProcess = (new Pipolino())
    ->add(new CheckCache())
    ->add(new LoadFromDB());
    
$showProcces = (new Pipolino())
    ->add(new JsonFormat())
    ->add($loadingProccess)
    ->add(new NullResult());
```

## Default stage

Default stage is last stage in pipolino, 
and does not accept `$next` callable as first argument.
If you need to alter behavior of default stage (return first argument)
call `Pipolino::withDefaultStage()` method.

```php
$pipolino = (new Pipolino)
    ->addStage(function (callable $net, $i) {
        if (is_string($i)) {
            $i = (int) $i;
        }
        
        return $next($i);
    })
    ->addStage(function (callable $next, $i) {
        if (is_int($i)) {
            return $i % 2 ? 'even' : 'odd';
        }
        
        return $next($i);
    })
    ->withDefaultStage(function () {
        throw new \Exception('Unable to guess type.');
    });
    
echo $pipolino->process(2); // output: even
echo $pipolino->process('4'); // output: even
echo $pipolino->proccess(2.50); // throws exception
```
