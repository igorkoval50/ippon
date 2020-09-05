<?php
/**
 * @copyright  Copyright (c) 2019, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiLanguageDetector\Service;


/**
 * Class UrlHelper
 *
 * @package NetiLanguageDetector\Service
 */
class UrlHelper
{
    /**
     * @param string   $path  - path of the request uri
     * @param string   $query - query/search of the request uri (= GET parameters)
     * @param string[] $remove - query parts (= GET parameters) to be removed
     * @param string[] $add - query parts (= GET parameters) to be added
     *
     * @return string - new request uri including path and query/search
     */
    public function modifyRequestUri($path, $query, $remove = [], $add = [])
    {
        // Remove the leading question mark
        if (0 === strpos($query, '?')) {
            $query = \substr($query, 1);
        }

        \parse_str($query, $queryParts);

        foreach ($remove as $key) {
            if (isset($queryParts[$key])) {
                unset($queryParts[$key]);
            }
        }

        foreach ($add as $key => $value) {
            $queryParts[$key] = $value;
        }

        return $this->buildUri($path, $queryParts);
    }

    private function buildUri($path, $queryParts)
    {
        if (0 < \count($queryParts)) {
            $query = http_build_query($queryParts);

            return $path . '?' . $query;
        }

        return $path;
    }
}
