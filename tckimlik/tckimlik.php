<?php
// *************************************************************************
// *                                                                       *
// * WHMCS TCKimlik - The Complete Turkish Identity Validation, Verify & Unique Identity Module    *
// * Copyright (c) APONKRAL. All Rights Reserved,                         *
// * Version: 2.0.0 (2.0.0release.1)                                      *
// * BuildId: 20190518.001                                                  *
// * Build Date: 18 May 2019                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: bilgi[@]aponkral.net                                                 *
// * Website: https://aponkral.net                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  APONKRAL may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
// Her şeyi sana yazdım!.. Her şeye seni yazdım!.. * Sena AÇIK

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly. This module was made by APONKRAL.");
exit();
}

require_once('helpers.php');

function tckimlik_config() {
    $db_field_names = str_putcsv(get_custom_fields());
    $configarray = [
    "name" => "TC Kimlik No Dogrulama",
    "description" => "WHMCS için T.C. Kimlik numarası doğrulama modülü.",
    "premium" => true,
    "version" => "2.0.0",
    "author" => "APONKRAL",
    "link" => "https://aponkral.net/",
    "language" => "turkish",
        "fields" => [
            "tc_field" => [
                "FriendlyName" => "TC Kimlik Özel Alanı",
                "Type" => "dropdown",
                "Options" => $db_field_names,
                "Description" => "Özel alanlarınız arasından TC Kimlik için olanı seçin",
            ],
            "birthyear_field" => [
                "FriendlyName" => "Doğum yılı alanı",
                "Type" => "dropdown",
                "Options" => $db_field_names,
                "Description" => "Özel alanlarınız arasından doğum yılı için olanı seçin",
            ],
            "verification_status_field" => [
                "FriendlyName" => "Doğrulama Durumu Kontrolü",
                "Type" => "dropdown",
                "Options" => $db_field_names,
                "Description" => "Özel alanlarınız arasından T.C. Kimlik Doğrulama Durumu için olanı seçin",
            ],
            "only_turkish" => [
                "FriendlyName" => "Ülke kontrolü",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "Yalnızca Türkiye adresli kullanıcılar için geçerli olsun",
            ],
			"unique_identity" => [
                "FriendlyName" => "Benzersiz Kimlik",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "Bir T.C. Kimlik Numarası ile bir kere kayıt olunabilir.",
            ],
            "verification_status_control" => [
                "FriendlyName" => "T.C. Kimlik Doğrulama Kontrolü",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "T.C. Kimlik doğrulaması yapmayan müşterilere bilgi mesajı gösterir.",
            ],
			"unique_identity_message" => [
                "FriendlyName" => "Benzersiz Kimlik Mesajı",
                "Type" => "text",
                "Size" => 25,
                "Description" => "Başka kullanıcıya ait olan bir T.C. Kimlik Numarası ile yeni kaydı ve profil güncellemeyi engeller.",
                "Default" => "Bu T.C. Kimlik Numarası ile kayıtlı bir kullanıcı var.",
            ],
            "error_message" => [
                "FriendlyName" => "Hata Mesajı",
                "Type" => "text",
                "Size" => 25,
                "Description" => "T.C. Kimlik Numarası uyuşmadığı takdirde müşteriye gösterilecek hata yazısı.",
                "Default" => "T.C. Kimlik Numaranız girmiş olduğunuz bilgiler ile uyuşmamaktadır.",
            ],
            "verification_about" => [
                "FriendlyName" => "T.C. Kimlik Doğrulama Bilgi Mesajı",
                "Type" => "textarea",
                "Size" => 25,
                "Description" => "T.C. Kimlik Numarasını doğrulamayan müşteriye gösterilecek bilgi yazısı.",
                "Default" => "Artık T.C. Kimlik doğrulaması yapmaktayız. Bu nedenle henüz T.C. Kimlik numarası doğrulaması yapmadıysanız müşteri bilgilerinizi güncellemeniz gerekmektedir.

T.C. Kimlik doğrulaması yapmayan müşterilerimiz müşteri panelinde bilgi güncellemek dışında işlem yapamazlar.",
            ],
            "via_proxy" => [
                "FriendlyName" => "Vekil Sunucu Kullan",
                "Type" => "yesno",
                "Size" => "25",
                "Description" => "T.C. Kimlik Bilgilerini APONKRAL API aracılığı ile doğrula. (Daha hızlı ve daha güvenli.)",
            ],
        ]
    ];
    return $configarray;
}

function tckimlik_activate() {

	return ['status' => 'success', 'description' => 'TC Kimlik No Dogrulama modülü başarıyla etkinleştirildi.'];

}

