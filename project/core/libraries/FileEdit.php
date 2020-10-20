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
                for ($i = 0; $i < count($file['name']); $i++) {
                    if (!empty($file['name'][$i])) {
                        $file_arr['name'] = $file['name'][$i];
                        $file_arr['type'] = $file['type'][$i];
                        $file_arr['tmp_name'] = $file['tmp_name'][$i];
                        $file_arr['error'] = $file['error'][$i];
                        $file_arr['size'] = $file['size'][$i];

                        $res_name = $this->createFile($file_arr);

                        if ($res_name) {
                            $this->imgArr[$key][] = $res_name;
                        }
                    }
                }
            } else {
                if ($file['name']) {
                    $res_name = $this->createFile($file['name']);
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

        $fileName = explode('.', $fileName);
        $fileName = (new TextModify())->translit($fileName);
        $fileName = $this->checkFile($fileName, $ext);
    }

    protected function checkFile($fileName, $ext, $fileLastName = '')
    {
        if (!file_exists($this->pathToDir . $fileName . $fileLastName . '.' . $ext)) {
            return $fileName . $fileLastName . '.' . $ext;
        }

        return $this->checkFile($fileName,$ext,hash('crc32',time()));
    }


}