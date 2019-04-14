<?php
namespace Chojnicki\VideoPlatformsParser;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return VideoPlatformsParser::class;
    }
}
