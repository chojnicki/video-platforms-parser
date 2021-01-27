<?php

namespace Chojnicki\VideoPlatformsParser\Tests;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;

class VideoPlatformsParserTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testGetYouTubeUrlOnApiDisabled()
    {
        $url = 'https://www.youtube.com/watch?v=jofNR_WkoCE';
        $params = [
            'youtube_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetShortenYouTubeUrlOnApiDisabled()
    {
        $url = 'https://youtu.be/watch?v=jofNR_WkoCE';
        $params = [
            'youtube_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetDailymotionUrlOnApiDisabled()
    {
        $url = 'https://www.dailymotion.com/video/x32w0hb';
        $params = [
            'dailymotion_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetVimeoUrlOnApiDisabled()
    {
        $url = 'https://vimeo.com/126100721';
        $params = [
            'vimeo_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetStreamableUrlOnApiDisabled()
    {
        $url = 'https://streamable.com/9f5cev';
        $params = [
            'streamable_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetLiveLeakUrl()
    {
        $url = 'https://www.liveleak.com/view?t=stuEM_1605476833';
        $params = [];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testGetFacebookUrl()
    {
        $url = 'https://www.facebook.com/thatofficeguyuk/videos/363207927987366';
        $params = [];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('api', $result);
    }

    public function testDetectUrlOnUnsupportedUrlShouldReturnFalse()
    {
        $url = 'https://google.com';
        $params = [];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->detectURL($url);

        $this->assertFalse($result);
    }
}
