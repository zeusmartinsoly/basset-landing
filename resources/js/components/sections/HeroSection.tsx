import { useGSAP } from '@gsap/react';
import { useEffect, useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function HeroSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const diceLeftRef = useRef<HTMLImageElement>(null);
    const diceRightRef = useRef<HTMLImageElement>(null);
    const branRef = useRef<HTMLImageElement>(null);
    const datRef = useRef<HTMLImageElement>(null);
    const numberRef = useRef<HTMLImageElement>(null);
    const mousePos = useRef({ x: 0, y: 0 });

    // Mouse Parallax Effect (Desktop only)
    useEffect(() => {
        // Skip parallax on touch devices and small screens
        const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        const isSmallScreen = window.innerWidth < 1024;

        if (isTouch || isSmallScreen) return;

        const handleMouseMove = (e: MouseEvent) => {
            mousePos.current = {
                x: (e.clientX / window.innerWidth - 0.5) * 2,
                y: (e.clientY / window.innerHeight - 0.5) * 2,
            };

            // Parallax on dice
            gsap.to(diceLeftRef.current, {
                x: mousePos.current.x * 25,
                y: mousePos.current.y * 25,
                rotateY: mousePos.current.x * 10,
                rotateX: -mousePos.current.y * 10,
                duration: 0.8,
                ease: 'power2.out',
            });

            gsap.to(diceRightRef.current, {
                x: mousePos.current.x * -20,
                y: mousePos.current.y * -20,
                rotateY: mousePos.current.x * -8,
                rotateX: -mousePos.current.y * -8,
                duration: 0.8,
                ease: 'power2.out',
            });

            // Subtle parallax on text
            gsap.to('.hero-text', {
                x: mousePos.current.x * 5,
                y: mousePos.current.y * 5,
                duration: 1,
                ease: 'power2.out',
            });
        };

        window.addEventListener('mousemove', handleMouseMove);
        return () => window.removeEventListener('mousemove', handleMouseMove);
    }, []);

    useGSAP(
        () => {
            const masterTL = gsap.timeline({
                defaults: { ease: 'expo.out' },
            });

            // Initial setup
            gsap.set(sectionRef.current, { visibility: 'visible' });
            gsap.set([branRef.current, datRef.current, numberRef.current], {
                clipPath: 'polygon(0 100%, 100% 100%, 100% 100%, 0% 100%)',
            });
            gsap.set([diceLeftRef.current, diceRightRef.current], {
                scale: 0,
                opacity: 0,
                rotation: -180,
            });
            gsap.set('.hero-text', { y: 40, opacity: 0 });

            // Phase 1: Logo Clip-Path Reveal (Awwwards style)
            masterTL
                .to(branRef.current, {
                    clipPath: 'polygon(0 0%, 100% 0%, 100% 100%, 0% 100%)',
                    duration: 1.4,
                    ease: 'power4.inOut',
                })
                .to(
                    datRef.current,
                    {
                        clipPath: 'polygon(0 0%, 100% 0%, 100% 100%, 0% 100%)',
                        duration: 1.4,
                        ease: 'power4.inOut',
                    },
                    '-=1.0'
                )
                .to(
                    numberRef.current,
                    {
                        clipPath: 'polygon(0 0%, 100% 0%, 100% 100%, 0% 100%)',
                        duration: 1.2,
                        ease: 'power4.inOut',
                    },
                    '-=0.8'
                );

            // Phase 2: Dice 3D Throw Effect
            masterTL
                .to(
                    diceLeftRef.current,
                    {
                        scale: 1,
                        opacity: 1,
                        rotation: 0,
                        duration: 1.2,
                        ease: 'back.out(1.7)',
                    },
                    '-=0.6'
                )
                .to(
                    diceRightRef.current,
                    {
                        scale: 1,
                        opacity: 1,
                        rotation: 0,
                        duration: 1.2,
                        ease: 'back.out(1.7)',
                    },
                    '-=0.9'
                );

            // Phase 3: Text Cascade Reveal
            masterTL.to(
                '.hero-text',
                {
                    y: 0,
                    opacity: 1,
                    stagger: 0.12,
                    duration: 1,
                    ease: 'power3.out',
                },
                '-=0.5'
            );

            // Phase 4: Infinite Floating Animations
            masterTL.add(() => {
                // Dice floating with slight rotation
                gsap.to(diceLeftRef.current, {
                    y: '+=20',
                    rotation: 5,
                    yoyo: true,
                    repeat: -1,
                    duration: 3.5,
                    ease: 'sine.inOut',
                });

                gsap.to(diceRightRef.current, {
                    y: '-=18',
                    rotation: -4,
                    yoyo: true,
                    repeat: -1,
                    duration: 4,
                    ease: 'sine.inOut',
                    delay: 0.3,
                });

                // Subtle pulse on 103
                gsap.to(numberRef.current, {
                    scale: 1.02,
                    yoyo: true,
                    repeat: -1,
                    duration: 2.5,
                    ease: 'sine.inOut',
                });

                // Subtle glow pulse on logo
                gsap.to([branRef.current, datRef.current], {
                    filter: 'brightness(1.1)',
                    yoyo: true,
                    repeat: -1,
                    duration: 3,
                    ease: 'sine.inOut',
                    stagger: 0.5,
                });
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="invisible relative flex min-h-screen w-full items-center justify-center overflow-hidden bg-black"
        >
            {/* Max Width Container */}
            <div className="relative mx-auto h-screen w-full">
                {/* Background Text/Logo Layer (SVG Assets) - Behind Dice */}
                <div className="pointer-events-none absolute inset-0 z-0 select-none">
                    {/* Bran - with bar */}
                    <img
                        ref={branRef}
                        src="/images/hero/bran.svg"
                        alt="Bran —"
                        className="absolute left-1/2 top-[25%] w-[85%] -translate-x-1/2 object-contain md:left-[5%] md:top-[18%] md:w-[70%] md:translate-x-0 lg:w-[60%]"
                    />

                    {/* 103 */}
                    <img
                        ref={numberRef}
                        src="/images/hero/103.svg"
                        alt="103"
                        className="absolute bottom-[8%] left-[5%] w-[35%] object-contain md:bottom-[5%] md:w-[25%] lg:w-[20%]"
                    />
                </div>

                {/* — dat Layer - Above Dice */}
                <div className="pointer-events-none absolute inset-0 z-20 select-none">
                    <img
                        ref={datRef}
                        src="/images/hero/dat.svg"
                        alt="— dat"
                        className="absolute right-1/2 top-[48%] w-[85%] translate-x-1/2 object-contain md:right-[5%] md:top-[45%] md:w-[70%] md:translate-x-0 lg:w-[60%]"
                    />
                </div>

                {/* Left Dice - Behind dat */}
                <div
                    className="pointer-events-none absolute inset-0 z-10"
                    style={{ perspective: '1000px' }}
                >
                    <img
                        ref={diceLeftRef}
                        src="/images/hero/dice-left.png"
                        alt="Red Dice Left"
                        className="absolute left-[15%] top-[38%] w-[45%] max-w-[280px] object-contain md:left-[25%] md:top-[35%] md:max-w-[400px] lg:left-[30%] lg:max-w-[500px]"
                        style={{
                            transformStyle: 'preserve-3d',
                            willChange: 'transform',
                        }}
                    />
                </div>

                {/* Right Dice - Above dat */}
                <div
                    className="pointer-events-none absolute inset-0 z-25"
                    style={{ perspective: '1000px' }}
                >
                    <img
                        ref={diceRightRef}
                        src="/images/hero/dice-right.png"
                        alt="Red Dice Right"
                        className="absolute left-[40%] top-[36%] w-[50%] max-w-[320px] object-contain md:left-[40%] md:top-[35%] md:max-w-[500px] lg:left-[45%] lg:max-w-[600px]"
                        style={{
                            transformStyle: 'preserve-3d',
                            willChange: 'transform',
                        }}
                    />
                </div>

                {/* Secondary Texts Layer */}
                <div className="pointer-events-none absolute inset-0 z-30">
                    {/* Arabic Quote - Top Right */}
                    <div
                        className="hero-text absolute right-[5%] top-[15%] flex flex-col items-end md:right-[25%] md:top-[18%] lg:right-[35%] lg:top-[21%]"
                        dir="rtl"
                    >
                        <p className="text-right font-arabic text-sm font-bold leading-relaxed text-white opacity-90 md:text-base lg:text-[1.2vw]">
                            لقرارات وتحكّم .. مفيش رمي نرد
                            <br />
                            في فهم إمتى وإزاي ترميه.
                        </p>
                    </div>

                    {/* Branding Is Thinking - Left Middle */}
                    <div className="hero-text absolute left-[5%] top-[68%] md:top-[50%] lg:top-[45%]">
                        <p className="font-display text-sm leading-snug tracking-wide text-white opacity-90 md:text-base lg:text-[1.2vw]">
                            Branding Is Thinking
                            <br />
                            Before Design.
                        </p>
                    </div>

                    {/* THIRD EDITION - Left, Above 103 */}
                    <div className="hero-text absolute bottom-[22%] left-[5%] md:bottom-[26%] md:left-[9%]">
                        <span className="font-display text-xs font-medium tracking-[0.2em] text-[#F02624] md:text-sm md:tracking-[0.25em] lg:text-[1.3rem]">
                            THIRD EDITION
                        </span>
                    </div>

                    {/* علامات 103 - Bottom Right */}
                    <div
                        className="hero-text absolute bottom-[22%] right-[5%] flex items-baseline gap-1 md:bottom-[18%] md:right-[4%] md:gap-2"
                        dir="rtl"
                    >
                        <span className="font-arabic text-2xl font-bold text-white md:text-3xl lg:text-[3vw]">
                            علامات
                        </span>
                        <span className="font-display text-sm font-bold text-white md:text-base lg:text-[1vw]">
                            103
                        </span>
                    </div>

                    {/* 2026 - Bottom Right Corner */}
                    <div className="hero-text absolute bottom-[8%] right-[5%] md:bottom-[5%]">
                        <span className="font-display text-xs tracking-widest text-white opacity-80 md:text-sm lg:text-[1.2vw]">
                            2026
                        </span>
                    </div>
                </div>
            </div>
        </section>
    );
}
