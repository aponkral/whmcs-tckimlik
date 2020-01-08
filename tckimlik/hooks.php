<?php
/**
	* WHMCS T.C. Kimlik Doğrulama Modülü
	*
	* Turkish: WHMCS için T.C. Kimlik numarası doğrulama modülü.
	* English: Turkish Identity Number (TIN) verification module for WHMCS.
	* Version: 1.2.5 (1.2.5release.1)
	* BuildId: 20200108.001
	* Build Date: 08 Jan 2020
	* Email: bilgi[@]aponkral.net
	* Website: https://aponkral.net
	* 
	*
	* @license Apache License 2.0
	*/
// Her şeyi sana yazdım!.. Her şeye seni yazdım!.. * Sena AÇIK

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly. This module was made by APONKRAL.");
exit();
}

require_once('helpers.php');
use Illuminate\Database\Capsule\Manager as Capsule;

// Get the module config
$conf = tckimlik_get_module_conf();
$tc_field = $conf["tc_field"];
$birthyear_field = $conf["birthyear_field"];
$verification_status_field = $conf["verification_status_field"];
$country_check = $conf["only_turkish"];
$unique_identity = $conf["unique_identity"];
$verification_status_control = $conf["verification_status_control"];
$identity_change_protection = $conf["identity_change_protection"];
$support_ticket_access = $conf["support_ticket_access"];
$unique_identity_message = $conf["unique_identity_message"];
$error_message = $conf["error_message"];
$verification_about = $conf["verification_about"];
$verification_about_link_name = $conf["verification_about_link_name"];
$identity_change_protection_message = $conf["identity_change_protection_message"];
$via_proxy = $conf["via_proxy"];

