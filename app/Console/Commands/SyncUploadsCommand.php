<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncUploadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all uploaded files from storage/app/public to public/uploads for bulletproof live hosting accessibility';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting sync between storage/app/public and public/uploads...');

        $directories = [
            'proofs' => [
                'src' => storage_path('app/public/proofs'),
                'dest' => public_path('uploads/proofs'),
            ],
            'user_qr' => [
                'src' => storage_path('app/public/proofs/user_qr'),
                'dest' => public_path('uploads/proofs/user_qr'),
            ],
            'admin_proofs' => [
                'src' => storage_path('app/public/proofs/admin_proofs'),
                'dest' => public_path('uploads/proofs/admin_proofs'),
            ],
            'qr' => [
                'src' => storage_path('app/public/qr'),
                'dest' => public_path('uploads/qr'),
            ],
        ];

        $syncedCount = 0;

        foreach ($directories as $key => $paths) {
            $src = $paths['src'];
            $dest = $paths['dest'];

            if (! File::isDirectory($dest)) {
                File::makeDirectory($dest, 0755, true);
                $this->line("Created directory: <comment>{$dest}</comment>");
            }

            if (File::isDirectory($src)) {
                $files = File::files($src);
                foreach ($files as $file) {
                    $target = $dest.DIRECTORY_SEPARATOR.$file->getFilename();
                    if (! File::exists($target) || File::size($file->getPathname()) !== File::size($target)) {
                        File::copy($file->getPathname(), $target);
                        $syncedCount++;
                        $this->line("Synced [{$key}]: <info>{$file->getFilename()}</info>");
                    }
                }
            }
        }

        $this->info("Sync completed successfully! {$syncedCount} file(s) synchronized.");

        return Command::SUCCESS;
    }
}