function tckimlik_deactivate() {

    return ['status' => 'success', 'description' => 'TC Kimlik No Dogrulama modülü başarıyla pasifleştirildi.'];

}

function tckimlik_output($vars) {
$tckimlik_config = tckimlik_config();

	$module_name = $tckimlik_config['name'];
	$module_description = $tckimlik_config['description'];
	$module_author = $tckimlik_config['author'];
	$author_link = $tckimlik_config['link'];
	
    $version = $vars['version'];

function update_check($version) {
if(function_exists('curl_exec')) {
	
	$curl = curl_init();
    $error = [];

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://raw.githubusercontent.com/aponkral/whmcs-tckimlik/master/version.txt",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 5,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_HTTPHEADER => [
        "content-type: text/plain; charset=utf-8",
		"user-agent: APONKRAL.APPS/WHMCS-T.C.Kimlik.Dogrulama",
      ],
    ]);
    $currentversion = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($currentversion)
    {
        
	if($version == $currentversion)
		return "<p style=\"color: #4CAF50;\">T.C. Kimlik No Doğrulama modülü güncel.</p>";
	else
		return "<p style=\"color: #F44336;\">T.C. Kimlik No Doğrulama modülü güncel değil! (<i style=\"color: #607D8B;\">Güncel sürüm: " . $currentversion . "</i>)</p><p style=\"color: #616161;\">Modülü güncellemek istiyorsanız <a href=\"https://github.com/aponkral/whmcs-tckimlik\" target=\"_blank\" title=\"WHMCS T.C. Kimlik Numarası doğrulama modülü\" style=\"color: #2196F3;\">GitHub'dan</a> Modülü indirerek WHMCS ana dizinininden <strong>modules/addons/</strong> klasörüne yükleyin.</p><p style=\"color: #424242;\">Lütfen dosyaları güncelledikten sonra bu sayfaya tekrar bakın.</p>";
    }

    if ($err)
    {
		return "<p style=\"color: #F44336;\">GitHub Raw Sunucusu ile bağlantı kurulamıyor. Lütfen daha sonra tekrar deneyiniz.</p>";
    }

} else {
		return "<p style=\"color: #F44336;\">API Sunucusu ile bağlantı kurulması için sunucunuzda <i>curl_exec</i> fonksiyonunun aktif olması gerekir.</p>";
}
}

$is_module_up_to_date = update_check($version);

echo "<table class=\"table table-bordered\">
				<tbody>
					<tr>
						<td><b style=\"color: #212121;\">Modül adı</b></td>
						<td>" . $module_name . "</td>
					</tr>
					<tr>
						<td><b style=\"color: #212121;\">Modül açıklaması</b></td>
						<td>" . $module_description . "</td>
					</tr>
					<tr>
						<td><b style=\"color: #212121;\">Modül sürümü</b></td>
						<td>" . $version . "</td>
					</tr>
					<tr>
						<td><b style=\"color: #212121;\">Modülü geliştiren</b></td>
						<td><a href=\"" . $author_link . "\" target=\"_blank\" title=\"APONKRAL Blog\" style=\"color: #2196F3;\">" . $module_author . "</a></td>
					</tr>
					<tr>
						<td class=\"text-center\" colspan=\"2\">" . $is_module_up_to_date . "</td>
					</tr>
				</tbody>
			</table>";

echo "<br /></div>";

}

function tckimlik_clientarea($vars) {
$conf = get_module_conf();
$verification_about = $conf["verification_about"];

global $CONFIG;
$clientarea_details_link = $CONFIG['SystemURL'] . "/" . "clientarea.php?action=details";

	$modulelink = $vars['modulelink'];

if($_GET['page'] == "verification_about") {
	return [
		'pagetitle' => 'TC Kimlik',
		'breadcrumb' => [$modulelink=>'TC Kimlik'],
		'templatefile' => 'templates/verificationabout',
		'requirelogin' => false, # accepts true/false
		'forcessl' => false, # accepts true/false
		'vars' => [
			'description' => $verification_about,
			'clientarea_details_link' => $clientarea_details_link,
		],
	];
}
else {
$tckimlik_config = tckimlik_config();

	$module_name = $tckimlik_config['name'];
	$module_description = $tckimlik_config['description'];
	$author_name = $tckimlik_config['author'];
	$author_link = $tckimlik_config['link'];

return [
		'pagetitle' => 'TC Kimlik',
		'breadcrumb' => [$modulelink=>'TC Kimlik'],
		'templatefile' => 'templates/index',
		'requirelogin' => false, # accepts true/false
		'forcessl' => false, # accepts true/false
		'vars' => [
			'module_name' => $module_name,
			'module_description' => $module_description,
			'author_name' => $author_name,
			'author_link' => $author_link,
		],
	];
}
}