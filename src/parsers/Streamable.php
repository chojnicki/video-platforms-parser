<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;
use Exception;

class Streamable
{
    protected $api_disabled = false; // not using api


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
        $url = 'https://api.streamable.com/videos/' . $id;

        /* Make call to API */
        $response = VideoPlatformsParser::HTTPGet($url);
        $json = json_decode($response, true);

        if (empty($json['status'])) throw new Exception('Video not found for given ID in API json response');

        return [
            'id' => $id,
            'title' => $json['title'],
            'description' => ! empty($json['description']) ? $json['description'] : null,
            'thumbnail' => ! empty($json['thumbnail_url']) ? 'https:' . strtok($json['thumbnail_url'], '?') : null,
            'duration' => ! empty($json['files']['mp4']['duration']) ? $json['files']['mp4']['duration'] : null,
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
        $url = 'https://streamable.com/' . $id;

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
                $return['title'] = str_replace(' - Streamable', '', $return['title']);
            } else if ($meta->getAttribute('property') == 'og:image') {
                $return['thumbnail'] = $meta->getAttribute('content');
                $return['thumbnail'] = strtok($return['thumbnail'], '?');
            }
        }

        preg_match('/, "duration": (\w+)/', $response, $duration);
        if (! empty($duration[1])) $return['duration'] = $duration[1];

        return $return;
    }
}
