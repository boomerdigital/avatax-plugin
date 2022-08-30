<?php
define( 'PLUGINNAME', 'Avatax Custom' );
define( 'PLUGINPATH', ABSPATH . 'wp-content/plugins/' );
define( 'AVATAXPLUGINPATH', PLUGINPATH.'avataxd/' );
$env=get_option('env');
if($env=='sandbox'){
    define( 'AVATAXENDPOINT', 'https://sandbox-rest.avatax.com/' );
}else{
    define( 'AVATAXENDPOINT', 'https://rest.avatax.com/' );
}
define( 'AVATAXRELATIVEPATH', site_url().'/wp-content/plugins/avataxd/' );
$ac = get_option('ac');
define( 'ACCOUNTNUMBER', $ac );
$lic = get_option('lic');
define( 'LICENSEKEY', $lic );
$com = get_option('com');
define( 'COMPANYCODE', $com );
$rec = get_option('rec');
define( 'RECORDCALCULATIONS', $rec );
$vat = get_option('vat');
define( 'VAT', $vat );
$commit = get_option('commit');
define( 'COMMIT', $commit );
$req = get_option('req');
define( 'ADDRESSVALIDATION', $req );
$non = get_option('non');
define( 'TAXCALCART', $non );
$check = get_option('check');
define( 'ENABLEADDRESSVALIDATE', $check );
$debug = get_option('debug');
define( 'DEBUG', $debug );
$shippingTax = get_option('shippingTax');
define( 'SHIPPINGTAX', $shippingTax );
?>