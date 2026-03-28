<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function handle()
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // Create backup directory if not exists
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/' . $database . '_' . $timestamp . '.sql';
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg(config('database.connections.mysql.host')),
            escapeshellarg($database),
            escapeshellarg($backupFile)
        );
        
        $this->info('Creating backup...');
        
        // Execute backup
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info('✅ Database backed up successfully!');
            $this->info("📍 Location: {$backupFile}");
        } else {
            $this->error('❌ Backup failed!');
            $this->error('Make sure mysqldump is available in your PATH');
        }
    }
}