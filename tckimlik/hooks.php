<?php

require_once('helpers.php');

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
exit();
}

// Get the module config
$conf = get_module_conf();
$tc_field = $conf["tc_field"];
$birthyear_field = $conf["birthyear_field"];
$country_check = $conf["only_turkish"];
$unique_identity = $conf["unique_identity"];
$unique_identity_message = $conf["unique_identity_message"];
$error_message = $conf["error_message"];

add_hook('ClientDetailsValidation', 1, function ($vars) use ($tc_field, $birthyear_field, $country_check, $unique_identity, $unique_identity_message, $error_message)
{
    if ($_SERVER["SCRIPT_NAME"] == '/creditcard.php')
    {
        return;
    }

    if (isset($vars["save"]))
    {
        $user_details = find_user_details($vars["email"]);
        
        if (!isset($vars["userid"]))
        {
            $vars["userid"] = $user_details["id"];
        }

        if (!isset($vars["firstname"]))
        {
            $vars["firstname"] = $user_details["firstname"];
        }

        if (!isset($vars["lastname"]))
        {
            $vars["lastname"] = $user_details["lastname"];
        }
    }

    // Get the custom fields from vars
    $form_tckimlik = $vars["customfield"][$tc_field];
    $form_birthyear = $vars["customfield"][$birthyear_field];

    if ($country_check == "on" && $vars["country"] == "TR")
    {
        if (empty($form_tckimlik) || empty($form_birthyear))
        {
            $error[] = "TC Kimlik Numaranız veya doğum tarihi alanını doldurmadınız.";
            return $error;
        }
		
		if($unique_identity == "on")
		{
		
		function validate_unique_identity($user_id, $tc_field, $form_tckimlik)
		{
			if(!isset($user_id) || empty($user_id) || !is_int($user_id))
			$user_id = 0;
			
			$sql_ui_count = "select COUNT(*) as total from `tblcustomfieldsvalues` where not relid=" . $user_id . " AND fieldid=" . $tc_field . " AND value='" . $form_tckimlik . "'";
			$sql_ui_count_query = mysql_query($sql_ui_count);
		
			if(mysql_fetch_assoc($sql_ui_count_query)['total'] == 0)
			return true;
			else
			return false;
		}
		
		$user_id = $vars['userid'];

		if(validate_unique_identity($user_id, $tc_field, $form_tckimlik) == true)
		{
		
        $validation = validate_tc($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message);
        logModuleCall('tckimlik','validation',array($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message), $validation, $validationn);

        if ($validation !== true)
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
			$validation = validate_tc($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message);
        logModuleCall('tckimlik','validation',array($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message), $validation, $validationn);

			if ($validation !== true)
			{
            return $validation;
			}
		}
		
    }
});

?>