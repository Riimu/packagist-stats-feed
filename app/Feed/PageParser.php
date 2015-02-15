<?php

namespace App\Feed;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PageParser
{
    public function parseFile($file)
    {
        return $this->parse(file_get_contents($file));
    }

    public function parse($html)
    {
        preg_match('#<h1[^>]*>([^>]*)</h1>#i', $html, $match);
        $user = $match[1];

        preg_match('#<ul[^>]*class="packages"[^>]*>(([^<]+|<(?!/ul))*+)</ul>#is', $html, $match);
        preg_match_all('#<li[^>]*>(([^<]+|<(?!/li))*+)</li>#i', $match[1], $matches);

        foreach ($matches[1] as $entry) {
            preg_match('#<i[^>]*class="icon-download"[^>]*>[^<]*</i>([^<]*)#i', $entry, $match);
            $downloads = (int) preg_replace('/[^\d]/', '', $match[1]);

            preg_match('#<i[^>]*class="icon-star"[^>]*>[^<]*</i>([^<]*)#i', $entry, $match);
            $stars = (int) preg_replace('/[^\d]/', '', $match[1]);

            preg_match('#<a[^>]*href="([^"]*)"[^>]*>([^<]*)#i', $entry, $match);
            $url = $match[1];
            $name = $match[2];

            if (preg_match('#<p[^>]*class="package-description"[^>]*>([^<]*)#i', $entry, $match)) {
                $description = $match[1];
            } else {
                $description = null;
            }

            $packages[] = new ParsedPackage($name, $description, $downloads, $stars, $url);
        }

        return [$user, $packages];
    }
}
