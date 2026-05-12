import { useGSAP } from '@gsap/react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { TestimonialItem, TestimonialsSectionSettings } from '@/types/landing';

interface TestimonialsSectionProps {
    data?: TestimonialsSectionSettings;
}

const AUTOPLAY_MS = 5000;

export default function TestimonialsSection({ data }: TestimonialsSectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const imageRefs = useRef<(HTMLDivElement | null)[]>([]);
    const quoteRefs = useRef<(HTMLDivElement | null)[]>([]);
    const progressRef = useRef<HTMLDivElement>(null);
    const autoplayRef = useRef<ReturnType<typeof setInterval> | null>(null);
    const isAnimating = useRef(false);

    const heading = data?.heading ?? 'رأي الكامبرز';
    const items = data?.items ?? [];
    const [active, setActive] = useState(0);

    const reducedMotion =
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const animateTo = useCallback(
        (next: number) => {
            if (isAnimating.current || next === active || items.length === 0) {
                return;
            }
            isAnimating.current = true;

            const dur = reducedMotion ? 0.01 : 0.6;
            const ease = 'power3.inOut';

            const currentImg = imageRefs.current[active];
            const nextImg = imageRefs.current[next];
            const currentQuote = quoteRefs.current[active];
            const nextQuote = quoteRefs.current[next];

            const tl = gsap.timeline({
                onComplete: () => {
                    isAnimating.current = false;
                    setActive(next);
                },
            });

            if (currentImg) {
                tl.to(currentImg, { opacity: 0, scale: 1.05, duration: dur, ease }, 0);
            }
            if (currentQuote) {
                tl.to(currentQuote, { opacity: 0, y: 20, duration: dur * 0.6, ease }, 0);
            }
            if (nextImg) {
                gsap.set(nextImg, { opacity: 0, scale: 1.08 });
                tl.to(nextImg, { opacity: 1, scale: 1, duration: dur, ease }, dur * 0.3);
            }
            if (nextQuote) {
                gsap.set(nextQuote, { opacity: 0, y: 30 });
                tl.to(
                    nextQuote,
                    { opacity: 1, y: 0, duration: dur * 0.8, ease: 'power3.out' },
                    dur * 0.5,
                );
            }
        },
        [active, items.length, reducedMotion],
    );

    const goNext = useCallback(() => {
        if (items.length === 0) {
            return;
        }
        animateTo((active + 1) % items.length);
    }, [active, items.length, animateTo]);

    const goPrev = useCallback(() => {
        if (items.length === 0) {
            return;
        }
        animateTo((active - 1 + items.length) % items.length);
    }, [active, items.length, animateTo]);

    useEffect(() => {
        if (items.length <= 1) {
            return;
        }
        autoplayRef.current = setInterval(goNext, AUTOPLAY_MS);

        return () => {
            if (autoplayRef.current) {
                clearInterval(autoplayRef.current);
            }
        };
    }, [goNext, items.length]);

    const resetAutoplay = useCallback(() => {
        if (autoplayRef.current) {
            clearInterval(autoplayRef.current);
        }
        autoplayRef.current = setInterval(goNext, AUTOPLAY_MS);
    }, [goNext]);

    useGSAP(
        () => {
            const section = sectionRef.current;
            if (!section || reducedMotion) {
                return;
            }

            gsap.from(section.querySelector('[data-testimonial-heading]'), {
                scrollTrigger: { trigger: section, start: 'top 80%', once: true },
                opacity: 0,
                y: 30,
                duration: 0.7,
                ease: 'power3.out',
            });
        },
        { scope: sectionRef },
    );

    useEffect(() => {
        if (!progressRef.current || items.length <= 1) {
            return;
        }
        const bar = progressRef.current;
        bar.style.transition = 'none';
        bar.style.transform = 'scaleX(0)';

        requestAnimationFrame(() => {
            bar.style.transition = `transform ${AUTOPLAY_MS}ms linear`;
            bar.style.transform = 'scaleX(1)';
        });
    }, [active, items.length]);

    return (
        <section
            id="testimonials"
            ref={sectionRef}
            dir="rtl"
            className="relative overflow-hidden bg-[#0A0A0A] py-20 sm:py-24 md:py-28 lg:py-32 xl:py-36"
            aria-labelledby="testimonials-heading"
        >
            <div className="relative z-[1] mx-auto max-w-7xl px-4 sm:px-6 md:px-10 lg:px-16">
                <h2
                    id="testimonials-heading"
                    data-testimonial-heading
                    className="mb-12 text-3xl font-black leading-tight text-white sm:mb-14 sm:text-4xl md:mb-16 md:text-5xl lg:mb-20 lg:text-6xl"
                >
                    {heading}
                </h2>

                {items.length === 0 ? (
                    <p className="py-16 text-center text-base text-white/40">
                        لم يتم إضافة شهادات بعد.
                    </p>
                ) : (
                    <div className="flex flex-col gap-10 lg:flex-row lg:items-center lg:gap-16 xl:gap-20">
                        {/* Portrait stack */}
                        <div className="relative mx-auto aspect-[3/4] w-full max-w-[280px] shrink-0 overflow-hidden rounded-2xl sm:max-w-[320px] md:max-w-[340px] lg:mx-0 lg:max-w-[380px] xl:max-w-[420px]">
                            {items.map((item, i) => (
                                <div
                                    key={`img-${item.name}-${i}`}
                                    ref={(el) => {
                                        imageRefs.current[i] = el;
                                    }}
                                    className="absolute inset-0"
                                    style={{ opacity: i === 0 ? 1 : 0 }}
                                >
                                    <img
                                        src={`/images/${item.image}`}
                                        alt={item.name}
                                        className="h-full w-full object-cover"
                                        loading={i === 0 ? 'eager' : 'lazy'}
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-[#0A0A0A]/60 via-transparent to-transparent" />
                                </div>
                            ))}

                            {/* Progress bar on image */}
                            {items.length > 1 && (
                                <div className="absolute inset-x-0 bottom-0 h-[3px] bg-white/10">
                                    <div
                                        ref={progressRef}
                                        className="h-full origin-right bg-[#F02624]"
                                        style={{ transform: 'scaleX(0)' }}
                                    />
                                </div>
                            )}
                        </div>

                        {/* Quote + controls */}
                        <div className="flex min-h-[260px] flex-1 flex-col justify-between sm:min-h-[280px] lg:min-h-[360px]">
                            {/* Quote stack */}
                            <div className="relative flex-1">
                                {items.map((item, i) => (
                                    <div
                                        key={`quote-${item.name}-${i}`}
                                        ref={(el) => {
                                            quoteRefs.current[i] = el;
                                        }}
                                        className={`${i === 0 ? '' : 'absolute inset-0'}`}
                                        style={{ opacity: i === 0 ? 1 : 0 }}
                                    >
                                        <span
                                            className="mb-4 block font-serif text-6xl leading-none text-[#F02624]/30 select-none sm:text-7xl lg:text-8xl"
                                            aria-hidden
                                        >
                                            "
                                        </span>
                                        <blockquote className="mb-6 whitespace-pre-line text-lg leading-[1.9] font-medium text-white/85 sm:text-xl md:text-2xl lg:mb-8 lg:text-2xl xl:text-[1.7rem]">
                                            {item.content}
                                        </blockquote>
                                        <div className="flex items-center gap-3">
                                            <div className="h-px w-8 bg-[#F02624]" />
                                            <span className="text-base font-black text-white sm:text-lg">
                                                {item.name}
                                            </span>
                                            {item.course && (
                                                <span className="text-sm text-white/35">
                                                    {item.course}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Navigation */}
                            {items.length > 1 && (
                                <div className="mt-10 flex items-center gap-4 lg:mt-12" dir="ltr">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            goPrev();
                                            resetAutoplay();
                                        }}
                                        aria-label="السابق"
                                        className="flex size-12 items-center justify-center rounded-full border border-white/15 text-white/60 transition-colors duration-200 hover:border-white/40 hover:text-white sm:size-14"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            className="size-5"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            goNext();
                                            resetAutoplay();
                                        }}
                                        aria-label="التالي"
                                        className="flex size-12 items-center justify-center rounded-full border border-white/15 text-white/60 transition-colors duration-200 hover:border-white/40 hover:text-white sm:size-14"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            className="size-5"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                    <span className="mr-2 text-sm tabular-nums text-white/30">
                                        {String(active + 1).padStart(2, '0')} / {String(items.length).padStart(2, '0')}
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </section>
    );
}
