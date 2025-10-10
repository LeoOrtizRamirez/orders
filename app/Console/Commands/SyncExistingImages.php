<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncExistingImages extends Command
{
    protected $signature = 'storage:sync-images';
    protected $description = 'Sync existing images to public folder for Hostinger';

    public function handle()
    {
        $storagePath = storage_path('app/public');
        $publicPath = public_path('storage');
        
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }
        
        // Sincronizar todos los archivos
        $this->info('Sincronizando imágenes existentes...');
        
        foreach (File::allFiles($storagePath) as $file) {
            $relativePath = $file->getRelativePathname();
            $destPath = $publicPath . '/' . $relativePath;
            
            if (!File::exists(dirname($destPath))) {
                File::makeDirectory(dirname($destPath), 0755, true);
            }
            
            if (!File::exists($destPath)) {
                File::copy($file->getPathname(), $destPath);
                $this->line("Copied: $relativePath");
            }
        }
        
        $this->info('¡Sincronización completada!');
    }
}