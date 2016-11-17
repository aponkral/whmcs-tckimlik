
# whmcs-tckimlik #
WHMCS için Ücretsiz T.C. Kimlik numarası doğrulama modülü

## Minimum Gereksinimler ##

- WHMCS >= 6.0
- PHP >= 5.3.7
- SOAP

WHMCS 7.0 RC1 ve PHP 7.0 ile de testleri gerçekleştirilmiştir.

WHMCS'nin minimum gereksinimlerini görmek için http://docs.whmcs.com/System_Requirements adresine göz atabilirsiniz.

## Kurulum ##
Projeyi herhangi bir yere clonelayabilir ya da GitHub üzerinden son sürümü indirebilirsiniz. Sürümler için [releases](https://github.com/aponkral/whmcs-tckimlik/releases) sayfasına göz atın.

#### Clone ####
Repoyu clonelayacaksanız herhangi bir yere cloneladıktan sonra proje dizinine gidip tckimlik klasörünü WHMCS_dizininiz/modules/addons dizini içerisine taşımalısınız;

```
# cd whmcs-tckimlik
# mv tckimlik WHMCS_dizininiz/modules/addons/.
```

#### Son sürümü indirin (önerilen kurulum) ####
[Buradan](https://github.com/aponkral/whmcs-tckimlik/releases) son sürümü indirdikten sonra WHMCS_dizininiz/modules/addons dizinine dosyaları çıkartın.

Modülün çalışması için 2 tane "custom field" oluşturmanız gerekiyor. Bunlardan biri TC Kimlik Numarasının girilmesi, diğeri ise kullanıcının doğum yılını almak için olmalı.

Kurulumu tamamlamak için WHMCS admin sayfanızdan "Setup -> Addon Modules" sayfasına gidip modülü etkinleştirin. Etkinleştirdikten sonra "Configure" butonuna tıklayarak TC Kimlik NO ve Doğum Yılı için oluşturduğunuz "Custom Field"ları seçmelisiniz.

## Etiketler ##
- WHMCS
- Eklenti
- Modül
- WHMCS için T.C. kimlik numarası doğrulama Eklentisi
- WHMCS için Ücretsiz T.C. Kimlik numarası doğrulama Eklentisi
- WHMCS için T.C. Kimlik numarası doğrulama Modülü
- WHMCS için Ücretsiz T.C. Kimlik numarası doğrulama Modülü
- WHMCS için Modül
- WHMCS için Eklenti
- Ücretsiz
- Tam Açık Kaynak Kodlu


Bu modül tam açık kaynak kodlu olduğu için tüm geliştiriciler tarafından geliştirilebilir.

---

# whmcs-tckimlik #
A Turkish Identification Number validator free addon for WHMCS

## Summary ##
This module offers an official way to validate Turkish Identification Numbers (TIN) for your Turkish users. Every Turkish citizen has a private and unique TIN (TC Kimlik Numarasi) and you can validate a TIN by consuming the SOAP services on https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?op=TCKimlikNoDogrula

## Minimum Requirements ##
- WHMCS >= 6.0
- PHP >= 5.3.7
- SOAP

Works with WHMCS 7.0 and PHP 7.0, too.

For the latest WHMCS minimum system requirements, please refer to
http://docs.whmcs.com/System_Requirements

## Installation ##
You can install this module by cloning the repo or downloading the latest release from GitHub. See the [releases](https://github.com/aponkral/whmcs-tckimlik/releases) page.

#### Cloning the repo ####
Clone the repo to anywhere you like and move the "tckimlik" directory to your WHMCS modules/addons directory;

```
# cd whmcs-tckimlik
# mv tckimlik WHMCSroot/modules/addons/.
```

#### Downloading the latest release (Recommended!) ####
You can download the latest release and unzip it directly to your WHMCSroot/modules/addon directory.

Module needs two Custom Fields to be created in WHMCS. One should hold the TNI data, the other should hold the user's birth year.

To complete the installation, you should go to your WHMCS admin area and click "Activate" in your "Setup -> Addon Modules" page. Then click "Configure" and select the appropriate fields you created before.

## Tags ##
- WHMCS
- Plugin
- Module
- Turkish Identification Number validator Plugin for WHMCS
- Turkish Identification Number validator free Plugin for WHMCS
- Turkish Identification Number validator Module for WHMCS
- Turkish Identification Number validator free Module for WHMCS
- Module for WHMCS
- Plugin for WHMCS
- Free
- Full Open Source


This module is fully open source can be developed by all developers.