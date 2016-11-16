<?php

$tckimlikmodulsurum = "1.0.2";

function tckimlik_guncellemevarmi($tckimlikmodulsurum) {
$tckimliksite = "https://aponkral.net/tckimlik/version.txt";

$ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $tckimliksite);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$tckimlikguncelsurum = curl_exec($ch);

curl_close($ch);

if($tckimlikmodulsurum == $tckimlikguncelsurum) {
return true;
}

else {
return false;
}

}

$tckimlikmoduluguncelmi = tckimlik_guncellemevarmi($tckimlikmodulsurum);

?>
