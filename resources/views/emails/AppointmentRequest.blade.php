@component('mail::message')
# New Appointment Request

Dear Dr. {{ $doctorName }},

You have received a new appointment request that requires your approval.

## Appointment Details

**Child Name:** {{ $childName }}  
**Date:** {{ \Carbon\Carbon::parse($appointmentDate)->format('l, F j, Y') }}  
**Time:** {{ \Carbon\Carbon::parse($appointmentTime)->format('h:i A') }}  
**Appointment ID:** {{ $appointmentID }}  
**Requested by:** {{ $nurseName }}

## Action Required

Please log in to the system to **approve** or **reject** this appointment request.

@component('mail::button', ['url' => config('app.url') . '/login'])
Login to Review Appointment
@endcomponent

## Next Steps

1. Log in to your account
2. Navigate to the appointment requests section
3. Review the appointment details
4. Approve or reject the request

Thank you for your attention!

Best regards,  
**Smart Child Care Administration**

---

<small>This is an automated notification. Please do not reply to this email.</small>
@endcomponent


