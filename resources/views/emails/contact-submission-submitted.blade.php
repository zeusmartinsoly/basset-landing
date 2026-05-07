<x-mail::message>
# رسالة تواصل جديدة

@if($submission->prefers_whatsapp)
<x-mail::panel>
يُفضل التواصل عبر واتساب على الرقم المُرسَل.
</x-mail::panel>
@endif

**الاسم:** {{ $submission->name }}

**البريد الإلكتروني:** {{ $submission->email }}

**الموبايل:** {{ $submission->phone }}

**وقت الإرسال:** {{ $submission->created_at->toDateTimeString() }}

@if($submission->ip_address)
**عنوان IP:** {{ $submission->ip_address }}

@endif

**الرسالة**

{{ $submission->message }}

@if($submission->user_agent)
<x-mail::subcopy>
User-Agent: {{ \Illuminate\Support\Str::limit((string) $submission->user_agent, 280) }}
</x-mail::subcopy>
@endif

شكرًا،<br>
{{ config('app.name') }}
</x-mail::message>
