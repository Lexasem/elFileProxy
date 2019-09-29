<?php
/*
Plugin Name: El File proxy
Plugin URI: http://github.com/lexasem
Description: this plugin substitutes file urls
Author: Alexey Semerenko
Version: 1.0.0
Author http://github.com/lexasem
*/

require_once plugin_dir_path(__FILE__) . 'includes/ElFileProxyActivator.php';


function elFileProxyActivate()
{
    ElFileProxyActivator::activate();
}

function elFileProxyDeactivate()
{
    ElFileProxyActivator::deactivate();
}

register_activation_hook(__FILE__, 'elFileProxyActivate');
register_deactivation_hook(__FILE__, 'elFileProxyDeactivate');

require_once plugin_dir_path(__FILE__) . 'includes/ElFileProxy.php';

$elFileProxy = new ElFileProxy();
$elFileProxy->run();
