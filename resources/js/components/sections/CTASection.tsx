import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { CtaSection as CtaSectionType } from '@/types/landing';

interface CTASectionProps {
    data: CtaSectionType;
}

export default function CTASection({ data }: CTASectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const buttonRef = useRef<HTMLAnchorElement>(null);
    const textRef = useRef<HTMLParagraphElement>(null);

    useGSAP(
        () => {
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.current,
                    start: 'top 80%',
                    once: true,
                },
            });

            // Button entrance with scale bounce
            tl.fromTo(
                buttonRef.current,
                {
                    scale: 0,
                    opacity: 0,
                },
                {
                    scale: 1,
                    opacity: 1,
                    duration: 0.8,
                    ease: 'back.out(1.7)',
                }
            );

            // Text fade in
            tl.fromTo(
                textRef.current,
                {
                    y: 30,
                    opacity: 0,
                },
                {
                    y: 0,
                    opacity: 1,
                    duration: 0.8,
                    ease: 'power3.out',
                },
                '-=0.4'
            );

            // Button float/levitate animation (infinite)
            gsap.to(buttonRef.current, {
                y: -8,
                duration: 2,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
                delay: 1,
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="relative w-full overflow-hidden bg-black py-10 sm:py-12 md:py-14 lg:py-16 xl:py-18 2xl:py-20"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 lg:px-10 xl:px-12 2xl:px-16">
                <div className="flex flex-col items-center gap-6 text-center sm:gap-7 md:gap-8 lg:gap-9 xl:gap-10">
                    {/* CTA Button */}
                    <a
                        ref={buttonRef}
                        href={data.button_url}
                        className="rounded-full bg-[#F02624] px-8 py-4 font-arabic text-xl font-bold text-white opacity-0 transition-colors duration-300 hover:bg-[#D62839] sm:px-10 sm:py-4.5 sm:text-2xl md:px-14 md:py-5 md:text-2xl lg:px-16 lg:py-6 lg:text-3xl xl:px-18 xl:py-6.5 xl:text-3xl 2xl:px-20 2xl:py-7 2xl:text-4xl"
                        dir="rtl"
                    >
                        {data.button_text}
                    </a>

                    {/* Tagline */}
                    <p
                        ref={textRef}
                        className="font-display text-base tracking-wide text-white opacity-0 sm:text-lg md:text-lg lg:text-xl xl:text-xl 2xl:text-2xl"
                    >
                        {data.tagline.split('\n').map((line, i) => (
                            <span key={i}>
                                {line}
                                {i < data.tagline.split('\n').length - 1 && <br />}
                            </span>
                        ))}
                    </p>
                </div>
            </div>
        </section>
    );
}
