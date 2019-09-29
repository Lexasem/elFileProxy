<?php


class ElFileProxy
{
    public function run()
    {
        add_action('init', [$this, 'addRewriteRules']);
        add_action('wp', [$this, 'doProxy']);
        add_filter('query_vars', [$this, 'addCustomVar']);
    }

    public function doProxy()
    {
        $this->returnFile(get_query_var('el_file'));
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

    private function returnFile($filePath)
    {
        if (empty($filePath)) {
            return; // do nothing
        }

        echo 'it works! file: ' . $filePath;
        die();
    }


}
