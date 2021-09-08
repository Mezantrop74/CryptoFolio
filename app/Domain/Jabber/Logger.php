<?php


namespace App\Domain\Jabber;


use Exception;
use Norgul\Xmpp\Loggers\Loggable;

class Logger implements Loggable
{
    /**
     * @var false|resource
     */
    private $log;
    /**
     * @var string
     */
    private $logFolder;
    /**
     *
     */
    const LOG_FILE = "xmpp.log";

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->logFolder = storage_path('logs');
        $this->createLogFile();
        if (config("services.jabber.log_enabled") == true) {
            $this->log = fopen($this->logFolder . DIRECTORY_SEPARATOR . self::LOG_FILE, 'a');
        }
    }

    /**
     *
     */
    protected function createLogFile(): void
    {
        if (config("services.jabber.log_enabled") == true && !file_exists($this->logFolder)) {
            mkdir($this->logFolder, 0777, true);
        }
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        $this->writeToLog($message);
    }

    /**
     * @param $message
     */
    public function logRequest($message)
    {
        $this->writeToLog($message, "REQUEST");
    }

    /**
     * @param $message
     */
    public function logResponse($message)
    {
        $this->writeToLog($message, "RESPONSE");
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        $this->writeToLog($message, "ERROR");
    }

    /**
     * @param $message
     * @param string $type
     */
    protected function writeToLog($message, $type = ''): void
    {
        if (config('services.jabber.log_enabled') == false) {
            return;
        }
        $prefix = date("Y.m.d H:m:s") . " " . session_id() . ($type ? " {$type}::" : " ");
        $this->writeToFile($this->log, $prefix . "$message\n");
    }

    /**
     * @param $file
     * @param $message
     */
    protected function writeToFile($file, $message)
    {
        try {
            fwrite($file, $message);
        } catch (Exception $e) {
            // silent fail
        }
    }

    /**
     * @param $resource
     * @return string
     */
    public function getFilePathFromResource($resource): string
    {
        $metaData = stream_get_meta_data($resource);
        return $metaData["uri"];
    }
}
