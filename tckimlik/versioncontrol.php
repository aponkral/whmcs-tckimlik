<?php

$tckimlikmodulversion = "1.0.1";

$tckimliksite = "https://aponkral.net/tckimlik/version.txt";


$ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $tckimliksite);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$tckimlikguncelversion = curl_exec($ch);

curl_close($ch);

?>
