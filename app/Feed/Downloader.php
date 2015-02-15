<?php

namespace App\Feed;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Downloader
{
    protected $userAgent = 'Riimu\'s Packagist Stats Feed (http://feed.riimu.net/packagist/info/)';

    /** @var \PDO PDO instance used to access the curl cache */
    private $pdo;

    private $logDirectory;

    public function __construct($logDirectory)
    {
        $this->logDirectory = $logDirectory;
    }

    public function updateFile($url, $file)
    {
        $entry = $this->getCacheEntry($file);
        $tmp = tmpfile();
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_FILE => $tmp,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => $this->userAgent
        ]);

        if ($entry && $entry['url'] === $url) {
            $this->setCacheHeaders($curl, $entry);
        }

        $this->downloadFile($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = false;

        if ($code === 200) {
            rewind($tmp);
            $headers = fread($tmp, curl_getinfo($curl, CURLINFO_HEADER_SIZE));

            $target = fopen($file, 'w');
            stream_copy_to_stream($tmp, $target);
            fclose($target);

            $this->saveCacheEntry($file, $url, $headers);
            $result = true;
        } elseif ($code !== 304) {
            throw new DownloadErrorException(
                sprintf('Error downloading "%s", got response: %s', $url, $code),
                $code
            );
        }

        curl_close($curl);
        fclose($tmp);

        return $result;
    }

    private function downloadFile($curl)
    {
        curl_exec($curl);

        if (curl_errno($curl) !== 0) {
            throw new CurlErrorException('Curl Error: ' . curl_error($curl));
        }

        $download = curl_getinfo($curl, CURLINFO_HEADER_SIZE) + curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
        $upload = curl_getinfo($curl, CURLINFO_REQUEST_SIZE);

        $stmt = $this->pdo->prepare('
INSERT OR REPLACE INTO bandwith (date, download, upload, requests)
SELECT ?, COALESCE(download, 0) + ?, COALESCE(upload, 0) + ?, COALESCE(requests, 0) + ?
FROM (SELECT ? AS date) AS a LEFT JOIN bandwith as b USING (date)
        ');
        $stmt->execute([date('Y-m-d'), $download, $upload, 1, date('Y-m-d')]);

        file_put_contents(
            sprintf('%s/%s.log', $this->logDirectory, date('Y-m-d')),
            sprintf(
                '[%s] %d, %s ms, %s B / %s B, %s' . PHP_EOL,
                date('r'),
                curl_getinfo($curl, CURLINFO_HTTP_CODE),
                number_format(round(curl_getinfo($curl, CURLINFO_TOTAL_TIME) * 1000)),
                number_format($download),
                number_format($upload),
                curl_getinfo($curl, CURLINFO_EFFECTIVE_URL)
            ),
            FILE_APPEND | LOCK_EX
        );
    }

    private function setCacheHeaders($curl, $entry)
    {
        $headers = [];

        if (!empty($entry['lastModified'])) {
            $headers[] = 'If-Modified-Since: ' . $entry['lastModified'];
        }
        if (!empty($entry['eTag'])) {
            $headers[] = 'If-None-Match: ' . $entry['eTag'];
        }

        if ($headers !== []) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
    }

    private function getCacheEntry($file)
    {
        $cacheFile = dirname($file) . '/curl_cache.sqlite';
        $newCache = !file_exists($cacheFile);

        $this->pdo = new \PDO(sprintf('sqlite:%s/curl_cache.sqlite', dirname($file)));
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        if ($newCache) {
            $this->createCacheFile($cacheFile);
        }

        $stmt = $this->pdo->prepare('SELECT url, downloaded, lastModified, eTag FROM files WHERE name = ?');
        $stmt->execute([basename($file)]);

        return current($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    private function saveCacheEntry($file, $url, $headers)
    {
        $entry = [
            'name' => basename($file),
            'url' => $url,
            'downloaded' => date('Y-m-d H:i:s'),
            'lastModified' => null,
            'eTag' => null,
        ];

        if (preg_match('/^last-modified: ([^\r\n]*)/im', $headers, $match)) {
            $entry['lastModified'] = $match[1];
        }
        if (preg_match('/^etag: ([^\r\n]*)/im', $headers, $match)) {
            $entry['eTag'] = $match[1];
        }

        $stmt = $this->pdo->prepare(
            'INSERT OR REPLACE INTO files (name, url, downloaded, lastModified, eTag) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute(array_values($entry));
    }

    private function createCacheFile($file)
    {
        $this->pdo->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS files (
  name TEXT PRIMARY KEY,
  url TEXT,
  downloaded TEXT,
  lastModified TEXT,
  eTag TEXT
)
SQL
        );

        $this->pdo->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS bandwith (
  date TEXT PRIMARY KEY,
  download INTEGER,
  upload INTEGER,
  requests INTEGER
)
SQL
        );
    }
}
