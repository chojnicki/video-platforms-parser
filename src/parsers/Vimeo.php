<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;
use Exception;

class Vimeo
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
        $url = 'https://vimeo.com/api/oembed.json?url=https%3A%2F%2Fvimeo.com%2F' . $id;

        /* Make call to API */
        $response = VideoPlatformsParser::HTTPGet($url);
        $json = json_decode($response, true);

        $json['thumbnail_url'] = str_replace('_295x166', '', $json['thumbnail_url']);

        return [
            'id' => $id,
            'title' => $json['title'],
            'description' => $json['description'],
            'thumbnail' => $json['thumbnail_url'],
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
        $url = 'https://vimeo.com/' . $id;

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
                $return['title'] = str_replace(' - video dailymotion', '', $return['title']);
            } else if ($meta->getAttribute('property') == 'og:description') {
                $return['description'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:image') {
                $return['thumbnail'] = $meta->getAttribute('content');
                if ($return['thumbnail']) {
                    $parsed_url = parse_url($return['thumbnail']);
                    parse_str(!empty($parsed_url['query']) ? $parsed_url['query'] : '', $params_url);
                    if (!empty($params_url['src0'])) $return['thumbnail'] = $params_url['src0'];
                }
            } else if ($meta->getAttribute('name') == 'keywords') {
                $return['tags'] = $meta->getAttribute('content');
                $return['tags'] = explode(',', $return['tags']);
                $return['tags'] = array_map('trim', $return['tags']); // remove spaces
            }
        }

        return $return;
    }

}
