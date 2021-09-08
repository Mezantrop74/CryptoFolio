<?php


namespace App\Domain\Backup;


use Illuminate\Support\Facades\Storage;

class Import
{

    /**
     * The database connection data.
     *
     * @var array
     */
    protected $connection;

    /**
     * The path to mysql dump.
     *
     * @var string
     */
    protected $mysqlPath;

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
     * Determine if backup will be restored from cloud.
     *
     * @var bool
     */
    protected $cloudRestoration;

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
     * Determinate if options will display all files.
     *
     * @var bool
     */
    protected $displayAllBackupFiles;

    /**
     * Determinate if latest backup will be restored.
     *
     * @var bool
     */
    protected $restoreLatestBackup;

    /**
     * Confirms restoration without asking.
     *
     * @var bool
     */
    protected $confirmRestoration;

    public function __construct()
    {
        $this->mysqlPath = config('backup.mysql.mysql_path', 'mysql');

        $this->connection = [
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
            'port' => config('database.connections.mysql.port'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];

        $this->localDisk = config('backup.mysql.local-storage.disk', 'local');
        $this->localPath = config('backup.mysql.local-storage.path');
        $this->cloudDisk = config('backup.mysql.cloud-storage.disk');
        $this->cloudPath = config('backup.mysql.cloud-storage.path');
    }

    /**
     * Execute the console command.
     *
     * @param string $filename
     * @return bool
     * @return bool
     */
    public function handle(string $filename)
    {
        $this->filename = $filename;
        return $this->restoreDatabase();
    }

    protected function getDisk()
    {
        return $this->cloudRestoration ? $this->cloudDisk : $this->localDisk;
    }

    protected function getFilePath($filename = null, $path = null)
    {
        $path = $this->cleanPath(is_null($path) ? $this->cloudRestoration ? $this->cloudPath : $this->localPath : $path);

        return $path . DIRECTORY_SEPARATOR . (is_null($filename) ? $this->filename : $filename);
    }

    protected function getAbsFilePath($filename, $disk = null, $path = null)
    {
        $path = $this->cleanPath($path);

        return Storage::disk(is_null($disk) ? $this->getDisk() : $disk)->getAdapter()->getPathPrefix() . $path . DIRECTORY_SEPARATOR . $filename;
    }

    protected function cleanPath($path)
    {
        return ltrim(rtrim($path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }

    protected function sanitizeFile($file)
    {
        $path = $this->cleanPath($this->cloudRestoration ? $this->cloudPath : $this->localPath);

        return str_replace($path . DIRECTORY_SEPARATOR, '', $file);
    }


    protected function backupFileExists()
    {
        return Storage::disk($this->getDisk())->has($this->getFilePath());
    }


    protected function restoreDatabase()
    {
        $hostname = escapeshellarg($this->connection['host']);
        $port = $this->connection['port'];
        $database = $this->connection['database'];
        $username = escapeshellarg($this->connection['username']);
        $password = $this->connection['password'];

        $databaseArg = escapeshellarg($database);
        $portArg = !empty($port) ? '-P ' . escapeshellarg($port) : '';
        $passwordArg = !empty($password) ? '-p' . escapeshellarg($password) : '';

        $localFilename = $this->filename;

        $restoreCommand = "{$this->mysqlPath} -h {$hostname} {$portArg} -u{$username} {$passwordArg} {$databaseArg} < " . sprintf('"%s"', $this->filename);
        exec($restoreCommand, $restoreResult, $result);
        return $result == 0;
    }

}
