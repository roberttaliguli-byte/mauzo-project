@component('mail::message')
# Habari {{ $ownerName }},

Taarifa kuhusu package yako ya **{{ $packageName }}** katika kampuni ya **{{ $companyName }}**.

@if($daysLeft <= 0)
## ⚠️ Package Yako Imekwisha!

Package yako ilimalizika tarehe **{{ $packageEndDate }}**. Kwa sasa huwezi kuendelea kutumia huduma zetu.

**Tafadhali chagua kifurushi kipya ili kuendelea na huduma.**

@component('mail::button', ['url' => $paymentUrl, 'color' => 'error'])
    Chagua Kifurushi Mpya
@endcomponent

@elseif($daysLeft <= 5)
## ⚠️ Package Yako Inakaribia Kuisha!

Package yako itaisha baada ya **siku {{ $daysLeft }}** tarehe **{{ $packageEndDate }}**.

Unaweza chagua kifurushi kipya kabla ya muda kuisha ili kuepuka usumbufu wa huduma.

@component('mail::button', ['url' => $paymentUrl, 'color' => 'warning'])
    Chagua Kifurushi Sasa
@endcomponent

@elseif($daysLeft <= 10)
## 📢 Kumbusho la Package

Package yako itaisha baada ya **siku {{ $daysLeft }}** tarehe **{{ $packageEndDate }}**.

Unaweza kuchagua kifurushi kipya mapema ili kuendelea na huduma bila usumbufu.

@component('mail::button', ['url' => $paymentUrl])
    Angalia Vifurushi
@endcomponent
@endif

### Vifurushi Vinavyopatikana:

| Kifurushi | Muda | Bei |
|:----------|:-----|:----|
| **30 days** | Mwezi 1 | TZS 15,000 |
| **180 days** | Miezi 6 | TZS 75,000 (Punguzo TZS 15,000) |
| **366 days** | Miezi 12 | TZS 150,000 (Punguzo TZS 30,000) |

@component('mail::panel')
**Faida za Kuchagua Kifurushi Mapema:**
- ✓ Hakuna usumbufu wa huduma
- ✓ Punguzo la bei kwa vifurushi vya muda mrefu
- ✓ Endelea na data zako zote
@endcomponent

Wasiliana nasi kwa:
- 📞 Simu: 0685496334 | 0614356830
- 📧 Email: mauzosheet9@gmail.com

Asante kwa kutumia huduma zetu,<br>
**{{ config('app.name') }} Team**

@component('mail::subcopy')
Kama tayari umelipa, tafadhali puuza ujumbe huu. Kama una swali lolote, wasiliana nasi.
@endcomponent
@endcomponent