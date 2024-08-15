<?php

namespace Chojnicki\VideoPlatformsParser\Tests;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;

class VideoPlatformsParserTest extends TestCase
{
    private $apiKeys = [];

    protected function setUp():void
    {
        if (file_exists(__DIR__ . '/apiKeys.php')) {
            $this->apiKeys = require __DIR__ . '/apiKeys.php';
        }

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
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
    }

    public function testGetShortenYouTubeUrlOnApiDisabled()
    {
        $url = 'https://youtu.be/jofNR_WkoCE';
        $params = [
            'youtube_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
    }

    public function testGetYouTubeUrlOnApiEnabled()
    {
        if (empty($this->apiKeys['youtube'])) return;

        $url = 'https://www.youtube.com/watch?v=jofNR_WkoCE';
        $params = [
            'youtube_api_key' => $this->apiKeys['youtube'],
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

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
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
    }

    public function testGetVimeoUrlOnApiDisabled()
    {
        $url = 'https://vimeo.com/126100721';
        $params = [
            'vimeo_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
    }

    public function testGetStreamableUrlOnApiDisabled()
    {
        $url = 'https://streamable.com/idw7xq';
        $params = [
            'streamable_api_disabled' => true,
        ];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('duration', $result);
    }

    public function testGetStreamableUrlOnApiEnabled()
    {
        $url = 'https://streamable.com/idw7xq';
        $params = [];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
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
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
    }

    public function testGetTwitterUrlOnApiDisabled()
    {
        $url = 'https://twitter.com/vuejsamsterdam/status/1356624340737998848';
        $params = ['twitter_api_disabled' => true];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('platform', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        $this->assertArrayHasKey('tags', $result);
    }

    public function testGetTwitterUrlOnApiEnabled()
    {
        if (empty($this->apiKeys['twitter'])) return;

        $url = 'https://twitter.com/vuejsamsterdam/status/1356624340737998848';
        $params = ['twitter_api_bearer_token' => $this->apiKeys['twitter']];
        $videoPlatformsParser = new VideoPlatformsParser($params);
        $result = $videoPlatformsParser->get($url);
        $result = array_filter($result);

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
