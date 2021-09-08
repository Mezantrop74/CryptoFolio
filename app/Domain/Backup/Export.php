<?php

namespace App\Domain\Backup;

use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Storage;

class Export
{
    /**
     * The database connection data.
     *
     * @var array
     */
    protected $connection;

    private $driver = 'mysql';

    /**
     * The path to mysql dump.
     *
     * @var string
     */
    protected $mysqldumpPath;

    /**
     * Dump file name.
     *
     * @var string
     */
    protected $filename;

    /**
     * Local disk where backups will be stored.
     *
     * @var string
     */
    protected $localDisk;

    /**
     * Local path where the backups will be stored.
     *
     * @var array
     */
    protected $localPath;

    /**
     * Determine if backup will be cloud synced.
     *
     * @var bool
     */
    protected $cloudSync;

    /**
     * Cloud disk name.
     *
     * @var string
     */
    protected $cloudDisk;

    /**
     * Cloud path where the backups will be stored.
     *
     * @var array
     */
    protected $cloudPath;

    /**
     * The path where backups will be stored.
     *
     * @var array
     */
    protected $keepLocal;

    /**
     * Determine if the file will be compressed.
     *
     * @var array
     */
    protected $isCompressionEnabled = false;

    public function __construct()
    {
        $this->mysqldumpPath = config('backup.mysql.mysqldump_path', 'mysqldump');

        $this->localDisk = config('backup.mysql.local-storage.disk', 'local');
        $this->localPath = config('backup.mysql.local-storage.path');
        $this->cloudSync = config('backup.mysql.cloud-storage.enabled', false);
        $this->cloudDisk = config('backup.mysql.cloud-storage.disk');
        $this->cloudPath = config('backup.mysql.cloud-storage.path');
        $this->keepLocal = config('backup.mysql.cloud-storage.keep-local', true);
    }


    public function handle()
    {
        $this->handleOptions();
        return $this->dumpDatabase();
    }

    protected function handleOptions()
    {
        $this->validateAndSetConnection($this->driver);

        $this->isCompressionEnabled = config('backup.mysql.compress', false);


        $this->setFilename();
    }

    protected function validateAndSetConnection($connection)
    {
        if (is_array($connectionData = config("database.connections.{$connection}"))) {
            if ($connectionData['driver'] == 'mysql') {
                $this->connection = [
                    'host' => $connectionData['host'],
                    'database' => $connectionData['database'],
                    'port' => $connectionData['port'],
                    'username' => $connectionData['username'],
                    'password' => $connectionData['password'],
                ];
            } else {
                throw new Error("Connection '{$connection}' should use MySQL driver!");
            }
        } else {
            throw new Error("Connection '{$connection}' does not exists!");
        }
    }

    protected function setFilename($filename = null)
    {
        if (empty($filename)) {
            $filename = $this->connection['database'] . '_' . Carbon::now()->format('YmdHis');
        }
        $filename = explode('.', $filename)[0];
        $this->filename = $filename . '.sql' . ($this->isCompressionEnabled ? '.gz' : '');
    }

    protected function getFilePath()
    {
        $localPath = $this->cleanPath($this->localPath);

        return $localPath . DIRECTORY_SEPARATOR . $this->filename;
    }

    protected function getFileCloudPath()
    {
        $cloudPath = $this->cleanPath($this->cloudPath);

        return $cloudPath . DIRECTORY_SEPARATOR . $this->filename;
    }

    protected function isPathAbsolute($path)
    {
        return starts_with($path, DIRECTORY_SEPARATOR);
    }

    protected function cleanPath($path)
    {
        return ltrim(rtrim($path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }

    protected function storeDumpFile($data)
    {
        if ($this->keepLocal) {
            Storage::disk($this->localDisk)->put($this->getFilePath(), $data);
        }
        if ($this->cloudSync) {
            Storage::disk($this->cloudDisk)->put($this->getFileCloudPath(), $data);
        }

        return true;
    }

    protected function dumpDatabase()
    {
        $hostname = escapeshellarg($this->connection['host']);
        $port = $this->connection['port'];
        $database = $this->connection['database'];
        $username = escapeshellarg($this->connection['username']);
        $password = $this->connection['password'];

        $databaseArg = escapeshellarg($database);
        $portArg = !empty($port) ? '-P ' . escapeshellarg($port) : '';
        $passwordArg = !empty($password) ? '-p' . escapeshellarg($password) : '';

        $dumpCommand = "{$this->mysqldumpPath} -C -h {$hostname} {$portArg} -u{$username} {$passwordArg} --single-transaction --skip-lock-tables --quick {$databaseArg}";
        exec($dumpCommand, $dumpResult, $result);
        if ($result == 0) {
            $dumpResult = implode(PHP_EOL, $dumpResult);
            $dumpResult = $this->isCompressionEnabled ? gzencode($dumpResult, 9) : $dumpResult;
            $this->storeDumpFile($dumpResult);
            return $this->getFilePath();
        }
        return null;
    }
}
