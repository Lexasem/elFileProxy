<?php


class ElFileProxy
{
    private $extToMime = [
        '.7z' => 'application/x-7z-compressed',
        '.avi' => 'video/x-msvideo',
        '.bmp' => 'image/bmp',
        '.css' => 'text/css',
        '.csv' => 'text/csv',
        '.doc' => 'application/msword',
        '.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.epub' => 'application/epub+zip',
        '.gif' => 'image/gif',
        '.gz' => 'application/gzip',
        '.ico' => 'image/vnd.microsoft.icon',
        '.jpeg' => 'image/jpeg',
        '.jpg' => 'image/jpeg',
        '.mid .midi' => 'audio/midi audio/x-midi',
        '.mp3' => 'audio/mpeg',
        '.ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        '.odt' => 'application/vnd.oasis.opendocument.text',
        '.oga' => 'audio/ogg',
        '.ogv' => 'video/ogg',
        '.pdf' => 'application/pdf',
        '.png' => 'image/png',
        '.ppt' => 'application/vnd.ms-powerpoint',
        '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        '.txt' => 'text/plain',
        '.wav' => 'audio/wav',
        '.webp' => 'image/webp',
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.zip' => 'application/zip',
    ];

    public function run()
    {
        add_action('init', [$this, 'addRewriteRules']);
        add_action('wp', [$this, 'doProxy']);
        add_filter('query_vars', [$this, 'addCustomVar']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function doProxy()
    {
        $elFile = get_query_var('el_file');
        if(empty($elFile)){
            return;
        }
        $this->returnFile($elFile);
    }

    public function addCustomVar($vars)
    {
        $vars[] .= 'el_file';
        return $vars;
    }

    public function addRewriteRules()
    {
        add_rewrite_rule('files/(.*)', 'index.php?el_file=$matches[1]', 'top');
        flush_rewrite_rules(false);
    }

    public function registerSettings()
    {
        $args = [
            'type' => 'string',
            'default' => 'https://wordpress.org',
        ];
        register_setting('el-file-proxy-options', 'efp-src-url', $args);
    }

    private function returnFile($filePath)
    {
        if (empty($filePath)) {
            $this->http404();
        }

        $src = get_option('efp-src-url') . '/' . $filePath;
        $contents = $this->loadFileContents($src);
        if (empty($contents)) {
            $this->http404();
        }
        $this->exportFile($contents, $this->getMimeType($filePath), $this->getFileName($filePath));
    }

    private function getFileName($src)
    {
        if (!preg_match('/([^\/]*)$/', $src, $matches)) {
            $this->http404();
        }

        return $matches[1];
    }

    private function getMimeType($src)
    {
        if(!preg_match('/(\.[^.]*)$/', $src, $matches)){
            $this->http404();
        };

        $ext = $matches[1];

        $mimeType = $this->arrGetValue($this->extToMime, $ext);
        if (empty($mimeType)) {
            $this->http404();
        }

        return $mimeType;
    }

    private function arrGetValue($array, $key, $default = null)
    {
        if (!array_key_exists($key, $array)) {
            return $default;
        }

        return $array[$key];
    }

    private function http404()
    {
        header("HTTP/1.0 404 Not Found");
        echo 'file not found';
        die();
    }

    private function loadFileContents($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    private function exportFile($contents, $mimeType, $fileName)
    {
        header('Content-Disposition: inline; filename="'
            . $fileName
            . '"');
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . strlen($contents));
        header('Connection: close');
        echo $contents;
        die();
    }
}
