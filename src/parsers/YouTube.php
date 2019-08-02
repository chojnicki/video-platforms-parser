<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;
use Exception;

class YouTube
{
    protected $api_key; // api key
    protected $api_disabled = false; // not using api


    /**
     * Set key from googleapis for connection with YT API
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
            throw new Exception('YouTube API was not set');
        }

        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $id . '&part=snippet&key=' . $this->api_key;

        /* Make call to API */
        $response = VideoPlatformsParser::HTTPGet($url);
        $json = json_decode($response, true);

        if (empty($json['items'][0]['snippet'])) throw new Exception('Video not found for given ID in API json response');

        $json = $json['items'][0]['snippet'];
        return [
            'id' => $id,
            'title' => $json['title'],
            'description' => ! empty($json['description']) ? $json['description'] : null,
            'thumbnail' => end($json['thumbnails'])['url'],
            'tags' => ! empty($json['tags']) ? $json['tags'] : null,
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
        $url = 'https://www.youtube.com/watch?v=' . $id;

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
