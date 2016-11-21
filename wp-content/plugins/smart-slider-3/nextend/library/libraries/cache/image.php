<?php
N2Loader::import('libraries.cache.cache');

class N2CacheImage extends N2Cache {

    public function makeCache($fileExtension, $callable, $parameters = array(), $hash = false) {

        if (!$hash) {
            $hash = $this->generateHash($fileExtension, $callable, $parameters);
        }
        $keepFileName = pathinfo($parameters[1], PATHINFO_FILENAME);
        $fileName     = $hash . (!empty($keepFileName) ? '/' . $keepFileName : '') . '.' . $fileExtension;

        if (!$this->isCached($fileName)) {
            $path = $this->getStorageFilePath($fileName);

            if (!empty($keepFileName) && !N2Filesystem::existsFolder(dirname($path))) {
                N2Filesystem::createFolder(dirname($path));
            }

            array_unshift($parameters, $path);

            call_user_func_array($callable, $parameters);
        }
        return $this->getStorageFilePath($fileName);
    }

    private function isCached($fileName) {
        if (N2Filesystem::existsFile($this->getStorageFilePath($fileName))) {
            return true;
        }
        return false;
    }

    private function generateHash($fileExtension, $callable, $parameters) {
        return md5(json_encode(array(
            $fileExtension,
            $callable,
            $parameters
        )));
    }

    protected function setCurrentPath() {
        $this->currentPath = N2Filesystem::getImagesFolder() . NDS . $this->group;

        if (!N2Filesystem::existsFolder($this->currentPath)) {
            N2Filesystem::createFolder($this->currentPath);
        }
    }
}

class N2StoreImage extends N2Cache {

    public function makeCache($fileName, $content) {
        if (!$this->isImage($fileName)) {
            return false;
        }

        $targetFile = $this->getStorageFilePath($fileName);
        if (!$this->isCached($fileName)) {
            N2Filesystem::createFile($targetFile, $content);
        }
        return $targetFile;
    }

    private function isCached($fileName) {
        if (N2Filesystem::existsFile($this->getStorageFilePath($fileName))) {
            return true;
        }
        return false;
    }

    private function isImage($fileName) {
        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png'
        );

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, $supported_image)) {
            return true;
        }
        return false;
    }

    protected function setCurrentPath() {
        $this->currentPath = N2Filesystem::getImagesFolder() . NDS . $this->group;

        if (!N2Filesystem::existsFolder($this->currentPath)) {
            N2Filesystem::createFolder($this->currentPath);
        }
    }
}