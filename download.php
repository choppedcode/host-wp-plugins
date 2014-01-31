<?php
/*
 *
 * One Time Download
 * Jacob Wyke
 * jacob@frozensheep.com
 *
 */

//The directory where the download files are kept - random folder names are best
$strDownloadFolder = DOWNLOAD_DIR;

//If you can download a file more than once
$boolAllowMultipleDownload = 0;

$strDownload = $strDownloadFolder.$_GET['file'];

$strFile = file_get_contents($strDownload);

header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"".str_replace(" ", "_", $_GET['file'])."\"");
echo $strFile;
die();
