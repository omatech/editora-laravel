<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Storage;
use Omatech\Editora\Admin\Models\Security;
use Imagick;

class AdminUploadCrop extends AuthController
{
    public function cropImage($imagePath, $crop) {
        $imagick = new Imagick(realpath($imagePath));

        if($crop['scaleX'] !== 1) {
            $imagick->rotateImage('none', 180);
            $imagick->flipImage();
        }
        $imagick->rotateImage('none', $crop['rotate']);

        $imagick->cropImage($crop['width'], $crop['height'], $crop['x'], $crop['y']);
        $imagick->resizeImage(
            $crop['container_width'] * $crop['scale'],
            $crop['container_height'] * $crop['scale'],
            \Imagick::FILTER_LANCZOS, 0.9, true
        );
        
        file_put_contents(realpath($imagePath), $imagick->getImageBlob());
    }

    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        if (request()->has('file')) {
            $file = request()->file;
            $crop = json_decode(request()->crop, true);

            if ($crop) {
                $this->cropImage($file, $crop);
            }

            $disk = Storage::disk(config('editora-admin.uploads-storage'));

            $fileInfo = $this->uploadFile($disk, $file);

            return response()->json(array_merge(['message' => 'ok'], $fileInfo), 200);
        }

        return response()->json(['message' => 'fail'], 200);
    }

    private function uploadFile($disk, $file)
    {
        $path = '/' . config('editora-admin.uploads-storage-dir', 'uploads') . '/' . date('Ymd');
        $fileName = $file->getClientOriginalName();
        $fileName = clean_file_name($fileName);
        $filePath = $path.'/'.$fileName;

        $fileInfo = $this->checkExistAndRename($disk, [
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'ext' => pathinfo($fileName, PATHINFO_EXTENSION),
            'fileName' => pathinfo($fileName, PATHINFO_BASENAME),
            'path' => $path,
            'filePath' => $filePath,
        ]);

        $file = $disk->putFileAs($path, $file, $fileInfo['fileName']);


        if (config('editora-admin.url-storage-relative')==true) {
            $accessUrl = $fileInfo['filePath'];
        }else{
            $accessUrl = $disk->url($file);
            $accessUrl = str_replace(config('editora-admin.remove-public-url-segments', []), '', $accessUrl);
        }

        return ['accessUrl' => $accessUrl, 'filePath' => $fileInfo['filePath']];
    }

    private function checkExistAndRename($disk, $file, $i = 0)
    {
        if ($disk->exists($file['filePath'])) {
            $file['fileName'] = $file['name'].'.'.$i.'.'.$file['ext'];
            $file['filePath'] = $file['path'].'/'.$file['fileName'];
            $file = $this->checkExistAndRename($disk, $file, ++$i);
        }

        return $file;
    }
}
