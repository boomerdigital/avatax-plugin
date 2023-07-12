<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// We'll be outputting a PDF
header('Content-type: application/pdf');

// Set filename of download
header('Content-Disposition: attachment; filename="User-guide.pdf"');

// Path to the file (searching in wordpress template directory)
$url = AVATAXPLUGINPATH."assets/User-guide.pdf";

readfile($url);