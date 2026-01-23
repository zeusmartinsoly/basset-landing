import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function CTASection() {
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
            className="relative w-full overflow-hidden bg-black py-12 md:py-16 lg:py-20"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-10 lg:px-16">
                <div className="flex flex-col items-center gap-8 text-center md:gap-10">
                    {/* CTA Button */}
                    <a
                        ref={buttonRef}
                        href="#register"
                        className="rounded-full bg-[#F02624] px-12 py-5 font-arabic text-2xl font-bold text-white opacity-0 transition-colors duration-300 hover:bg-[#D62839] sm:px-16 sm:py-6 sm:text-3xl md:px-20 md:py-7 md:text-4xl"
                        dir="rtl"
                    >
                        احجز مكان
                    </a>

                    {/* Tagline */}
                    <p
                        ref={textRef}
                        className="font-display text-lg tracking-wide text-white opacity-0 sm:text-xl md:text-2xl"
                    >
                        We Don't Design Brands ..
                        <br />
                        We Build them!
                    </p>
                </div>
            </div>
        </section>
    );
}
