<?php

$db = array();
$auth_conf = array();

// ------------------------
// MySQL Configuration :
// ------------------------

$db['host'] = "127.0.0.1";
$db['user'] = "root";
$db['pass'] = "";
$db['name'] = "radardgeti";

$appId = '469051533130170'; //Facebook App ID
$appSecret = '26020cfe2bc84b28b4e034e6cd7b6c33'; // Facebook App Secret
// ------------------------
// Auth Configuration :
// ------------------------

// Base url of site PHPAuth 2.0 is hosted on, including trailing slash
$auth_conf['base_url'] = "http://localhost/radar/"; 

// Password salt 1
$auth_conf['salt_1'] = 'us_1dUDN4N-53/dkf7Sd?vbc_due1d?df!feg';
// Password salt 2
$auth_conf['salt_2'] = 'Yu23ds09*d?u8SDv6sd?usi$_YSdsa24fd+83';
// Password salt 3
$auth_conf['salt_3'] = '63fds.dfhsAdyISs_?&jdUsydbv92bf54ggvc';

?>