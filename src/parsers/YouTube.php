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

        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $id . '&part=snippet,contentDetails&key=' . $this->api_key;

        /* Make call to API */
        $response = VideoPlatformsParser::HTTPGet($url);
        $json = json_decode($response, true);

        if (!count($json['items'])) throw new Exception('Video not found for given ID in API json response');

        $json = $json['items'][0];
        return [
            'id' => $id,
            'title' => $json['snippet']['title'],
            'description' => ! empty($json['snippet']['description']) ? $json['snippet']['description'] : null,
            'thumbnail' => end($json['snippet']['thumbnails'])['url'],
            'tags' => ! empty($json['snippet']['tags']) ? $json['snippet']['tags'] : null,
            'duration' => self::ISO8601ToSeconds($json['contentDetails']['duration']),
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
        preg_match('/itemprop="duration" content="(\w+)">/', $response, $duration);
        if (! empty($duration[1])) $return['duration'] = self::ISO8601ToSeconds($duration[1]);

        return $return;
    }


    /**
     * Convert ISO 8601 values like P2DT15M33S to a total value of seconds.
     * credits to RuudBurger
     *
     * @param string $ISO8601
     * @return int
     */
    private static function ISO8601ToSeconds($ISO8601) {
        try {
            $interval = new \DateInterval($ISO8601);
        } catch (Exception $e) {
            return 0;
        }

        return ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }
}
