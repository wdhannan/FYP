@component('mail::message')
# Welcome to Smart Child Care System

Hello {{ $fullName }},

Your {{ ucfirst($role) }} account has been successfully created in the Smart Child Care System.

## Your Login Credentials

**Login User ID:** {{ $staffID }}  
**Email:** {{ $email }}  
**Temporary Password:** `{{ $temporaryPassword }}`

## Important Instructions

1. **Log in to the system** using your Staff ID and the temporary password provided above.
2. **Change your password immediately** after your first login for security purposes.
3. If you have any questions or need assistance, please contact the system administrator.

## System Access

You can access the system at: {{ config('app.url') }}

@component('mail::button', ['url' => config('app.url') . '/login'])
Login to System
@endcomponent

Thank you for being part of the Smart Child Care team!

Best regards,  
**Smart Child Care Administration**

---

<small>This is an automated email. Please do not reply to this message.</small>
@endcomponent

