<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait Files {

    // This function do changing image name and then move it.
    public static function moveFile($file, $folder) {
        $new_name = Str::uuid() .  '.' . $file->getClientOriginalExtension();
        if(! file_exists(public_path($folder))) {
            mkdir(public_path($folder));
        }
        File::move($file->getRealPath(), public_path($folder) . '/' . $new_name);
        return $new_name;

    }

    // This function do image delation.
    public static function deleteFile($path) {
        if(file_exists($path) && !is_dir($path)) {
            unlink($path);
        }
    }
}
