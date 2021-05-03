<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;

class Facebook
{

    /**
     * Get FB video page source and parse info
     *
     * @param string $id
     * @return array
     */
    public function getVideoInfo($id)
    {
        $url = 'https://facebook.com/watch/?v=' . $id;

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
            if ($meta->getAttribute('name') == 'description') {
                $return['description'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:image') {
                $return['thumbnail'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:title') {
                $return['title'] = $meta->getAttribute('content');
            }
        }

        if (empty($return['title'])) {
            $return['title'] = $dom->getElementsByTagName('title')->item(0)->textContent;
        }
        if (empty($return['description'])) {
            $return['description'] = $return['title'];
        }

        return $return;
    }

}
