<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;
use Exception;

class Twitter
{
    protected $api_key; // api key
    protected $api_disabled = false; // not using api


    /**
     * Set bearer token from twitter developer account for connection with Twitter API
     *
     * @param string $api
     */
    public function setApiKey($api)
    {
        $this->api_key = $api;
    }


    /**
     * Disable API - use standard parser instead (not recomended)
     *
     * @param bool $value
     */
    public function disableAPI($value = true)
    {
        $this->api_disabled = $value;
    }


    /**
     * Get info with or without api
     *
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function getVideoInfo($id)
    {
        if (!$this->api_disabled) {
            return $this->getVideoInfoWithAPI($id);
        } else {
            return $this->getVideoInfoWithoutAPI($id);
        }
    }



    /**
     * Use official API for video info (require API key) - fast and reliable
     *
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function getVideoInfoWithAPI($id)
    {
        if (empty($this->api_key)) {
            throw new Exception('Twitter API Bearer token was not set');
        }

        $url = 'https://api.twitter.com/1.1/statuses/show.json?id=' . $id . '&tweet_mode=extended';

        /* Make call to API */
        $response = VideoPlatformsParser::HTTPGet($url, ['Authorization' => 'Bearer ' . $this->api_key]);
        $json = json_decode($response, true);

        if (empty($json['extended_entities']['media'][0])) throw new Exception('Video not found for given ID in API json response');
        $media = $json['extended_entities']['media'][0];
        $description = ! empty($json['full_text']) ? $json['full_text'] : '';
        $title = strtok($description, PHP_EOL);
        $title = preg_replace('/[^ -\x{2122}]\s+|\s*[^ -\x{2122}]/u','', $title);

        if (! empty($json['entities']['hashtags'])) {
            $tags = [];
            array_map(function ($item) use ($tags) {
                $tags[] = $item['text'];
            }, $json['entities']['hashtags']);
        }

        return [
            'id' => $id,
            'title' => ! empty($title) ? $title : null,
            'description' => ! empty($description) ? $description : null,
            'thumbnail' => ! empty($media['media_url_https']) ? $media['media_url_https'] : null,
            'tags' => ! empty($tags) ? $tags : null,
            'duration' => ! empty($media['video_info']['duration_millis']) ? $media['video_info']['duration_millis']/1000 : null,
            'api' => true
        ];
    }



    /**
     * Parse video page without API - slower and less reliable
     *
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function getVideoInfoWithoutAPI($id)
    {
        $url = 'https://twitter.com/x/status/' . $id;

        /* Grab video page */
        $response = VideoPlatformsParser::HTTPGet($url);

        /* Make HTML DOM from response */
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // ignore errors in html on website
        $dom->loadHTML($response);

        /* Parse data */
        $return = [];
        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++) { // check all meta tags
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
                $return['title'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:description') {
                $return['description'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:image') {
                $return['thumbnail'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('name') == 'keywords') {
                $return['tags'] = $meta->getAttribute('content');
                $return['tags'] = explode(',', $return['tags']);
                $return['tags'] = array_map('trim', $return['tags']); // remove spaces
            }
        }

        return $return;
    }
}
