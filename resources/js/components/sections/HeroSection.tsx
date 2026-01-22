import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function HeroSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const diceLeftRef = useRef<HTMLImageElement>(null);
    const diceRightRef = useRef<HTMLImageElement>(null);
    const branRef = useRef<HTMLImageElement>(null);
    const datRef = useRef<HTMLImageElement>(null);
    const numberRef = useRef<HTMLImageElement>(null);

    useGSAP(
        () => {
            const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

            // Initial setup
            gsap.set(sectionRef.current, { visibility: 'visible' });

            // Staggered Text Reveal (Secondary text)
            tl.fromTo(
                '.hero-text',
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, duration: 1, stagger: 0.1 }
            );

            // Main Logo Parts Reveal
            tl.fromTo(
                [branRef.current, datRef.current, numberRef.current],
                { y: 50, opacity: 0 },
                { y: 0, opacity: 1, duration: 1.2, stagger: 0.15, ease: 'power3.out' },
                0
            );

            // Dice Reveal
            tl.fromTo(
                [diceLeftRef.current, diceRightRef.current],
                { scale: 0, opacity: 0 },
                { scale: 1, opacity: 1, duration: 1.2, ease: 'elastic.out(1, 0.75)', stagger: 0.2 },
                '-=0.8'
            );

            // Floating Animation
            gsap.to(diceLeftRef.current, {
                y: 15,
                rotation: 3,
                duration: 4,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
            });

            gsap.to(diceRightRef.current, {
                y: -15,
                rotation: -2,
                duration: 4.5,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
                delay: 0.5,
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="invisible relative flex w-full items-center justify-center bg-black"
            style={{ minHeight: '100vh' }}
        >
            {/* Max Width Container - Pixel Perfect Constraint */}
            <div className="relative mx-auto h-screen w-full">
                {/* Background Text/Logo Layer (SVG Assets) - Behind Dice */}
                <div className="pointer-events-none absolute inset-0 z-0 select-none">
                    {/* Bran - with bar */}
                    <img
                        ref={branRef}
                        src="/images/hero/bran.svg"
                        alt="Bran —"
                        className="absolute object-contain"
                        style={{
                            top: '18%',
                            left: '5%',
                            width: '60%',
                        }}
                    />

                    {/* 103 */}
                    <img
                        ref={numberRef}
                        src="/images/hero/103.svg"
                        alt="103"
                        className="absolute object-contain"
                        style={{
                            bottom: '5%',
                            left: '5%',
                            width: '20%',
                        }}
                    />
                </div>

                {/* — dat Layer - Above Dice */}
                <div className="pointer-events-none absolute inset-0 z-20 select-none">
                    <img
                        ref={datRef}
                        src="/images/hero/dat.svg"
                        alt="— dat"
                        className="absolute object-contain"
                        style={{
                            top: '45%',
                            right: '5%',
                            width: '60%',
                        }}
                    />
                </div>

                {/* Left Dice - Behind dat */}
                <div className="pointer-events-none absolute inset-0 z-10">
                    <img
                        ref={diceLeftRef}
                        src="/images/hero/dice-left.png"
                        alt="Red Dice Left"
                        className="absolute object-contain"
                        style={{
                            top: '35%',
                            left: '30%',
                            maxWidth: '500px',
                        }}
                    />
                </div>

                {/* Right Dice - Above dat */}
                <div className="pointer-events-none absolute inset-0 z-25">
                    <img
                        ref={diceRightRef}
                        src="/images/hero/dice-right.png"
                        alt="Red Dice Right"
                        className="absolute object-contain"
                        style={{
                            top: '35%',
                            left: '45%',
                            maxWidth: '600px',
                        }}
                    />
                </div>

                {/* Secondary Texts Layer */}
                <div className="pointer-events-none absolute inset-0 z-30">
                    {/* Arabic Quote - Top Right */}
                    <div
                        className="hero-text absolute flex flex-col items-end"
                        style={{ top: '21%', right: '35%' }}
                        dir="rtl"
                    >
                        <p className="font-arabic text-[1.2vw] leading-relaxed font-bold text-white opacity-90">
                            لقرارات وتحكّم .. مفيش رمي نرد
                            <br />
                            في فهم إمتى وإزاي ترميه.
                        </p>
                    </div>

                    {/* Branding Is Thinking - Left Middle */}
                    <div
                        className="hero-text absolute"
                        style={{ top: '45%', left: '5%' }}
                    >
                        <p className="font-display text-[1.2vw] leading-snug tracking-wide text-white opacity-90">
                            Branding Is Thinking
                            <br />
                            Before Design.
                        </p>
                    </div>

                    {/* THIRD EDITION - Left, Above 103 */}
                    <div
                        className="hero-text absolute"
                        style={{ bottom: '26%', left: '9%' }}
                    >
                        <span className="font-display text-[1.3rem] font-medium tracking-[0.25em] text-white">
                            THIRD EDITION
                        </span>
                    </div>

                    {/* علامات 103 - Bottom Right */}
                    <div
                        className="hero-text absolute flex items-baseline gap-2"
                        style={{ bottom: '18%', right: '4%' }}
                        dir="rtl"
                    >
                        <span className="font-arabic text-[3vw] font-bold text-white">
                            علامات
                        </span>
                        <span className="font-display text-[2vw] font-bold text-white">
                            103
                        </span>
                    </div>

                    {/* 2026 - Bottom Right Corner */}
                    <div
                        className="hero-text absolute"
                        style={{ bottom: '5%', right: '5%' }}
                    >
                        <span className="font-display text-[1.2vw] tracking-widest text-white opacity-80">
                            2026
                        </span>
                    </div>
                </div>
            </div>
        </section>
    );
}
