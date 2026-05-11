import { useGSAP } from '@gsap/react';
import { Form, usePage } from '@inertiajs/react';
import { MessageCircle } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import storeContactSubmission from '@/actions/App/Http/Controllers/StoreContactSubmissionController';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { gsap } from '@/lib/animations/gsap-setup';
import { cn } from '@/lib/utils';
import type { ContactWaitlistSectionSettings } from '@/types/landing';

type FlashProps = {
    flash?: {
        success?: boolean | string | null;
    };
};

const labelClass =
    'font-arabic mb-1.5 block text-sm font-semibold text-white sm:text-[15px]';

const inputShell =
    'rounded-xl border border-white/12 bg-black text-white placeholder:text-white/38 shadow-inner shadow-black/50 transition-[border-color,box-shadow] hover:border-white/18';

const DEFAULT_HEADING = 'تواصل معنا';

const DEFAULT_DESCRIPTION =
    'سجل الآن في قائمة الانتظار .. وهيتم التواصل معاك بمجرد فتح الباب للتسجيل في النسخة الجديدة';

const DEFAULT_SUBMIT_BUTTON = 'إرسال الطلب';

interface ContactWaitlistSectionProps {
    data?: ContactWaitlistSectionSettings | null;
}

export default function ContactWaitlistSection({ data }: ContactWaitlistSectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const [preferWa, setPreferWa] = useState(true);
    const flash = usePage<FlashProps>().props.flash;

    useEffect(() => {
        if (flash?.success) {
            setPreferWa(true);
        }
    }, [flash?.success]);

    const heading = (data?.heading ?? '').trim() || DEFAULT_HEADING;
    const description = (data?.description ?? '').trim() || DEFAULT_DESCRIPTION;
    const submitButtonText = (data?.submit_button_text ?? '').trim() || DEFAULT_SUBMIT_BUTTON;

    useGSAP(
        () => {
            gsap.fromTo(
                sectionRef.current?.querySelectorAll('.reveal-contact') ?? [],
                { opacity: 0, y: 36 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.85,
                    stagger: 0.08,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: sectionRef.current,
                        start: 'top 82%',
                        once: true,
                    },
                },
            );
        },
        { scope: sectionRef },
    );

    return (
        <section
            ref={sectionRef}
            id="contact-waitlist"
            className="scroll-mt-24 bg-black py-14 sm:scroll-mt-28 sm:py-16 md:py-20 lg:py-24"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 lg:px-10 xl:px-12 2xl:px-16">
                <div
                    className={cn(
                        'reveal-contact mx-auto w-full max-w-7xl rounded-3xl',
                        'border border-white/[0.12] bg-[#0a0a0a] p-6 sm:p-8 md:p-10',
                        'shadow-[inset_0_1px_0_0_rgba(255,255,255,0.04)]',
                    )}
                >
                    <header dir="rtl" className="flex flex-col gap-3 text-center">
                        <h2 className="font-display font-semibold tracking-wide text-[#D4A853] sm:text-lg md:text-xl">
                            {heading}
                        </h2>
                        <p className="font-arabic mx-auto w-full px-1 whitespace-pre-line text-[15px] leading-[1.75] text-white/88 sm:text-base md:text-lg">
                            {description}
                        </p>
                    </header>

                    {flash?.success ? (
                        <div
                            dir="rtl"
                            className="font-arabic reveal-contact mt-8 rounded-2xl border border-[#25D366]/35 bg-[#25D366]/[0.07] px-4 py-3.5 text-center text-[15px] leading-relaxed text-white sm:text-base"
                            role="status"
                        >
                            تم استلام بياناتك بنجاح. سنُتواصل معاك قريبًا إن شاء الله.
                        </div>
                    ) : null}

                    <Form
                        action={storeContactSubmission.url()}
                        method="post"
                        disableWhileProcessing
                        resetOnSuccess
                        options={{ preserveScroll: true }}
                        className="reveal-contact mt-10 flex flex-col gap-8"
                    >
                        {({ errors, processing }) => (
                            <>
                                <div dir="rtl" className="grid gap-8">
                                    <div className="grid gap-0">
                                        <Label htmlFor="contact-name" className={labelClass}>
                                            الاسم
                                        </Label>
                                        <Input
                                            id="contact-name"
                                            name="name"
                                            autoComplete="name"
                                            required
                                            aria-invalid={Boolean(errors.name)}
                                            placeholder="الاسم بالكامل"
                                            className={cn(
                                                'font-arabic h-12 text-base md:h-[3.25rem] md:text-[17px]',
                                                inputShell,
                                                'focus-visible:border-[#D4A853]/55 focus-visible:ring-[#D4A853]/22',
                                                'aria-invalid:border-[#F02624]/60 aria-invalid:ring-[#F02624]/15',
                                            )}
                                        />
                                        {errors.name ? (
                                            <p className="font-arabic mt-1.5 text-[13px] text-[#FF6B6B]" role="alert">
                                                {errors.name}
                                            </p>
                                        ) : null}
                                    </div>

                                    <div
                                        dir="rtl"
                                        className="grid gap-8 lg:grid-cols-2 lg:gap-x-8 lg:gap-y-2"
                                    >
                                        <div className="grid min-w-0 content-start gap-0">
                                            <Label htmlFor="contact-phone" className={labelClass}>
                                                الموبايل (واتساب)
                                            </Label>
                                            <Input
                                                id="contact-phone"
                                                name="phone"
                                                type="tel"
                                                inputMode="tel"
                                                autoComplete="tel"
                                                required
                                                dir="ltr"
                                                aria-invalid={Boolean(errors.phone)}
                                                placeholder="01012345678"
                                                className={cn(
                                                    'text-end h-12 text-base tabular-nums md:h-[3.25rem]',
                                                    inputShell,
                                                    'focus-visible:border-[#D4A853]/55 focus-visible:ring-[#D4A853]/22',
                                                    'aria-invalid:border-[#F02624]/60 aria-invalid:ring-[#F02624]/15',
                                                )}
                                            />
                                            <p className="font-arabic mt-2 text-[12px] leading-relaxed text-white/55 sm:text-[13px]">
                                                اكتب رقم الواتساب اللي نقدر نتواصل معاك عليه.
                                            </p>
                                            {errors.phone ? (
                                                <p className="font-arabic mt-1 text-[13px] text-[#FF6B6B]" role="alert">
                                                    {errors.phone}
                                                </p>
                                            ) : null}
                                        </div>

                                        <div className="grid min-w-0 content-start gap-0">
                                            <Label htmlFor="contact-email" className={labelClass}>
                                                البريد الإلكتروني
                                            </Label>
                                            <Input
                                                id="contact-email"
                                                name="email"
                                                type="email"
                                                inputMode="email"
                                                autoComplete="email"
                                                required
                                                dir="ltr"
                                                aria-invalid={Boolean(errors.email)}
                                                placeholder="name@example.com"
                                                className={cn(
                                                    'font-arabic text-end h-12 text-base md:h-[3.25rem]',
                                                    inputShell,
                                                    'focus-visible:border-[#D4A853]/55 focus-visible:ring-[#D4A853]/22',
                                                    'aria-invalid:border-[#F02624]/60 aria-invalid:ring-[#F02624]/15',
                                                )}
                                            />
                                            {errors.email ? (
                                                <p className="font-arabic mt-1.5 text-[13px] text-[#FF6B6B]" role="alert">
                                                    {errors.email}
                                                </p>
                                            ) : null}
                                        </div>
                                    </div>

                                    <div className="grid gap-0">
                                        <Label htmlFor="contact-message" className={labelClass}>
                                            الرسالة
                                        </Label>
                                        <Textarea
                                            id="contact-message"
                                            name="message"
                                            required
                                            rows={5}
                                            aria-invalid={Boolean(errors.message)}
                                            placeholder="اكتب رسالتك أو أي سؤال عندك…"
                                            className={cn(
                                                'font-arabic min-h-36 text-base md:min-h-40 md:text-[17px] leading-relaxed',
                                                inputShell,
                                                'px-3 py-3',
                                                'focus-visible:border-[#D4A853]/55 focus-visible:ring-[#D4A853]/22',
                                                'aria-invalid:border-[#F02624]/60 aria-invalid:ring-[#F02624]/15',
                                            )}
                                        />
                                        {errors.message ? (
                                            <p className="font-arabic mt-1.5 text-[13px] text-[#FF6B6B]" role="alert">
                                                {errors.message}
                                            </p>
                                        ) : null}
                                    </div>
                                </div>

                                <input type="hidden" name="prefers_whatsapp" value={preferWa ? '1' : '0'} />

                                <label
                                    dir="rtl"
                                    className={cn(
                                        'flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-3.5 transition-colors sm:items-center sm:gap-4',
                                        'border-white/12 bg-black hover:border-white/18',
                                        preferWa && 'border-[#D4A853]/38 bg-[#D4A853]/[0.04]',
                                    )}
                                >
                                    <Checkbox
                                        id="contact-wa"
                                        checked={preferWa}
                                        onCheckedChange={(v) => setPreferWa(v === true)}
                                        className={cn(
                                            'mt-0.5 shrink-0 border-white/35',
                                            'data-[state=checked]:border-[#F02624]',
                                            'data-[state=checked]:bg-[#F02624]',
                                            'data-[state=checked]:text-white',
                                            'focus-visible:ring-[#D4A853]/35',
                                            'sm:mt-0',
                                        )}
                                        aria-labelledby="contact-wa-text"
                                    />
                                    <span
                                        id="contact-wa-text"
                                        className="font-arabic flex flex-1 flex-col gap-1.5 text-[14px] leading-relaxed text-white/88 select-none sm:flex-row sm:items-center sm:gap-3 sm:text-[15px]"
                                    >
                                        <MessageCircle
                                            className="size-5 shrink-0 text-[#25D366] opacity-[0.92]"
                                            aria-hidden
                                        />
                                        أرغب في التواصل عبر واتساب على الرقم الموضَّح أعلاه
                                    </span>
                                </label>

                                <div className="flex w-full justify-center" dir="rtl">
                                    <Button
                                        type="submit"
                                        disabled={processing}
                                        className={cn(
                                            'font-arabic inline-flex h-auto min-h-0 cursor-pointer rounded-full',
                                            'border-0 bg-[#F02624] px-8 py-3 text-lg font-bold text-white',
                                            'shadow-none transition-all duration-300 hover:scale-105 hover:bg-[#D62839]',
                                            'sm:px-10 sm:py-3.5 sm:text-xl',
                                            'md:px-12 md:py-4 md:text-xl',
                                            'lg:px-14 lg:py-4.5 lg:text-2xl',
                                            'xl:px-16 xl:py-5 xl:text-2xl',
                                            'focus-visible:border-transparent focus-visible:ring-[3px]',
                                            'focus-visible:ring-[#D4A853]/35',
                                            'disabled:cursor-not-allowed disabled:pointer-events-none',
                                            'disabled:opacity-55 disabled:hover:scale-100',
                                        )}
                                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                                        dir="rtl"
                                    >
                                        {processing ? 'جاري الإرسال…' : submitButtonText}
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </section>
    );
}
