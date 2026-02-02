<x-mail::message>
# Usajili Mpya wa Mtumiaji

Mtumiaji mpya amesajiliwa kwenye mfumo wa MauzoSheet.

**Taarifa za Mtumiaji:**  
- Jina: {{ $user->name }}  
- Jina la Mtumiaji: {{ $user->username }}  
- Barua Pepe: {{ $user->email }}  
- Wadhifa: {{ $user->role }}

**Taarifa za Kampuni:**  
- Jina la Kampuni: {{ $company->company_name ?? 'Haipo' }}  
- Mahali: {{ $company->location ?? 'Haipo' }}  
- Simu: {{ $company->phone ?? 'Haipo' }}  
- Mkoa: {{ $company->region ?? 'Haipo' }}

Tarehe ya Usajili: {{ $user->created_at->format('d/m/Y H:i') }}

<x-mail::button :url="route('admin.dashboard')">
Fungua Admin Dashboard
</x-mail::button>

Asante,<br>
Mfumo wa MauzoSheet
</x-mail::message>
