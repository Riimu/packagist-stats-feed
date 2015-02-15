# Packagist Stats Feed #

Packagist Stats Feed is a simple web application that generates RSS feeds to
track the download stats for [Packagist](https://packagist.org/) packages. This
makes it easier to track the popularity of your packages by getting frequently
updated statistics on how many times the packages have been downloaded.

To get the RSS feeds that lists the statistics for a specific user, use the URL
below, except replace the `USERNAME` with your username.

`http://feed.riimu.net/packagist/feed/USERNAME/`

Note that RSS feed are only updated when they are requested. Thus, the
statistics update frequency is based on how often the feed itself is requested.
It will only update once per hour at most, however.

When the package is seen for the first time, the feed item will be titled
"New package", but after that it will detail the changed statistics.

## Setting up ##

Setting up your own packagist stats feed application should not be a complicated
task. Only the following tasks should be required:

  * Run `composer install`
  * Make sure the `index.php` is reachable via your web server
  * Set up the database using `database.sql`
  * Configure the database settings in `config/database.php`
  
This web application has been designed to run on Apache, MySQL 5.2 and PHP 5.4
or later.

## Credits ##

This web application is copyright 2015 to Riikka Kalliomäki.

See LICENSE for license and copying information.
