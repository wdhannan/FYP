@component('mail::message')
# Welcome to Digital Child Health Record System

Hello {{ $parentName }},

Your child **{{ $childName }}** has been successfully registered in the Digital Child Health Record System.

## Your Login Credentials

**Login User ID:** {{ $parentID }}  
**Email:** {{ $email }}  
**Temporary Password:** `{{ $temporaryPassword }}`

## Important Instructions

1. **Log in to the system** using your Parent ID and the temporary password provided above.
2. **Change your password immediately** after your first login for security purposes.
3. You can view your child's health records, appointment history, and more through the system.
4. If you have any questions or need assistance, please contact the clinic.

## System Access

You can access the system at: {{ config('app.url') }}

@component('mail::button', ['url' => config('app.url') . '/login'])
Login to System
@endcomponent

Thank you for using the Digital Child Health Record System!

Best regards,  
**Digital Child Health Record System Administration**

---

<small>This is an automated email. Please do not reply to this message.</small>
@endcomponent

