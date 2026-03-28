<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestoreDatabase extends Command
{
    protected $signature = 'db:restore {file? : The backup file to restore}';
    protected $description = 'Restore the database from a backup';

    public function handle()
    {
        $backupDir = storage_path('app/backups');
        
        // Get backup file
        $file = $this->argument('file');
        
        if (!$file) {
            $backups = glob($backupDir . '/*.sql');
            if (empty($backups)) {
                $this->error('No backup files found!');
                return 1;
            }
            
            $backups = array_map('basename', $backups);
            $file = $this->choice('Select a backup to restore', $backups);
            $file = $backupDir . '/' . $file;
        } else {
            $file = $backupDir . '/' . $file;
        }
        
        if (!file_exists($file)) {
            $this->error("Backup file not found: {$file}");
            return 1;
        }
        
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        // Correct MySQL client path for Laragon
        $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysql.exe';
        
        // Check if MySQL client exists
        if (!file_exists($mysqlPath)) {
            $this->error("MySQL client not found at: {$mysqlPath}");
            $this->info("Please update the path in the command");
            return 1;
        }
        
        $this->warn('⚠️  This will overwrite your current database!');
        if (!$this->confirm('Are you sure you want to restore this backup?')) {
            return 0;
        }
        
        $this->info('Restoring database...');
        $this->info("Using MySQL client: {$mysqlPath}");
        $this->info("Backup file: {$file}");
        
        // Build restore command without -p flag if password is empty
        if (empty($password)) {
            $command = sprintf(
                '"%s" -u%s -h%s %s < "%s"',
                $mysqlPath,
                $username,
                $host,
                $database,
                $file
            );
        } else {
            $command = sprintf(
                '"%s" -u%s -p%s -h%s %s < "%s"',
                $mysqlPath,
                $username,
                $password,
                $host,
                $database,
                $file
            );
        }
        
        $this->info("Executing command...");
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info('✅ Database restored successfully!');
            $this->info("📍 Restored from: {$file}");
        } else {
            $this->error('❌ Restore failed!');
            $this->error("Return code: {$returnCode}");
            if (!empty($output)) {
                $this->error("Output: " . implode("\n", $output));
            }
        }
    }
}