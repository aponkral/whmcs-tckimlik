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
$admin_user = $conf["whmcs_admin_user"];
$error_message = $conf["error_message"];

add_hook('ClientDetailsValidation', 1, function ($vars) use ($tc_field, $birthyear_field, $country_check, $error_message)
{
    if ($_SERVER["SCRIPT_NAME"] == '/creditcard.php')
    {
        return;
    }

    if (isset($vars["save"]))
    {
        $user_details = find_user_details($vars["email"]);

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

    if (($country_check == "on" && $vars["country"] == "TR") || $country_check == "")
    {
        if (empty($form_tckimlik) || empty($form_birthyear))
        {
            $error[] = "TC Kimlik Numaranız veya doğum tarihi alanını doldurmadınız.";
            return $error;
        }

        $validation = validate_tc($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message);
        logModuleCall('tckimlik','validation',array($form_tckimlik, $form_birthyear, $vars["firstname"], $vars["lastname"], $error_message), $validation, $validationn);

        if ($validation !== true)
        {
            return $validation;
        }
    }
});

add_hook('CustomFieldSave', 1, function($vars) use ($tc_field, $admin_user)
{
    //Check that the fieldid is one you wish to override
    if ($vars['fieldid'] == $tc_field)
    {
        $str["password2"] = $vars["value"];
        $command = "encryptpassword";
        $result = localAPI($command, $str, $admin_user);
        return array('value' => $result["password"]);
    }
    return array('value' => $vars["value"]);
});

add_hook('CustomFieldLoad', 1, function($vars) use ($tc_field, $admin_user)
{
    //Check that the fieldid is one you wish to override
    if ($vars['fieldid'] == $tc_field)
    {
        $str["password2"] = $vars["value"];
        $command = "decryptpassword";
        $result = localAPI($command, $str, $admin_user);
        return array('value' => $result["password"]);
    }
    return array('value' => $vars["value"]);
});

add_hook('ClientAreaPage', 99, function($vars) use ($tc_field, $admin_user)
{
    if ($vars["SCRIPT_NAME"] != "/viewinvoice.php")
    {
        return true;
    }
    $return_value = array();
    //Check if the fieldid is one you wish to override
    foreach ($vars["customfields"] as $key => $customfield) {
        if ($customfield["id"] == $tc_field)
        {
            $str["password2"] = $customfield["value"];
            $command = "decryptpassword";
            $result = localAPI($command, $str, $admin_user);
            if ($result["password"] != "")
            {
                $return_value = array("customfields" => (array($key => array("id" => $tc_field,
                                        "fieldname" => $customfield["fieldname"],
                                        "value" => $result["password"]))));
            } else {
                array_push($return_value, array("customfields" => array($customfield)));
            }
        } else {
            array_push($return_value, array("customfields" => array($customfield)));
        }
    }
    return $return_value;
});

?>