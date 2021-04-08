<?php


namespace libraries;


class FileEdit
{

    protected $imgArr = [];
    protected $pathToDir;


    public function addFile($pathToDir = false)
    {

        if (!$pathToDir) {
            $this->pathToDir = $_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR;
        } else {
            $this->pathToDir = $pathToDir;
        }

        foreach ($_FILES as $key => $file) {

            if (is_array($file['name'])) {
                $file_arr = [];

              foreach ($file['name'] as $i => $val) {
                  
                    if (!empty($file['name'][$i])) {
                        $file_arr['name'] = $file['name'][$i];
                        $file_arr['type'] = $file['type'][$i];
                        $file_arr['tmp_name'] = $file['tmp_name'][$i];
                        $file_arr['error'] = $file['error'][$i];
                        $file_arr['size'] = $file['size'][$i];

                        $res_name = $this->createFile($file_arr);

                        if ($res_name) {
                            $this->imgArr[$key][$i] = $res_name;
                        }
                    }
                }
            } else {
                if ($file['name']) {
                    $res_name = $this->createFile($file);
                    if ($res_name) {
                        $this->imgArr[$key] = $res_name;
                    }
                }

            }


        }
        return $this->getFiles();
    }

    public function getFiles()
    {

        return $this->imgArr;

    }

    protected function createFile($file)
    {
        $fileName = explode('.', $file['name']);

        $ext = $fileName[count($fileName) - 1];
        unset($fileName[count($fileName) - 1]);

        $fileName = explode('.', $fileName[0]);

        $fileName = (new TextModify())->translit($fileName[0]);
        $fileName = $this->checkFile($fileName, $ext);
        $dest = $this->pathToDir . $fileName;

        if ($this->uploadFile($file['tmp_name'], $dest)) {
            return $fileName;
        }

        return false;
    }

    protected function checkFile($fileName, $ext, $fileLastName = '')
    {
        if (!file_exists($this->pathToDir . $fileName . $fileLastName . '.' . $ext)) {
            return $fileName . $fileLastName . '.' . $ext;
        }

        return $this->checkFile($fileName, $ext, '_' . hash('crc32', time() . mt_rand(1, 1000)));
    }

    protected function uploadFile($tmpName, $fileFullName)
    {
        return move_uploaded_file($tmpName, $fileFullName);
    }


}