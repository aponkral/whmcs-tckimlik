
# whmcs-tckimlik #
WHMCS için Ücretsiz T.C. Kimlik numarası doğrulama modülü

## Minimum Gereksinimler ##

- WHMCS >= 6.0
- PHP >= 5.4.0

WHMCS 7.8.3 ve PHP 7.3 ile de testleri gerçekleştirilmiştir.

WHMCS'nin minimum gereksinimlerini görmek için https://docs.whmcs.com/System_Requirements adresine göz atabilirsiniz.

## Özellikler ##
- Açık kaynak
- T.C. Kimlik Numarası doğrulama
- Sadece Türkiye için doğrulama (opsiyonel)
- Benzersiz Kimlik (opsiyonel)
- Özelleştirilebilir bilgi mesajları
- Direkt nvi.gov.tr API ile doğrulama
- Vekil sunucu (APONKRAL API) ile doğrulama
- Kimlik doğrulaması yapmayan kullanıcıları bilgi sayfasına yönlendirme

## Kurulum ##
Projeyi herhangi bir yere klonlayabilir ya da GitHub üzerinden son sürümü indirebilirsiniz. Sürümler için [releases](https://github.com/aponkral/whmcs-tckimlik/releases) sayfasına göz atın.

#### Clone ####
Repoyu klonlayacaksanız herhangi bir yere klonladıktan sonra proje dizinine gidip tckimlik klasörünü WHMCS_dizininiz/modules/addons dizini içerisine taşımalısınız;

```
# cd whmcs-tckimlik
# mv tckimlik WHMCS_dizininiz/modules/addons/.
```

#### Son sürümü indirin (önerilen kurulum) ####
[Buradan](https://github.com/aponkral/whmcs-tckimlik/releases) son sürümü indirdikten sonra WHMCS_dizininiz/modules/addons dizinine dosyaları çıkartın.

Modülün çalışması için 2 tane "custom field" oluşturmanız gerekiyor. Bunlardan biri TC Kimlik Numarasının girilmesi, diğeri ise kullanıcının doğum yılını almak için olmalı.

Kurulumu tamamlamak için WHMCS admin sayfanızdan "Setup -> Addon Modules" sayfasına gidip modülü etkinleştirin. Etkinleştirdikten sonra "Configure" butonuna tıklayarak TC Kimlik NO ve Doğum Yılı için oluşturduğunuz "Custom Field"ları seçmelisiniz.

## Özel Müşteri Alanları ##
*T.C. Kimlik Numarası alanı;*
- Alan İsmi: T.C. Kimlik Numarası
- Alan Türü: Metin Kutusu
- Seçenekler: Sipariş formunda göster

*Doğum yılı alanı;*
- Alan İsmi: Doğum Yılı
- Alan Türü: Metin Kutusu
- Seçenekler: Sipariş formunda göster

*T.C. Kimlik doğrulama durumu alanı;*
- Alan İsmi: T.C. Kimlik Doğrulama Durumu
- Alan Türü: Onay Kutusu
- Seçenekler: Sadece Admin

## Ekran Görüntüleri ##
![Ekran görüntüsü 1](https://github.com/aponkral/whmcs-tckimlik/raw/master/screenshoots/whmcs-tckimlik-Screenshot-1.png "Ekran görüntüsü 1")

![Ekran görüntüsü 2](https://github.com/aponkral/whmcs-tckimlik/raw/master/screenshoots/whmcs-tckimlik-Screenshot-2.png "Ekran görüntüsü 2")

## Etiketler ##
- Tam Açık Kaynak Kodlu
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
- Aynı T.C. Kimlik Numarası ile kayıt olamama
- Tek T.C. Kimlik Numarası ile kayıt olma
- Benzersiz T.C. Kimlik Numarası ile kayıt olma
- Özelleştirilebilen bilgi mesajları


Bu modül [APONKRAL Apps](https://aponkral.net/apps/) tarafından tam açık kaynak kodlu olarak yayınlandığı için tüm geliştiriciler tarafından geliştirilebilir.

---

# whmcs-tckimlik #
A Turkish Identification Number validator free addon for WHMCS

## Summary ##
This module offers an official way to validate Turkish Identification Numbers (TIN) for your Turkish users. Every Turkish citizen has a private and unique TIN (Turkish Identification Numbers) and you can validate a TIN by consuming the SOAP services on https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?op=TCKimlikNoDogrula

## Minimum Requirements ##
- WHMCS >= 6.0
- PHP >= 5.4.0

Works with WHMCS 7.8.3 and PHP 7.3, too.

For the latest WHMCS minimum system requirements, please refer to
https://docs.whmcs.com/System_Requirements

## Features ##
- Open source
- Turkish Identity Number verification
- Only authentication for Turkey (optional)
- Unique Identity (optional)
- Customizable information messages
- Direct ​​Verification via nvi.gov.tr API
- Verification with proxy server (APONKRAL API)
- Forward non-authenticated users to the information page

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

## Custom Client Fields ##
*Turkish Identity Number field;*
- Field Name: Turkish Identity Number
- Field Type: Text Box
- Options: Show on order form

*Birthyear field;*
- Field Name: Birthyear
- Field Type: Text Box
- Options: Show on order form

*Turkish Identity verification status field;*
- Field Name: Turkish Identity Verification Satus
- Field Type: Tick Box
- Options: Admin Only

## Screenshoots ##
![Screenshot 1](https://github.com/aponkral/whmcs-tckimlik/raw/master/screenshoots/whmcs-tckimlik-Screenshot-1.png "Screenshot 1")

![Screenshot 2](https://github.com/aponkral/whmcs-tckimlik/raw/master/screenshoots/whmcs-tckimlik-Screenshot-2.png "Screenshot 2")

## Tags ##
- Full Open Source
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
- Unable to register with the same Turkish ID
- Registering with a Single Turkish Identity Number
- Register with a unique Turkish Identity Number
- Customizable information messages


This module can be developed by all developers as [APONKRAL Apps](https://aponkral.net/apps/) is released as full open source code.