<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Storage;
use Omatech\Editora\Admin\Models\Security;

class AdminUploadCrop extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        if (request()->has('file')) {
            $file = request()->file;
            
            $disk = Storage::disk(config('editora-admin.uploads-storage'));

            $fileInfo = $this->uploadFile($disk, $file);
            
            return response()->json(array_merge(['message' => 'ok'], $fileInfo), 200);
        }

        return response()->json(['message' => 'fail'], 200);
    }

    private function uploadFile($disk, $file)
    {
        $path = '/uploads/'.date('Ymd');
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
