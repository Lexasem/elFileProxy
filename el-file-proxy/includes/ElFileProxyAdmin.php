<?php


class ElFileProxyAdmin
{
    public function initHooks()
    {
        add_action('admin_menu', [$this, 'adminMenu']);
    }

    public function adminMenu()
    {
        add_options_page('elFileProxy settings', 'elFileProxy settings', 'manage_options', 'el-file-proxy-settings', [$this, 'renderMenu']);
    }

    public function renderMenu()
    {
        echo '<div class="wrap">'
            . '<h1>ElFileProxySettings</h1>'
            . '</div>';

        /** @noinspection HtmlUnknownTarget */
        echo '<form method="post" action="options.php">';

        settings_fields('el-file-proxy-options');
        do_settings_sections('el-file-proxy-options');
        /** @noinspection HtmlDeprecatedAttribute */
        echo '<table class="form-table">'
            . '<tr valign="top">'
            . '<th scope="row">Source url</th>'
            . '<td>'
            . '<input type="text" name="efp-src-url" value="' . esc_attr(get_option('efp-src-url')) . '"/>'
            . '</td>'
            . '</tr>'
            . '</table>';
        submit_button('save');
        echo '</form>';
    }
}
