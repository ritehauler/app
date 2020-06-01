<?php
/**
 * Get file contents from relative url
 * @param string $dir
 * @param string $file_ext
 */

if (!function_exists("getFileContents")) {
    function getFileContents($dir, $file_ext = ".sql")
    {
        $contents = "";
        $url = $dir . "/"; // reserver for file_get_contents
        $dir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($dir, "/")); // convert slashes for scandir
        if (preg_match('/localhost/', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname())) {
            $files = scandir($dir);
        } else {
            $files = scandir(DIRECTORY_SEPARATOR . $dir);
        }
        if ($files) {
            foreach ($files as $file) {
                if (preg_match("@(\\" . $file_ext . ")$@", $file)) {
                    //include_once($dir.$file);
                    $contents .= trim(file_get_contents($url . $file, true));
                }
            }
        }
        return $contents;
    }
}