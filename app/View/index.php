<h1>Packagist Stats Feed</h1>

<p><em>Packagist Stats Feed</em> is a simple web application that generates RSS
feeds to track the download stats for <a href="https://packagist.org">Packagist</a> packages.
This makes it easier to track the popularity of your packages by getting frequently
updated statistics on how many times the packages have been downloaded.</p>

<p>To get the RSS feeds that lists the statistics for a specific user, use the
URL below, except replace the <code>USERNAME</code> with your username.</p>

<p><code><?=$link->url('/feed/USERNAME/')?></code></p>

<p>Note that RSS feed are only updated when they are requested. Thus, the statistics
update frequency is based on how often the feed itself is requested. It will
only update once per hour at most, however.</p>

<p>When the package is seen for the first time, the feed item will be titled
"New package", but after that it will detail the changed statistics.</p>
