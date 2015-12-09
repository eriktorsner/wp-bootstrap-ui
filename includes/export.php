<?php

class wpbsui_Export
{
    public function runExport()
    {

        // Create a mock localsettings
        $ls = new stdClass();
        $ls->url = get_site_url();
        $ls->wppath = rtrim(ABSPATH, '/');
        file_put_contents(WPBSUI_CONTENT.'/localsettings.json', json_encode($ls));

        chdir(WPBSUI_CONTENT);
        $export = Wpbootstrap\Export::getInstance();
        $export->export();

        $fileName = md5(json_encode($ls).microtime(true)).'.zip';
        $zip = new ZipArchive();
        $res = $zip->open(WPBSUI_CONTENT.'/'.$fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($res) {
            $this->addFolder($zip, WPBSUI_CONTENT.'/bootstrap');
            $zip->addFile(WPBSUI_CONTENT.'/appsettings.json', 'appsettings.json');
            if (file_exists(WPBSUI_CONTENT.'/manifest')) {
                $zip->addFile(WPBSUI_CONTENT.'/manifest', 'manifest');
            }
            $zip->close();
        } else {
            echo 'Could not create zip file: '.WPBSUI_CONTENT.'/'.$fileName;
        }
    }

    private function addFolder($zip, $folder)
    {
        // Create recursive directory iterator
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(WPBSUI_CONTENT) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
