# Report Emailer [![Latest Stable Version](https://poser.pugx.org/midnite81/report-emailer/version)](https://packagist.org/packages/midnite81/report-emailer) [![Total Downloads](https://poser.pugx.org/midnite81/report-emailer/downloads)](https://packagist.org/packages/midnite81/report-emailer) [![Latest Unstable Version](https://poser.pugx.org/midnite81/report-emailer/v/unstable)](https://packagist.org/packages/midnite81/report-emailer) [![License](https://poser.pugx.org/midnite81/report-emailer/license.svg)](https://packagist.org/packages/midnite81/report-emailer)
This allows for easy report emailing in Laravel 5

Work in progress - proper documentation to follow.

# Installation

This package requires PHP 5.6+, and includes a Laravel 5 Service Provider.

To install through composer include the package in your `composer.json`.

    "midnite81/report-emailer": "0.0.*"

Run `composer install` or `composer update` to download the dependencies or you can run `composer require midnite81/report-emailer`.

## Register the service provider 

To use the package with Laravel 5 firstly add the GeoLocation service provider to the list of service providers 
in `app/config/app.php`.

    'providers' => [

      Midnite81\ReportEmailer\ReportEmailerServiceProvider::class
              
    ];

## Further information

This readme will be updated in due course as this package is under current pre-release development.