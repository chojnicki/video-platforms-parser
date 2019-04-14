<?php
namespace Chojnicki\VideoPlatformsParser\parsers;

use Chojnicki\VideoPlatformsParser\VideoPlatformsParser;
use DOMDocument;

class LiveLeak
{

    /**
     * Get CDA video page source and parse info
     *
     * @param string $id
     * @return array
     */
    public function getVideoInfo($id)
    {
        $url = 'https://www.liveleak.com/view?t=' . $id;

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
                $return['title'] = str_replace('Liveleak.com - ', '', $return['title']);
            } else if ($meta->getAttribute('property') == 'og:description') {
                $return['description'] = $meta->getAttribute('content');
            } else if ($meta->getAttribute('property') == 'og:image') {
                $return['thumbnail'] = $meta->getAttribute('content');
                $return['thumbnail'] = str_replace('_thumb.jpg', '_sf.jpg', $return['thumbnail']);
            }
        }

        if (preg_match_all('/<p><strong>Tags:<\/strong>(.*)<\/p>/', $response, $tags_preg)) {
            $return['tags'] = $tags_preg[1][0]; // get found part
            $return['tags'] = explode(',', $return['tags']); // make array
            $return['tags'] = array_map('trim', $return['tags']); // remove spaces
        }

        return $return;
    }




}
