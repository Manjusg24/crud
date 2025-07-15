<?php
session_start();
session_unset();
session_destroy();

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['name']))
{
    header("location:../index.php");
}
?>
