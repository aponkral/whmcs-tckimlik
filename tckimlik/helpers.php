<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

include_once("versioncontrol.php");

if($tckimlikmodulversion == $tckimlikguncelversion) {

include_once("helpers-t.php");

}

elseif($tckimlikmodulversion != $tckimlikguncelversion) {

include_once("helpers-f.php");

}

?>
