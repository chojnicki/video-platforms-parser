<?php

namespace Chojnicki\VideoPlatformsParser;

use Chojnicki\VideoPlatformsParser\parsers\YouTube;
use Chojnicki\VideoPlatformsParser\parsers\Dailymotion;
use Chojnicki\VideoPlatformsParser\parsers\LiveLeak;
use Chojnicki\VideoPlatformsParser\parsers\Facebook;
use Chojnicki\VideoPlatformsParser\parsers\Twitter;
use Chojnicki\VideoPlatformsParser\parsers\Vimeo;
use Chojnicki\VideoPlatformsParser\parsers\CDA;
use Chojnicki\VideoPlatformsParser\parsers\Streamable;
use Exception;

class VideoPlatformsParser
{
    private $params = [];


    /**
     * VideoPlatformsParser constructor
     * @param array $params - options
     */
    public function __construct($params = [])
    {
        $this->params = $params;
    }


    /**
     * Main function that will recognize site from URL and use correct parser
     *
     * @param $url
     * @return array
     * @throws Exception
     */
    public function get($url)
    {
        /* Detect plaform and get video id from url*/
        $detected = $this->detectURL($url);

        /* Init needed parser */
        switch ($detected['platform']) {
            case 'youtube':
                $parser = new YouTube();
                if (!empty($this->params['youtube_api_key'])) $parser->setApiKey($this->params['youtube_api_key']);
                if (!empty($this->params['youtube_api_disabled'])) $parser->disableAPI($this->params['youtube_api_disabled']);
                break;
            case 'liveleak':
                $parser = new LiveLeak();
                break;
            case 'dailymotion':
                $parser = new Dailymotion();
                if (!empty($this->params['dailymotion_api_disabled'])) $parser->disableAPI($this->params['dailymotion_api_disabled']);
                break;
            case 'facebook':
                $parser = new Facebook();
                break;
            case 'twitter':
                $parser = new Twitter();
                if (!empty($this->params['twitter_api_bearer_token'])) $parser->setApiKey($this->params['twitter_api_bearer_token']);
                if (!empty($this->params['twitter_api_disabled'])) $parser->disableAPI($this->params['twitter_api_disabled']);
                break;
            case 'vimeo':
                $parser = new Vimeo();
                if (!empty($this->params['vimeo_api_disabled'])) $parser->disableAPI($this->params['vimeo_api_disabled']);
                break;
            case 'cda':
                $parser = new CDA();
                break;
            case 'streamable':
                $parser = new Streamable();
                if (!empty($this->params['streamable_api_disabled'])) $parser->disableAPI($this->params['streamable_api_disabled']);
                break;
            default:
                throw new Exception('Platform or video not detected in given URL');
        }

        /* Return info from parser */
        $info = $parser->getVideoInfo($detected['id']);

        /* If there is not tags then generate own */
        if (empty($info['tags']) && !empty($info['title'])) {
            $info['tags'] = explode(' ', $info['title']);
            $info['tags'] = preg_replace('/([^\w ]|_)/u', '', $info['tags']);
            $info['tags'] = array_map('trim', $info['tags']);
            $info['tags'] = array_filter($info['tags'], function($v) { return (strlen($v) > 2) && (strlen($v) < 15); });
            $info['tags'] = array_unique($info['tags']);
        }

        /* Return results */
        return [
            'id' => $detected['id'],
            'platform' => $detected['platform'],
            'title' => (!empty($info['title'])) ? $info['title'] : '',
            'description' => (!empty($info['description'])) ? $info['description'] : '',
            'thumbnail' => (!empty($info['thumbnail'])) ? $info['thumbnail'] : '',
            'tags' => (!empty($info['tags'])) ? $info['tags'] : [],
            'duration' => (! empty($info['duration'])) ? intval($info['duration']) : 0,
            'api' => (!empty($info['api'])) ? $info['api'] : false,
        ];
    }


