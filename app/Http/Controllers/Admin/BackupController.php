<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Backup\Export;
use App\Domain\Backup\Import;
use App\Http\Controllers\Controller;
use Backup\Console\Commands\MysqlDump;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;

class BackupController extends Controller
{
    public function export(Request $request)
    {
        $export = (new Export())->handle();
        if ($export != null) {
            return response()->download(storage_path('app' . DIRECTORY_SEPARATOR . $export));
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file' => 'required|file|mimetypes:text/plain'
        ]);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);

        $backupFile = $request->file('import_file');
        $name = Str::random() . '.sql';
        $path = $backupFile->storeAs(config('backup.mysql.local-storage.path') . DIRECTORY_SEPARATOR . 'tmp', $name);
        $import = (new Import())->handle(Storage::disk('local')->path($path));
        Storage::delete($path);
        if ($import) {
            return redirect()->back()->with('success', __('Import was successfully finished.'));
        }
        return redirect()->back()->with('error', __('Import error.'));
    }
}
