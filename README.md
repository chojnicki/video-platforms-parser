# Video Platforms Parser

Video Platforms Parser is easy to use SDK with grabber for multiple platforms at once, like YouTube, Dailymotion, Facebook and more.

## Requirements

- PHP 7.0 or higher
- Laravel 5.4 or higher (not tested on lower but should work on 5.*)


## Supported platforms
| Platform      | With API           |  Without API  |
| ------------- | -------------      | ------------- |
| YouTube       | YES (key required) |  YES          |
| Dailymotion   | YES                |  YES          |
| Facebook      | (not ready)        |  YES          |
| LiveLeak      | NO API             |  YES          |
| CDA           | NO API             |  YES          |
| Vimeo         | YES                |  YES          |

* With API - parser is using official API - fast and reliable (but YouTube require api key)
* Without API - parser will grab video page and parse required info (needed in platforms that do not provide API or as a backup) - can be slower and less reliable

Every parser that is using API also has parser without API as backup. To use it you need to disable API for selected platform in config (not recommended).


## Installation with Composer

Simply require package with composer:
```
composer require chojnicki/video-platforms-parser
```

## Installation without Composer or Laravel
Download zip of this repository and unpack in your PHP project.
Require VideoPlatformsParser file:
```
require '/video-platforms-parser/src/VideoPlatformsParser.php';
```


## Usage with Laravel

Require package with composer:
```
composer require chojnicki/video-platforms-parser
```

(for Laravel below 5.5) In `/config/app.php` add Service Provider:
```
Chojnicki\VideoPlatformsParser\ServiceProvider::class,
```
(for Laravel below 5.5) In `/config/app.php` add Facade:
```
'VideoPlatformsParser' => Chojnicki\VideoPlatformsParser\Facade::class,
```

Publish config:
```
php artisan vendor:publish --provider="Chojnicki\VideoPlatformsParser\ServiceProvider"
```

Now You can start grabbing info like this:
```
$info = VideoPlatformsParser::get('https://www.youtube.com/watch?v=jofNR_WkoCE');
```


## Usage without Laravel but with Composer
Require Composer to project if it was not required before
```
require_once 'vendor/autoload.php';
```

Create new object:
```
$parser = new Chojnicki\VideoPlatformsParser\VideoPlatformsParser(); // put array config inside
```

Grab video info like this:
```
$info = $parser->get('https://www.youtube.com/watch?v=jofNR_WkoCE');
```

## Usage without Laravel and without Composer
Require parser
```
require '/video-platforms-parser/src/VideoPlatformsParser.php';
```
Manually load parsers for every video platform that will be used
```
require '/video-platforms-parser/src/parsers/YouTube.php';
```

Create new object:
```
$parser = new Chojnicki\VideoPlatformsParser\VideoPlatformsParser(); // put array config inside
```

Grab video info like this:
```
$info = $parser->get('https://www.youtube.com/watch?v=jofNR_WkoCE');
```


## Returned data

For every supported platform parser will return array with:

- id: video ID
- platform: site name
- title: video title
- description: video description
- thumbnail: url for image with highest possible resolution
- tags: array with keywords
- api: will be true if official platform API was used and false otherwise

More info in future :)
