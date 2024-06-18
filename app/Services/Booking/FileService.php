<?php

namespace App\Services\Booking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileService
{
    protected function isExtensionAllowed(String $ext)
    {
        $allowed_extensions = [
            'jpg', 'jpeg', 'gif', 'png'
        ];

        return in_array($ext, $allowed_extensions);
    }

    public function uploadPicture($file, $dest, $filename = null)
    {
        $ext = $file->getClientOriginalExtension();

        if (!$this->isExtensionAllowed($ext)) {
            return false;
        }

        $filename = $filename ?? time() .'.'. $ext;

        if ($file->isValid()) {
            $file->storeAs($dest, $filename);
            return response()->json([
                'link' => asset('storage/'. $dest .'/'. $filename)
            ]);
        }

        return false;
    }

    public function upload($file, $dest, $filename = null, $resize = [])
    {
        $ext = $file->getClientOriginalExtension();
        $path = public_path($dest);

        if (!$this->isExtensionAllowed($ext)) {
            return false;
        }

        $filename = $filename ?? time() .'.'. $ext;

        if ($file->isValid()) {
            $file = Image::make($file);

            if (count($resize) > 0) {
                $file
                    ->crop($resize['w'], $resize['h'])
                    ->resize($resize['w'], $resize['h'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
            }

            if (!is_dir($path)) {
                mkdir($path);
            }

            $file->save(public_path($dest .'/'. $filename));
            return response()->json([
                'link' => asset($dest .'/'. $filename)
            ]);
        }

        return false;
    }
}