    /**
     * Check if URL is supported and return platform and video id
     *
     * @param $url
     * @return array|false
     */
    public function detectURL($url)
    {
        if (empty($url)) return false;

        /* Parse URL */
        $parsed_url = parse_url($url);
        $parsed_url['host'] = str_replace('www.', '', $parsed_url['host']);
        parse_str(!empty($parsed_url['query']) ? $parsed_url['query'] : '', $params_url);

        /* Detect site and parse video ID */
        if (strpos($parsed_url['host'], 'youtube.com') !== false) {
            if (empty($params_url['v'])) return false;

            return [
                'platform' => 'youtube',
                'id' => $params_url['v']
            ];
        } else if (strpos($parsed_url['host'], 'youtu.be') !== false) {
            if (empty(explode('/', $parsed_url['path'])[1])) return false;

            return [
                'platform' => 'youtube',
                'id' => explode('/', $parsed_url['path'])[1]
            ];
        } else if (strpos($parsed_url['host'], 'liveleak.com') !== false) {
            if (empty($params_url['t'])) return false;

            return [
                'platform' => 'liveleak',
                'id' => $params_url['t']
            ];
        } else if (strpos($parsed_url['host'], 'dailymotion.com') !== false) {
            if (strpos($parsed_url['path'], '/video/') === false) return false;
            if (empty(explode('/', $parsed_url['path'])[2])) return false;

            return [
                'platform' => 'dailymotion',
                'id' => explode('/', $parsed_url['path'])[2]
            ];
        } else if (strpos($parsed_url['host'], 'facebook.com') !== false) {
            if (strpos($parsed_url['path'], '/videos/') !== false) { // #1 type link
                if (empty($parsed_url['path'])) return false;
                if (empty(explode('/', $parsed_url['path'])[3])) return false;
                $path = array_filter(explode('/', $parsed_url['path']));

                return [
                    'platform' => 'facebook',
                    'id' => end($path)
                ];
            } else if (strpos($parsed_url['path'], '/watch/') !== false) { // #2 type link
                if (empty($params_url['v'])) return false;

                return [
                    'platform' => 'facebook',
                    'id' => $params_url['v']
                ];
            }
        } else if (strpos($parsed_url['host'], 'twitter.com') !== false) {
            if (empty($parsed_url['path'])) return false;
            $path = explode('/', $parsed_url['path']);
            if (!is_numeric(end($path))) return false;

            return [
                'platform' => 'twitter',
                'id' => end($path)
            ];
        } else if (strpos($parsed_url['host'], 'vimeo.com') !== false) {
            if (empty($parsed_url['path'])) return false;
            $path = explode('/', $parsed_url['path']);
            if (!is_numeric(end($path))) return false;

            return [
                'platform' => 'vimeo',
                'id' => end($path)
            ];
        } else if (strpos($parsed_url['host'], 'cda.pl') !== false) {
            if (strpos($parsed_url['path'], '/video/') === false) return false;
            if (empty(explode('/', $parsed_url['path'])[2])) return false;

            return [
                'platform' => 'cda',
                'id' => explode('/', $parsed_url['path'])[2]
            ];
        } else if (strpos($parsed_url['host'], 'streamable.com') !== false) {
            if (empty(explode('/', $parsed_url['path'])[1])) return false;

            return [
                'platform' => 'streamable',
                'id' => explode('/', $parsed_url['path'])[1]
            ];
        }


        return false;
    }


    /**
     * Method for API/parsers calls
     *
     * @param string $url
     * @param array $headers
     * @return false|string
     */
    public static function HTTPGet($url, $headers = [])
    {
        $headers = array_merge([
            'Accept-language' => 'en-US,en;q=0.5',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'User-Agent' => 'facebot' // simulate bot to make sure that site will provide :og meta
        ], $headers);

        array_walk($headers, function (&$value, $key) {
            $value = $key . ': ' . $value;
        });
        $headers = array_values($headers);

        $context = stream_context_create([
            'http' => [
                'header' => $headers
            ]
        ]);

        return file_get_contents($url, false, $context);
    }
}
