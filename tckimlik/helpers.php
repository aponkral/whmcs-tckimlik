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

// use WHMCS (Laravel) db functions
use Illuminate\Database\Capsule\Manager as Capsule;

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

function tckimlik_find_user_details($email)
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

function tckimlik_get_custom_fields()
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

function tckimlik_get_module_conf()
{
    $retVal = [];
    $exclude_fields = ['version', 'access',];
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

function tckimlik_strtouppertr($str)
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

function identity_change_check($userid, $tin_field, $birthyear_field, $verification_status_field, $form_tin, $form_name, $form_surname, $form_birthyear)
{
	$verification_status_count = Capsule::table('tblcustomfieldsvalues')->where('relid', '=', $userid)->where('fieldid', '=', $verification_status_field)->where('value', '=', "on")->count();

	if ($verification_status_count != 0)
	{
		$tin_value = Capsule::table('tblcustomfieldsvalues')->where('relid', '=', $userid)->where('fieldid', '=', $tin_field)->value('value');
		if ($tin_value != $form_tin)
			return true;

		$name_value = Capsule::table('tblclients')->where("id", "=", $userid)->value('firstname');
		if ($name_value != $form_name)
			return true;

		$surname_value = Capsule::table('tblclients')->where("id", "=", $userid)->value('lastname');
		if ($surname_value != $form_surname)
			return true;

		$birthyear_value = Capsule::table('tblcustomfieldsvalues')->where('relid', '=', $userid)->where('fieldid', '=', $birthyear_field)->value('value');
		if ($birthyear_value != $form_birthyear)
			return true;
	}
	else
		return false;
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
	[
	'options' => [
	'min_range' => 10000000000,
	'max_range' => 99999999999,
	'default' => FALSE
	]
	]
	) == $tc && filter_var(
	$year,
	FILTER_VALIDATE_INT,
	[
	'options' => [
	'min_range' => 1900,
	'max_range' => date("Y"),
	'default' => FALSE
	]
	]
	) == $year) {

function isTcKimlik($tin)
{			
	if (strlen($tin) == 11)
	{
		$digit = str_split($tin);
		$digit10_test=fmod(($digit[0] + $digit[2] + $digit[4] + $digit[6] + $digit[8]) * 7  - ($digit[1] + $digit[3] + $digit[5] + $digit[7]), 10);
		$digit11_test = fmod($digit[0] + $digit[1] + $digit[2] + $digit[3] + $digit[4] + $digit[5] + $digit[6] + $digit[7] + $digit[8] + $digit[9], 10);
	}

	if (strlen($tin) != 11)
		{
			$sonuc=false;
		}
	elseif ($digit[0] == 0)
		{
			$sonuc=false;
		}
	elseif (!is_numeric($digit[0]) or !is_numeric($digit[1]) or !is_numeric($digit[2]) or  !is_numeric($digit[3]) or !is_numeric($digit[4]) or !is_numeric($digit[5]) or !is_numeric($digit[6]) or !is_numeric($digit[7]) or !is_numeric($digit[8]) or  !is_numeric($digit[9]) or !is_numeric($digit[10]))
	{
		$sonuc=false;		
	}
	elseif ($digit10_test != $digit[9])
	{
		$sonuc=false;
	}
	elseif ($digit11_test != $digit[10])
	{
		$sonuc=false;
	}
	else
	{
		$sonuc=true;
	}
	return $sonuc;	
}

if(isTcKimlik($tc)) {

function name_validation($name) {
if(preg_match("/^([a-zA-ZöçşığüÖÇŞİĞÜ' ]+)$/",$name))
	return true;
else
 	return false;
}
if (name_validation($name) === false || name_validation($surname) === false) {
	return "İsim veya soyisim geçersiz";
}

	
	if( $via_proxy == "on" ) {
    
    if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_getinfo') && function_exists('curl_error') && function_exists('curl_close')) {

$tckimlik_api_error_messages = [
1001 => "Geçerli bir json verisi gönderilmedi (hata kodu: 1001)",
1002 => "Json parametreleri eksik (hata kodu: 1002)",
1003 => "T.C. kimlik numarası standartlara uymamaktadır.",
1004 => "T.C. kimlik numarası geçerli bir sayı değil ve doğum yılı 1900 veya daha fazla olabilir.",
1005 => "İsim veya soyisim geçersiz",
2001 => "NVI Api geçerli bir sonuç vermedi. (hata kodu: 2001)",
5001 => "Curl işlevinde bir sorun var. Bu hatayı görürseniz, lütfen bize ulaşın. (hata kodu 5001)",
5002 => "Curl_exec işlevinde bir sorun var. Bu hatayı görürseniz, lütfen bize ulaşın. (hata kodu: 5002)",
5003 => "Api sunucusunda önbelleğe yazma hatasıdır. Bu hatayı görürseniz, lütfen bize ulaşın. (hata kodu 5003)",
5004 => "Api sunucusunda önbellekten okuma hatasıdır. Bu hatayı görürseniz, lütfen bize ulaşın. (hata kodu 5004)"
];
	
	$curl = curl_init();
    $error = [];

    // Convert name and surname to uppercase and year to an int value
    $name = tckimlik_strtouppertr($name);
    $surname = tckimlik_strtouppertr($surname);
    $year = intval($year);
    
    $tckimlik_apiurl = "https://api.aponkral.com/tckimlik-api/v1.1/";
    $tckimlik_api_data = json_encode(["tin" => $tc, "name" => $name, "surname" => $surname, "birthyear" => $year]);

    curl_setopt_array($curl, [
      CURLOPT_URL => $tckimlik_apiurl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_HTTPHEADER => [
        "cache-control: no-cache",
        "content-type: application/json; charset=utf-8",
        "user-agent: APONKRAL.APPS/WHMCS-T.C.Kimlik.Dogrulama",
        "api-connecting-host: " . $_SERVER['HTTP_HOST'],
      ],
      CURLOPT_POSTFIELDS => $tckimlik_api_data,
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

	if ($response)
	{
		if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
			$error[] = "Api geçerli bir json verisi ile yanıt vermedi. Lütfen modül geliştiricisiyle iletişime geçin.";
		} else {
		$response = json_decode($response, true);
		if ($response['status'] == "success") {
			if ($response['verification'] == "true")
			{
				return true;
			} elseif ($response['verification'] == "false") {
				$error[] = $error_message;
			} else {
				$error[] = $error_message;
			}
		}
		else if ($response['status'] == "error") {
		$error[] = "T.C. Kimlik Doğrulama: " . $tckimlik_api_error_messages[$response['error_code']];
		} else {
		$error[] = "T.C. Kimlik Doğrulama: Bilinmeyen bir hata oluştu.";
		}
		}
	}

    if ($err)
    {
        $error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulamıyor. Lütfen daha sonra tekrar deneyiniz.";
    }
    
    } else {
    	$error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulması için sunucunuzda <i>CURL</i> fonksiyonlarının aktif olması gerekir.";
    }
    
    } else {
    
    if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_getinfo') && function_exists('curl_error') && function_exists('curl_close')) {
	
	$curl = curl_init();
    $error = [];

    // Convert name and surname to uppercase and year to an int value
    $name = tckimlik_strtouppertr($name);
    $surname = tckimlik_strtouppertr($surname);
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
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $request,
      CURLOPT_HTTPHEADER => [
        "cache-control: no-cache",
        "content-type: application/soap+xml; charset=utf-8",
        "user-agent: APONKRAL.APPS/WHMCS-T.C.Kimlik.Dogrulama",
      ],
    ]);
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
    	$error[] = "<b>TC Kimlik No Dogrulama:</b> API Sunucusu ile bağlantı kurulması için sunucunuzda <i>CURL</i> fonksiyonlarının aktif olması gerekir.";
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