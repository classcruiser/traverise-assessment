<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Ifsnop\Mysqldump as IMysqldump;
use Stancl\Tenancy\Database\Models\Domain;

class DatabaseController extends Controller
{
    public function __construct()
    {
        // make sure this controller can only be accessed on local environment and staging environment
        if (!in_array(app()->environment(), ['local', 'staging'])) {
            abort(404);
        }
    }

    public function backup()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".gz";
  
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
  
        $returnVar = NULL;
        $output  = NULL;
  
        exec($command, $output, $returnVar);
    }

    public function index()
    {
        $files = glob(storage_path() . "/app/backup/*");
        
        return view('database', compact('files'));
    }

    public function restore()
    {
        $file = request('file');

        Artisan::call('db:wipe', ['--drop-views' => true]);
        
        $command = "gunzip < ". $file ." | mysql --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE');
 
        $returnVar = NULL;
        $output  = NULL;

        try {
            $dump = new IMysqldump\Mysqldump(
                'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                [
                    'compress' => IMysqldump\Mysqldump::GZIP,
                    'add-drop-table' => true,
                    'single-transaction' => true,
                    'skip-dump-date' => true,
                ]
            );
            $dump->restore($file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // fix domains
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $domain->domain = str_replace('.com', '.dev', $domain->domain);
            $domain->save();
        }

        return redirect()->back()->with('success', 'Database restored successfully');
    }
}