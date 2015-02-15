<?php

namespace App;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ApplicationLogger
{
    public static function logAccess($filename)
    {
        $timestamp = empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'];

        if (empty($_SERVER['REQUEST_METHOD']) || empty($_SERVER['REQUEST_URI']) || empty($_SERVER['SERVER_PROTOCOL'])) {
            $requestLine = '-';
        } else {
            $requestLine = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'];
        }

        file_put_contents(
            $filename,
            sprintf(
                "%s %s %s [%s] \"%s\" %s %s \"%s\" \"%s\"\n",
                empty($_SERVER['REMOTE_ADDR']) ? '-' : $_SERVER['REMOTE_ADDR'],
                empty($_SERVER['HTTP_HOST']) ? '-' : $_SERVER['HTTP_HOST'],
                '-',
                date('d/M/Y:H:i:s O', $timestamp),
                self::escapeString($requestLine),
                http_response_code(),
                0,
                empty($_SERVER['HTTP_REFERER']) ? '-' : self::escapeString($_SERVER['HTTP_REFERER']),
                empty($_SERVER['HTTP_USER_AGENT']) ? '-' : self::escapeString($_SERVER['HTTP_USER_AGENT'])
            ),
            FILE_APPEND + LOCK_EX
        );
    }

    private static function escapeString($string)
    {
        $string = strtr($string, ["\r" => '\r', "\n" => '\n', "\t" => '\t', '"' => '\"', '\\' => '\\\\']);
        return preg_replace_callback('/[\x00-\x1F\x7F-\xFF]/', function ($match) {
            return '\x' . sprintf('%02x', ord($match[0]));
        }, $string);
    }

    public static function logException(\Exception $exception, $filename)
    {
        file_put_contents($filename, sprintf(
            "[%s] Logged exception '%s' with message '%s' in %s (%d)\n\nStack Trace:\n%s\n\n",
            date('r'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        ), FILE_APPEND | LOCK_EX);
    }

    public static function logError($error, $filename)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

        file_put_contents($filename, sprintf(
            "[%s] Logged error '%s' in %s (%d)\n",
            date('r'),
            $error,
            $trace[0]['file'],
            $trace[0]['line']
        ), FILE_APPEND | LOCK_EX);
    }
}
