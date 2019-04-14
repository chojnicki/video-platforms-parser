# Video Platforms Parser

Video Plarforms Parser is easy to use SDK for multiple platforms at once, like YouTube or Dailymotion.

## Requirements

- PHP 7.0 or higher
- Laravel 5.4 or higher (not tested on lower but should work on 5.*)

## Instalation with Composer

Simply require package with composer:
```
composer require chojnicki/video-platforms-grabber
```

## Instalation without Composer or Laravel
Download zip of this repository and unpack in your PHP project.
Require VideoPlatformsParser file:
```
require '/video-platforms-parser/src/VideoPlatformsParser.php';
```


## Usage with Laravel

Require package with composer:
```
composer require chojnicki/video-platforms-grabber
```

In `/config/app.php` add service provider (not required from Laravel 5.4):
```
Chojnicki\VideoPlatformsParser\ServiceProvider::class,
```

Add also Facade:
```
'VideoPlatformsParser' => Chojnicki\VideoPlatformsParser\Facade::class,
```

Publish config:
```
$ php artisan vendor:publish --provider="Chojnicki\VideoPlatformsParser\ServiceProvider"
```

Now You can start grabbing info like this:
```
$info = VideoPlatformsParser::get('https://www.youtube.com/watch?v=jofNR_WkoCE');
```


## Usage without Laravel

Create new object:
```
$parser = new VideoPlatformsParser();
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

More grabbed info in future :)
