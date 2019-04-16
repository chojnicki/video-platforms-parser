<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;

class CDA
{

    /**
     * Get CDA video page source and parse info
     *
     * @param string $id
     * @return array
     */
    public function getVideoInfo($id)
    {
        $url = 'https://www.cda.pl/video/' . $id;

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
                if (strpos($return['thumbnail'], 'http') === false) {
                    $return['thumbnail'] = 'https:' . $return['thumbnail'];
                }
            } else if ($meta->getAttribute('name') == 'keywords') {
                $return['tags'] = $meta->getAttribute('content');
                $return['tags'] = explode(',', $return['tags']);
                $return['tags'] = array_slice($return['tags'], 2); // remove first two tags (video, filmik)
            }
        }

        return $return;
    }
}
