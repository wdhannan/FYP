@component('mail::message')
# Upcoming Appointment Reminder

Dear {{ $parentName }},

This is a reminder about your child's upcoming appointment at Smart Child Care System.

## Appointment Details

**Child Name:** {{ $childName }}  
**Doctor:** {{ $doctorName }}  
**Date:** {{ \Carbon\Carbon::parse($appointmentDate)->format('l, F j, Y') }}  
**Time:** {{ \Carbon\Carbon::parse($appointmentTime)->format('h:i A') }}  
**Appointment ID:** {{ $appointmentID }}

## Important Reminders

- Please arrive **15 minutes early** for check-in
- Bring your child's health record book (if applicable)
- If you need to reschedule or cancel, please contact us at least 24 hours in advance

## Contact Information

If you have any questions or need to make changes to this appointment, please contact our clinic.

@component('mail::button', ['url' => config('app.url') . '/login'])
View Appointment Details
@endcomponent

Thank you for choosing Smart Child Care System!

Best regards,  
**Smart Child Care Administration**

---

<small>This is an automated reminder. Please do not reply to this email.</small>
@endcomponent

