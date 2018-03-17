<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
exit();
}

require_once('helpers.php');

function tckimlik_config() {
    $db_field_names = str_putcsv(get_custom_fields());
    $configarray = array(
    "name" => "TC Kimlik No Dogrulama",
    "description" => "WHMCS için T.C. Kimlik numarası doğrulama modülü",
    "version" => "1.1.0",
    "author" => "APONKRAL",
    "language" => "turkish",
        "fields" => array(
            "tc_field" => array(
                "FriendlyName" => "TC Kimlik Özel Alanı",
                "Type" => "dropdown",
                "Options" => $db_field_names,
                "Description" => "Özel alanlarınız arasından TC Kimlik için olanı seçin",
            ),
            "birthyear_field" => array(
                "FriendlyName" => "Doğum yılı alanı",
                "Type" => "dropdown",
                "Options" => $db_field_names,
                "Description" => "Özel alanlarınız arasından doğum yılı için olanı seçin",
            ),
            "only_turkish" => array(
                "FriendlyName" => "Ülke kontrolü",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "Yalnızca Türkiye adresli kullanıcılar için geçerli olsun",
            ),
			"unique_identity" => array(
                "FriendlyName" => "Benzersiz Kimlik",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "Bir T.C. Kimlik Numarası ile bir kere kayıt olunabilir.",
            ),
			"unique_identity_message" => array(
                "FriendlyName" => "Benzersiz Kimlik Mesajı",
                "Type" => "text",
                "Size" => 25,
                "Description" => "Başka kullanıcıya ait olan bir T.C. Kimlik Numarası ile yeni kaydı ve profil güncellemeyi engeller.",
                "Default" => "Bu T.C. Kimlik Numarası ile kayıtlı bir kullanıcı var.",
            ),
            "error_message" => array(
                "FriendlyName" => "Hata Mesajı",
                "Type" => "text",
                "Size" => 25,
                "Description" => "T.C. Kimlik Numarası uyuşmadığı takdirde müşteriye gösterilecek hata yazısı.",
                "Default" => "T.C. Kimlik Numaranız girmiş olduğunuz bilgiler ile uyuşmamaktadır.",
            ),
        )
    );
    return $configarray;
}

function tckimlik_output() {

$getconfname = "TC Kimlik No Dogrulama";
$getconfdescription = "WHMCS için T.C. Kimlik numarası doğrulama modülü";
$getconfversion = "1.1.0";
$getconfauthor = "APONKRAL";

require_once("versioncontrol.php");

echo "<div style=\"background: #eee; padding: 10px; font-size: 14px\"><br /><br />";
echo "<strong>Modül adı : </strong>" . $getconfname . "<br /><br />";
echo "<strong>Modül açıklaması : </strong>" . $getconfdescription . "<br /><br />";
echo "<strong>Modül sürümü : </strong>" . $getconfversion . "<br /><br />";
echo "<strong>Modülü geliştiren : </strong>" . $getconfauthor . "<br /><br /><br />";

### Guncelleme Kontrol Başladı ###

if($tckimlikmoduluguncelmi === true) {
// T.C. Kimlik numarası doğrulama eklentisi günceldir.
echo "T.C. Kimlik No Doğrulama Modülü Güncel.";
}

elseif($tckimlikmoduluguncelmi === false) {
// T.C. Kimlik numarası doğrulama eklentisi güncel değildir.
echo "T.C. Kimlik No Doğrulama Modülü Güncel değil." . "<br />";
echo "Modülü güncellemek istiyorsanız <a href=\"https://github.com/aponkral/whmcs-tckimlik/\" target=\"_blank\" title=\"WHMCS T.C. Kimlik Numarası doğrulama modülü\">GitHub'dan</a> Modülü indirerek WHMCS ana dizinininden <strong>modules/addons/</strong> klasörüne yükleyin.";
}

### Guncelleme Kontrol Bitti ###

echo "<br /></div>";

}
?>