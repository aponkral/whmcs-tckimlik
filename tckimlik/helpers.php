<?php
// *************************************************************************
// *                                                                       *
// * WHMCS TCKimlik - The Complete Turkish Identity Validation, Verify & Unique Identity Module    *
// * Copyright (c) APONKRAL. All Rights Reserved,                         *
// * Version: 1.1.9 (1.1.9release.1)                                      *
// * BuildId: 20181018.001                                                  *
// * Build Date: 18 Oct 2018                                               *
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

// use WHMCS (Laravel) db functions
use WHMCS\Database\Capsule;

/**
 *
 * Module configuration
 *
 */

/**
 *
 * Get firstname and lastname details from DB using user email
 *
 * @param $email string user email
 *
 * @return array firstname and lastname keys with first/lastname values
 */

function find_user_details($email)
{
    $retArr = [];
    $details = Capsule::table('tblclients')
                ->select('id', 'firstname', 'lastname')
                ->where('email', $email)
                ->first();

	$retArr["id"] = $details->id;
    $retArr["firstname"] = $details->firstname;
    $retArr["lastname"] = $details->lastname;

    return $retArr;
}

/**
 *
 * Get all custom field names and ids from database
 *
 * @param none
 *
 * @return array field names concat with ids - format: "id|name"
 */

function get_custom_fields()
{
    $field_names = Capsule::table('tblcustomfields')->select('fieldname', 'id')
                                                    ->get();
    $retVal = [];
    foreach ($field_names as $value) {
        array_push($retVal, $value->id . "|" . $value->fieldname);
    }
    return $retVal;
}

/**
 *
 * Return a CSV string from a PHP array
 * Taken from https://gist.github.com/johanmeiring/2894568
 *
 * @param array $csv_array An array of values
 *
 * @return string Comma seperated values of custom field names
 */

if (!function_exists('str_putcsv')) {
    function str_putcsv($input, $delimiter = ',', $enclosure = "'") {
        $fp = fopen('php://temp', 'r+b');
        fputcsv($fp, $input, $delimiter, $enclosure);
        rewind($fp);
        $data = rtrim(stream_get_contents($fp), "\n");
        fclose($fp);
        return $data;
    }
}

/**
 *
 * Get modules configuration fields for hooks
 *
 * @param none
 *
 * @return array Module configuration fields
 */

function get_module_conf()
{
    $retVal = [];
    $exclude_fields = array('version', 'access',);
    $results = Capsule::table('tbladdonmodules')->select('setting', 'value')
                                            ->where('module', 'tckimlik')
                                            ->whereNotIn('setting', $exclude_fields)
                                            ->get();
    foreach ($results as $row)
    {
        list($value, $rest) = explode("|", $row->value , 2);
        $retVal[$row->setting] = str_replace("'", "", $value);
    }
    return $retVal;
}

/**
 *
 * strtoupper function with Turkish character support. Because Turkish "i" char
 * is "İ" in upper case and mb_strtoupper doesn't know the locale and outputs "I"
 *
 * @params $str str Turkish string to convert case
 *
 * @return str
 */

function strtouppertr($str)
{
    $str = str_replace("ç", "Ç", $str);
	$str = str_replace("ğ", "Ğ", $str);
	$str = str_replace("ı", "I", $str);
	$str = str_replace("i", "İ", $str);
	$str = str_replace("ö", "Ö", $str);
	$str = str_replace("ü", "Ü", $str);
	$str = str_replace("ş", "Ş", $str);
	$str = strtoupper($str);
	$str = trim($str);
	return $str;
}

/**
 * Validate Turkish Idenfication Number from tckimlik.nvi.gov.tr
 *
 * @param $tc int Turkish Identification Number to validate
 * @param $year int Birth year of person
 * @param $name str Name of person
 * @param $surname str Surname of person
 *
 * @return boolean
 */

