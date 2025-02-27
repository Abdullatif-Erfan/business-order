<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use Morilog\Jalali\Jalalian;
use Spatie\Backup\Tasks\Backup\BackupDestinationFactory;
use Spatie\Backup\BackupDestination\BackupDestination;
use App\Models\Backup;
use Yajra\DataTables\Facades\DataTables;

use Exception;

class BackupController extends Controller
{
    /**
     * List all backups from the database.
     */
    public function index()
    {
        // $backups = Backup::latest()->get(); // Get all backups from DB
        // return view('backups.index', compact('backups'));
        return view('backups.list');
    }

    public function getData(Request $request)
    {
        $backups = Backup::orderBy('id', 'DESC')->get();
    
        return DataTables::of($backups)
            
            ->addIndexColumn()

            

            ->addColumn('times', function($backup) {
                return '<div style="direction:ltr;">'.\Carbon\Carbon::parse($backup->times)->diffForHumans().'</div>';
            })

            ->addColumn('restore', function($backup) {
                return '<i class="fas fa-recycle restoreIcon" data-id="'.$backup->id.'" style="font-size:20px;"></i>';
            })

            // ->addColumn('download', function($backup) {
            //     return '<a href="' . route("backups.download", $backup->id) . '">
            //                 <i class="fas fa-download" style="font-size:20px;"></i>
            //             </a>';
            // })
            ->addColumn('download', function($backup) {
                return '<a href="#" onclick="checkPassword(event, \'' . route("backups.download", $backup->id) . '\')">
                            <i class="fas fa-download" style="font-size:20px;"></i>
                        </a>';
            })

            ->addColumn('delete', function($backup) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$backup->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['delete','download','restore','times'])
            ->make(true);
    }

    public function createBackup(Request $request)
    {
        $filename = "db-" . date("Y-m-d_H-i-s") . ".sql";  
    
        // Define the backup file path (full path)
        $backupFilePath = storage_path('app/backups/' . $filename);


        // Convert absolute path to relative storage path (for database storage)
        $relativeStoragePath = "backups/{$filename}";
        // Get a public URL (if you want the file to be accessible via browser)
        $publicUrl = Storage::url($relativeStoragePath);
        // Store this in the database
        $filePathToStore = $publicUrl; // "/storage/backups/db-2025-02-26_16-57-46.sql


        $type = 'full';
        $label = $request->label ?? 'بک اپ دیتابیس';
        $dates = $datetime = Jalalian::now()->format('Y-m-d H:i:s');
        $times = time();
        try {
            // Get MySQL database configuration
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            // return ['dbHost' => $dbHost, 'dbPort' => $dbPort, 'dbName' => $dbName, 'dbUser' => $dbUser, 'dbPassword' => $dbPassword];

            // Create backup record
            $backup = Backup::create([
                'label'     => $label,
                'file_name' => $filename,
                'file_path' => $filePathToStore,
                'times'     => $times,
                'dates'     => $dates,
                'created_by' => auth()->user()->full_name ?? '',
            ]);

            $filename = $backupFilePath;

            // Validate database configuration
            if (!$dbHost || !$dbPort || !$dbName || !$dbUser) {
                throw new Exception('Database configuration is incomplete');
            }

            // MySQL-specific command format (for Windows, change path to mysqldump if needed)
            $mysqldumpPath = env('MYSQL_DUMP_PATH', 'D:/installed/xamp2/mysql/bin/mysqldump.exe');

            // Check if mysqldump exists
            if (!file_exists(trim($mysqldumpPath, '"'))) {
                throw new Exception('mysqldump not found at ' . $mysqldumpPath);
            }


            // Tables to exclude
            $excludedTables = ['backups','sessions'];

            // Generate --ignore-table options
            $ignoreTableParams = '';
            foreach ($excludedTables as $table) {
                $ignoreTableParams .= " --ignore-table={$dbName}.{$table}";
            }



            // Prepare mysqldump command without ingored table
            // $command = match ($type) {
            //     'data_only' => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --no-create-info --result-file=\"{$filename}\"",
            //     'schema_only' => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --no-data --result-file=\"{$filename}\"",
            //     default => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --result-file=\"{$filename}\""
            // };

            // Prepare mysqldump command with ingored table
            $command = match ($type) {
                'data_only' => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --no-create-info --result-file=\"{$filename}\"{$ignoreTableParams}",
                'schema_only' => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --no-data --result-file=\"{$filename}\"",
                default => "{$mysqldumpPath} --user={$dbUser} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --databases {$dbName} --result-file=\"{$filename}\"{$ignoreTableParams}"
            };

            // Execute backup command
            exec("{$command} 2>&1", $output, $returnVar);

            // Check if the command was successful
            if ($returnVar !== 0) {
                \Log::error('Backup command failed', [
                    'output' => $output,
                    'return_code' => $returnVar
                ]);
                throw new Exception("Backup failed: " . implode("\n", $output));
            }

            // Verify backup file exists and has content
            if (!file_exists($filename) || filesize($filename) === 0) {
                throw new Exception("Backup file is empty or not created");
            }

            Session::flash('notification', [
                'message' => 'موفقانه بک اپ ایجاد گردید',
                'type' => 'success',
            ]);
            return redirect()->route('backups.index');

        } catch (Exception $e) {
            
          // If backup fails, output the error
            Session::flash('notification', [
               'message' => 'Backup failed: ' . $e,
               'type' => 'error',
           ]);
           return redirect()->route('backups.index');
        }
    }

