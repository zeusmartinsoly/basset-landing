<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'digits_between:8,22'],
            'message' => ['required', 'string', 'max:5000'],
            'prefers_whatsapp' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone');
        if (is_string($phone)) {
            $this->merge([
                'phone' => self::digitsOnlyAscii($phone),
            ]);
        }

        $wa = $this->input('prefers_whatsapp', '1');
        $this->merge([
            'prefers_whatsapp' => filter_var($wa, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'من فضلك أدخل الاسم.',
            'name.max' => 'الاسم طويل جدًا.',
            'email.required' => 'من فضلك أدخل البريد الإلكتروني.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'phone.required' => 'من فضلك أدخل رقم الموبايل.',
            'phone.digits_between' => 'رقم الموبايل قصير أو طويل جدًا؛ حاول تكتبه من غير مسافات (عربي أو إنجليزي).',
            'message.required' => 'من فضلك اكتب رسالتك.',
            'message.max' => 'الرسالة طويلة جدًا.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الموبايل',
            'message' => 'الرسالة',
            'prefers_whatsapp' => 'التفضيل للتواصل عبر واتساب',
        ];
    }

    /** Strip formatting and map Eastern Arabic / Persian numerals to Western digits before validation. */
    public static function digitsOnlyAscii(string $phone): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $easternArabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $digits = str_replace($easternArabic, $western, $phone);
        $digits = str_replace($persian, $western, $digits);

        return preg_replace('/\D/', '', $digits) ?? '';
    }
}
