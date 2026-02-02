<x-mail::message>
# New User Registration

A new user has registered on MauzoSheet system.

**User Details:**  
- Name: {{ $user->name }}  
- Username: {{ $user->username }}  
- Email: {{ $user->email }}  
- Role: {{ $user->role }}

**Company Details:**  
- Company Name: {{ $company->company_name ?? 'N/A' }}  
- Location: {{ $company->location ?? 'N/A' }}  
- Phone: {{ $company->phone ?? 'N/A' }}  
- Region: {{ $company->region ?? 'N/A' }}

Registration Date: {{ $user->created_at->format('d/m/Y H:i') }}

<x-mail::button :url="route('admin.users.index')">
View in Admin Panel
</x-mail::button>

Thanks,<br>
MauzoSheet System
</x-mail::message>