    /**
     * restore backup
     */

    public function restoreBackup($backupId)
    {
        try 
        {
            // Find and validate backup
            $backup = Backup::findOrFail($backupId);
            
            // Save the original backup data (you may use this for rollback purposes if needed)
            $backupData = $backup->toArray();
            
    
            // Validate that the backup file exists
            $backupPath = realpath(storage_path("app/backups/{$backup->file_name}"));
            if (!$backupPath || !file_exists($backupPath)) {
                throw new Exception("Backup file not found at: {$backup->file_name}");
            }
    
            \Log::info('Starting database restore process...', [
                'backup_path' => $backupPath,
                'backup_exists' => file_exists($backupPath),
                'backup_size' => filesize($backupPath),
                'backup_id' => $backupId,
                'original_data' => $backupData
            ]);
    
             // Get MySQL database configuration
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
    
            
            // Validate MySQL utility (mysqldump for restoration)
            $mysqlPath = env('MYSQL_PATH', 'D:/installed/xamp2/mysql/bin/mysql.exe');

            if (!file_exists(trim($mysqlPath, '"'))) {
                throw new Exception('mysql utility not found at: ' . $mysqlPath);
            }

            // Step 2: Restore the database
            $restoreCommand = '';
            
    
            // For SQL files, restore using the mysql command
            $restoreCommand = sprintf(
                '"%s" --user=%s --password=%s --host=%s --port=%d --database=%s < "%s" 2>&1',
                $mysqlPath,
                $dbUser,
                $dbPassword,
                $dbHost,
                $dbPort,
                $dbName,
                $backupPath
            );

            \Log::info('Executing restore command...', ['command' => $restoreCommand]);
            exec($restoreCommand, $output, $returnVar);

            // Check if the command was successful
            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output);
                \Log::error('Database restore failed', [
                    'output' => $output,
                    'return_code' => $returnVar,
                    'command' => $restoreCommand
                ]);

                throw new Exception("Database restore failed. Error: " . $errorMessage);
            }
            // Restore the original backup data
            // Backup::where('id', $backupId)->update($backupData);

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            \Log::error('Restore failed', [
                'backup_id' => $backupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'failed']);
        }
    }

    

    /**
     * Download a backup file.
     */
    public function download($id)
    {
        $backup = Backup::findOrFail($id);
        // return ['backup' => $backup->file_name];
        
        if (file_exists(storage_path("app/backups/{$backup->file_name}"))) {
            return response()->download(storage_path("app/backups/{$backup->file_name}"));
        } else {
            return back()->with('error', 'Backup file not found!');
        }
    }

    /**
     * Delete a backup file and remove from database.
     */
    public function deleteBackup($id)
    {

            $backup = Backup::findOrFail($id);
             // Construct the full file path using the stored file name
            $filePath = storage_path("app/backups/{$backup->file_name}");

            // Check if the backup file exists
            if (file_exists($filePath)) 
            {
                unlink($filePath);
                $backup->delete();
              return response()->json(['status' => 'success']);
            } 
            else 
            {
               return response()->json(['status' => 'failed']);
            }

    }
    
}
