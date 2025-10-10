<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class StorageHelper
{
    public static function syncToPublic($storagePath)
    {
        $sourcePath = storage_path('app/public/' . $storagePath);
        $destinationPath = public_path('storage/' . $storagePath);
        
        if (File::exists($sourcePath)) {
            // Crear directorio si no existe
            if (!File::exists(dirname($destinationPath))) {
                File::makeDirectory(dirname($destinationPath), 0755, true);
            }
            
            // Copiar archivo
            File::copy($sourcePath, $destinationPath);
            return true;
        }
        
        return false;
    }
    
    public static function deleteFromPublic($storagePath)
    {
        $publicPath = public_path('storage/' . $storagePath);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
            return true;
        }
        
        return false;
    }
}