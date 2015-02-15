<?='<?xml version="1.0" encoding="UTF-8" ?>'?>


<rss version="2.0" xmlns:psf="<?=$link->url('/')?>doctype.dtd" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
 <title>Downloads history for <?=$escape($name)?></title>
 <description>History of new downloads and stars in Packagist for the packages of <?=$escape($name)?></description>
 <atom:link href="<?=$link->self(true);?>" rel="self" type="application/rss+xml" />
 <link><?=$link->url('/')?></link>
 <lastBuildDate><?=$date->format('r')?></lastBuildDate>
 <pubDate><?=$date->format('r')?></pubDate>
 <ttl>60</ttl>

<?php

$formatDelta = function ($value) {
    $sign = $value < 0 ? '-' : '+';
    return $value == 0 ? '' : sprintf(' (%s%s)', $sign, number_format($value));
};

foreach($entries as $entry) {
    $package = $entry->getPackage();
    $downloads = $formatDelta($entry->getNewDownloads());
    $stars = $formatDelta($entry->getNewStars());

    if ($entry->getNewDownloads() === null) {
        $title = 'New package';
    } else {
        $titles = [];

        if ($downloads != '') {
            $titles[] = sprintf('%s%s downloads', number_format($entry->getDownloads()), $downloads);
        }
        if ($stars != '') {
            $titles[] = sprintf('%s%s stars', number_format($entry->getStars()), $stars);
        }

        $title = implode(', ', $titles);
    }
?>
 <item>
  <title><?=$escape($package->getName())?>: <?=$escape($title)?></title>
  <description>
   <?=$escape($package->getDescription())?>&lt;br /&gt;&lt;br /&gt;
   Downloads: <?=number_format($entry->getDownloads())?><?=$downloads?>&lt;br /&gt;
   Stars: <?=number_format($entry->getStars())?>

  </description>
  <psf:downloads><?=(int) $entry->getDownloads()?></psf:downloads>
  <psf:new_downloads><?=(int) $entry->getNewDownloads()?></psf:new_downloads>
  <psf:stars><?=(int) $entry->getStars()?></psf:stars>
  <psf:new_stars><?=(int) $entry->getNewStars()?></psf:new_stars>
  <link><?=$escape($packagistUrl . $package->getUrl())?></link>
  <guid isPermaLink="false"><?=$escape($package->getName()) . ':' . $entry->getDate()->getTimestamp()?></guid>
  <pubDate><?=$entry->getDate()->format('r')?></pubDate>
 </item>
<?php } ?>

</channel>
</rss>
