<?php

namespace App\Feed;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ParsedPackage
{
    private $name;
    private $description;
    private $downloads;
    private $stars;
    private $url;

    public function __construct($name, $description, $downloads, $stars, $url)
    {
        $this->name = (string) $name;
        $this->description = $description === null ? null : (string) $description;
        $this->downloads = (int) $downloads;
        $this->stars = (int) $stars;
        $this->url = (string) $url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDownloads()
    {
        return $this->downloads;
    }

    public function getStars()
    {
        return $this->stars;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
