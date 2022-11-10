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
$com = get_option('companycode');
define( 'COMPANYCODE', $com );
$comID = get_option('companyID');
define( 'COMPANYID', $comID );
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
$enable_avatax = get_option('avatax_enable');
define( 'ENABLEAVATAX', $enable_avatax );
$default_tax_code = get_option('default_tax_code');
define( 'DEFAULTTAXCODE', $default_tax_code );
?>