add_hook('ClientDetailsValidation', 1, function ($vars) use ($tc_field, $birthyear_field, $verification_status_field, $country_check, $unique_identity, $verification_status_control, $identity_change_protection, $unique_identity_message, $error_message, $identity_change_protection_message,$via_proxy)
{
    if ($_SERVER["SCRIPT_NAME"] == '/creditcard.php')
    {
        return;
    }

    if (isset($vars["save"]))
    {
        $user_details = tckimlik_find_user_details($vars["email"]);
        
		if(isset($user_details["id"]) && !empty($user_details["id"]) && is_numeric($user_details["id"]))
			$vars["userid"] = $user_details["id"];
		else
			$vars["userid"] = $_SESSION['uid'];

        if (!isset($vars["firstname"]))
        {
            $vars["firstname"] = $user_details["firstname"];
        }

        if (!isset($vars["lastname"]))
        {
            $vars["lastname"] = $user_details["lastname"];
        }
    }

	$user_id = $vars["userid"];

    // Get the custom fields from vars
    $form_tckimlik = $vars["customfield"][$tc_field];
    $form_birthyear = $vars["customfield"][$birthyear_field];
    $form_verification_status = $vars["customfield"][$verification_status_field];

    if (($country_check == "on" && $vars["country"] == "TR") || $country_check == "")
    {
        if (empty($form_tckimlik) || empty($form_birthyear))
        {
            $error[] = "TC Kimlik Numaranız veya doğum tarihi alanını doldurmadınız.";
            return $error;
        }
        
        if (!is_int(intval($form_tckimlik)) || strlen($form_tckimlik) < 11 || strlen($form_tckimlik) > 11)
        {
            $error[] = "TC Kimlik Numaranız 11 basamaklı bir sayı olmalıdır.";
            return $error;
        }
        
        if (!is_int(intval($form_birthyear)))
        {
            $error[] = "Doğum Yılınız geçerli bir tamsayı değildir.";
            return $error;
        }

// Kimlik Değişiklik Koruması Başladı
if ($identity_change_protection == "on")
{
	if (identity_change_check($user_id, $tc_field, $birthyear_field, $verification_status_field, $form_tckimlik, $vars["firstname"], $vars["lastname"], $form_birthyear))
	{
		$error[] = $identity_change_protection_message;
		return $error;
	}
}
// Kimlik Değişiklik Koruması Bitti

		if($unique_identity == "on")
		{
		
		function validate_unique_identity($user_id, $tc_field, $form_tckimlik)
		{
			if(!isset($user_id) || empty($user_id) || !is_numeric($user_id))
			$user_id = 0;
			
			$check_unique_identity = Capsule::table('tblcustomfieldsvalues')
				->where('relid', '!=', $user_id)
				->where('fieldid', '=', $tc_field)
				->where('value', '=', $form_tckimlik)
				->count();
		
if($check_unique_identity == 0)
	return true;
else
	return false;
		}

		if(validate_unique_identity($user_id, $tc_field, $form_tckimlik) == true)
		{
		
        $validation = validate_tc($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message, $via_proxy);
        if($validation === true)
	        logModuleCall('tckimlik','validation',["tc_kimlik_numarasi"=>$form_tckimlik, "dogum_yili"=>$form_birthyear, "isim"=>$vars["firstname"], "soyisim"=>$vars["lastname"], "sonuc"=>"Başarılı", "vekil_durumu"=>($via_proxy == "on" ? "etkin" : "pasif")], $validation, $validationn);
        else
	        logModuleCall('tckimlik','validation',["tc_kimlik_numarasi"=>$form_tckimlik, "dogum_yili"=>$form_birthyear, "isim"=>$vars["firstname"], "soyisim"=>$vars["lastname"], "sonuc"=>$error_message, "vekil_durumu"=>($via_proxy == "on" ? "etkin" : "pasif")], $validation, $validationn);

		if($validation === true && $verification_status_control == "on" && $user_id > 0) {
			$check_verification_status = Capsule::table('tblcustomfieldsvalues')
					->where('relid', '=', $user_id)
					->where('fieldid', '=', $verification_status_field)
					->count();
				
if($check_verification_status == 1) {
	Capsule::table('tblcustomfieldsvalues')
			->where("relid", "=", $user_id)
			->where("fieldid", "=", $verification_status_field)
			->update(["value"=>"on", "updated_at"=>date("Y-m-d H:i:s", time())]);
}
else {
$insert_verification_status = [
	"fieldid" => $verification_status_field,
	"relid" => $user_id,
	"value" => "on",
	"created_at" => date("Y-m-d H:i:s", time()),
	"updated_at" => date("Y-m-d H:i:s", time()),
	];
	Capsule::table('tblcustomfieldsvalues')
		->insert($insert_verification_status);
}
		}
        elseif ($validation !== true)
        {
            return $validation;
        }
		}
		else
		{
			return $unique_identity_message;
		}
		}
		else
		{
			$validation = validate_tc($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message, $via_proxy);
        if($validation === true)
	        logModuleCall('tckimlik','validation',["tc_kimlik_numarasi"=>$form_tckimlik, "dogum_yili"=>$form_birthyear, "isim"=>$vars["firstname"], "soyisim"=>$vars["lastname"], "sonuc"=>"Başarılı", "vekil_durumu"=>($via_proxy == "on" ? "etkin" : "pasif")], $validation, $validationn);
        else
	        logModuleCall('tckimlik','validation',["tc_kimlik_numarasi"=>$form_tckimlik, "dogum_yili"=>$form_birthyear, "isim"=>$vars["firstname"], "soyisim"=>$vars["lastname"], "sonuc"=>$error_message, "vekil_durumu"=>($via_proxy == "on" ? "etkin" : "pasif")], $validation, $validationn);

			if ($validation !== true)
			{
            return $validation;
			}
		}
		
    }
});

add_hook('ClientAreaPage', 1, function ($vars) use ($country_check, $verification_status_field, $verification_status_control, $support_ticket_access) {
	
if($verification_status_control == "on") {
$client_id  = $_SESSION['uid'];
if (empty($client_id))
{
	return;
}

$user_country = Capsule::table('tblclients')
		->where("id", "=", $client_id)
		->value('country');
		
		if (($country_check == "on" && $user_country == "TR") || $country_check == "") {

global $CONFIG;
$URL = $CONFIG['SystemURL'] . "/" . "index.php?m=tckimlik&page=verification_about";

$user_verification = Capsule::table('tblcustomfieldsvalues')
		->where("relid", "=", $client_id)
		->where("fieldid", "=", $verification_status_field)
		->value('value');
	
if($user_verification != "on") {
	$filename = $vars['filename'];
if($support_ticket_access == "on" && ($filename == "supporttickets" || $filename == "submitticket" || $filename == "viewticket"))
	$support_tickets_access = true;
else
	$support_tickets_access = false;
	
if (($filename == "clientarea" && $_GET['action'] == "details") || ($filename == "index" && $_GET['m'] == "tckimlik") || $support_tickets_access) {

}
else {
	header("Location: " . $URL);
	exit;
}
}
}
}
});