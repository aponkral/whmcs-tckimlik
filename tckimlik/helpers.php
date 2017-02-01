<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
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

function find_user_details($email)
{
    $retArr = [];
    $details = Capsule::table('tblclients')
                ->select('firstname', 'lastname')
                ->where('email', $email)
                ->first();

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

function validate_tc($tc, $year, $name, $surname)
{

function isTcKimlik($tc)  
{  
if(strlen($tc) < 11){ return false; }  
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

    $error = [];

    // Convert name and surname to uppercase and year to an int value
    $name = strtouppertr($name);
    $surname = strtouppertr($surname);
    $year = intval($year);

    	$veriler = array('TCKimlikNo' => $tc, 'Ad' => $name, 'Soyad' => $surname, 'DogumYili' => $year);
			$baglan = new SoapClient('https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL');
			$response = $baglan->TCKimlikNoDogrula($veriler);
			
			$result=$response->TCKimlikNoDogrulaResult;

    if ($response)
    {

$hata_mesaji = "T.C. Kimlik Numaranız girmiş olduğunuz bilgiler ile uyuşmamaktadır.";
        
        if ($result == true)
        {
            return true;
        } elseif ($result == false) {
            $error[] = $hata_mesaji;
        } else {
            $error[] = $hata_mesaji;
        }
    }

    if ($err)
    {
        $error[] = "Sunucuyla bağlantı kurulamıyor. Lütfen daha sonra tekrar deneyiniz.";
    }

    return $error;
} else {
$hata_mesaji = "T.C. Kimlik Numaranız T.C. Kimlik Numarası standartlarına uymamaktadır.";
$error[] = $hata_mesaji;
return $error;
}

}

?>