function validate_tc($tc, $year, $name, $surname, $error_message, $via_proxy)
{
	
	if(filter_var(
	$tc,
	FILTER_VALIDATE_INT,
	array(
	'options' => array(
	'min_range' => 10000000000,
	'max_range' => 99999999999,
	'default' => FALSE
	)
	)
	) == $tc && filter_var(
	$year,
	FILTER_VALIDATE_INT,
	array(
	'options' => array(
	'min_range' => 1900,
	'max_range' => date("Y"),
	'default' => FALSE
	)
	)
	) == $year) {

function isTcKimlik($tc)  
{  
if(strlen($tc) < 11 || strlen($tc) > 11){ return false; }  
if($tc[0] == '0'){ return false; }  
$plus = ($tc[0] + $tc[2] + $tc[4] + $tc[6] + $tc[8]) * 7;  
$minus = $plus - ($tc[1] + $tc[3] + $tc[5] + $tc[7]);  
$mod = $minus % 10;  
if($mod != $tc[9]){ return false; }  
$all = '';  
for($i = 0 ; $i < 10 ; $i++){ $all += $tc[$i]; }  
if($all % 10 != $tc[10]){ return false; }  
  
return true;  
}

if(isTcKimlik($tc)) {
	
	if( $via_proxy == "on" ) {
    
    if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_getinfo') && function_exists('curl_error') && function_exists('curl_close')) {
	
	$curl = curl_init();
    $error = [];

    // Convert name and surname to uppercase and year to an int value
    $name = strtouppertr($name);
    $surname = strtouppertr($surname);
    $year = intval($year);
    
    $apiurl = "https://api.aponkral.com/tckimlik-api/?name=" . $name . "&surname=" . $surname . "&tin=". $tc . "&birthyear=" . $year;

    curl_setopt_array($curl, array(
      CURLOPT_URL => $apiurl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: text/plain; charset=utf-8",
        "user-agent: APONKRAL.APPS/WHMCS-T.C.Kimlik.Dogrulama",
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($response)
    {
        
        if ($response == "true")
        {
            return true;
        } elseif ($response == "false") {
            $error[] = $error_message;
        } else {
            $error[] = $error_message;
        }
    }

    if ($err)
    {
        $error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulamıyor. Lütfen daha sonra tekrar deneyiniz.";
    }
    
    } else {
    	$error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulması için sunucunuzda <i>curl_exec</i> fonksiyonunun aktif olması gerekir.";
    }
    
    } else {
    
    if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_getinfo') && function_exists('curl_error') && function_exists('curl_close')) {
	
	$curl = curl_init();
    $error = [];

    // Convert name and surname to uppercase and year to an int value
    $name = strtouppertr($name);
    $surname = strtouppertr($surname);
    $year = intval($year);

    	$request = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
    <soap12:Body>
        <TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
            <TCKimlikNo>' . $tc . '</TCKimlikNo>
            <Ad>' . $name . '</Ad>
            <Soyad>' . $surname . '</Soyad>
            <DogumYili>' . $year . '</DogumYili>
        </TCKimlikNoDogrula>
    </soap12:Body>
</soap12:Envelope>';
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $request,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/soap+xml; charset=utf-8",
        "user-agent: APONKRAL.APPS/WHMCS-T.C.Kimlik.Dogrulama",
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($response)
    {

        preg_match('#<TCKimlikNoDogrulaResult>(.*?)</TCKimlikNoDogrulaResult>#', $response, $result);
        
        if ($result[1] == "true")
        {
            return true;
        } elseif ($result[1] == "false") {
            $error[] = $error_message;
        } else {
            $error[] = $error_message;
        }
    }

    if ($err)
    {
        $error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulamıyor. Lütfen daha sonra tekrar deneyiniz.";
    }
    
    } else {
    	$error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulması için sunucunuzda <i>curl_exec</i> fonksiyonunun aktif olması gerekir.";
    }
    
	}

    return $error;
} else {
$hata_mesaji = "T.C. Kimlik Numaranız T.C. Kimlik Numarası standartlarına uymamaktadır.";
$error[] = $hata_mesaji;
return $error;
}

} else {
$hata_mesaji = "T.C. Kimlik Numaranız veya Doğum Yılınız geçerli bir sayı değildir.";
$error[] = $hata_mesaji;
return $error;
}

}