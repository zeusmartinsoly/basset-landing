import { useGSAP } from '@gsap/react';
import { useRef, useState, useEffect } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { ForWhomSection as ForWhomSectionType } from '@/types/landing';

// Crosshair Component with drawing animation
function Crosshair({
    className,
    lineRef,
}: {
    className: string;
    lineRef: React.RefObject<SVGSVGElement | null>;
}) {
    return (
        <svg
            ref={lineRef}
            className={className}
            viewBox="0 0 40 40"
            fill="none"
        >
            <line
                x1="20"
                y1="0"
                x2="20"
                y2="40"
                stroke="#F02624"
                strokeWidth="1.5"
                className="crosshair-line"
            />
            <line
                x1="0"
                y1="20"
                x2="40"
                y2="20"
                stroke="#F02624"
                strokeWidth="1.5"
                className="crosshair-line"
            />
        </svg>
    );
}

// Text Scramble Hook
function useTextScramble(text: string, isActive: boolean) {
    const [displayText, setDisplayText] = useState(text);
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*?';

    useEffect(() => {
        if (!isActive) {
            setDisplayText(text);
            return;
        }

        let iteration = 0;
        const interval = setInterval(() => {
            setDisplayText(
                text
                    .split('')
                    .map((char, index) => {
                        if (char === ' ') return ' ';
                        if (index < iteration) return text[index];
                        return chars[Math.floor(Math.random() * chars.length)];
                    })
                    .join('')
            );

            if (iteration >= text.length) {
                clearInterval(interval);
            }

            iteration += 1 / 2;
        }, 40);

        return () => clearInterval(interval);
    }, [isActive, text]);

    return displayText;
}

interface ForWhomSectionProps {
    data: ForWhomSectionType;
}

export default function ForWhomSection({ data }: ForWhomSectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const cardRef = useRef<HTMLDivElement>(null);
    const forWhomRef = useRef<HTMLHeadingElement>(null);
    const cursorRef = useRef<HTMLSpanElement>(null);
    const crosshair1Ref = useRef<SVGSVGElement>(null);
    const crosshair2Ref = useRef<SVGSVGElement>(null);
    const crosshair3Ref = useRef<SVGSVGElement>(null);
    const [scrambleActive, setScrambleActive] = useState(false);
    const [titleText, setTitleText] = useState('');

    const fullTitle = data.title;
    const scrambledText = useTextScramble(data.subtitle, scrambleActive);

    useGSAP(
        () => {
            // Set initial states
            gsap.set(cardRef.current, {
                opacity: 0,
                scale: 0.95,
            });

            gsap.set(
                [
                    crosshair1Ref.current,
                    crosshair2Ref.current,
                    crosshair3Ref.current,
                ],
                {
                    scale: 3,
                    opacity: 0,
                    rotation: 180,
                }
            );

            gsap.set('.for-whom-item', {
                opacity: 0,
                y: 40,
                x: 20,
            });

            gsap.set(forWhomRef.current, {
                opacity: 0,
            });

            // Set crosshair lines for drawing animation
            const crosshairLines = document.querySelectorAll('.crosshair-line');
            crosshairLines.forEach((line) => {
                const length = (line as SVGLineElement).getTotalLength?.() || 40;
                gsap.set(line, {
                    strokeDasharray: length,
                    strokeDashoffset: length,
                });
            });

            // Cursor blinking animation
            gsap.to(cursorRef.current, {
                opacity: 0,
                repeat: -1,
                yoyo: true,
                duration: 0.5,
                ease: 'steps(1)',
            });

            // Typewriter effect for title
            let titleIndex = 0;
            const typeTitle = () => {
                if (titleIndex <= fullTitle.length) {
                    setTitleText(fullTitle.slice(0, titleIndex));
                    titleIndex++;
                    gsap.delayedCall(0.08, typeTitle);
                } else {
                    // Hide cursor when done
                    gsap.to(cursorRef.current, {
                        opacity: 0,
                        duration: 0.3,
                    });
                }
            };

            // Main Timeline with ScrollTrigger
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.current,
                    start: 'top 75%',
                    end: 'bottom 25%',
                    toggleActions: 'play none none none',
                    once: true,
                },
            });

            // Phase 1: Title typewriter
            tl.add(() => {
                typeTitle();
            });

            // Phase 2: Card fade in (wait for typewriter ~1.2s)
            tl.to(
                cardRef.current,
                {
                    opacity: 1,
                    scale: 1,
                    duration: 0.8,
                    ease: 'power3.out',
                },
                '+=0.8'
            );

            // Phase 3: Crosshairs targeting animation
            tl.to(
                [
                    crosshair1Ref.current,
                    crosshair2Ref.current,
                    crosshair3Ref.current,
                ],
                {
                    scale: 1,
                    opacity: 1,
                    rotation: 0,
                    duration: 0.8,
                    stagger: 0.15,
                    ease: 'back.out(2)',
                },
                '-=0.4'
            );

            // Phase 3b: Draw crosshair lines
            tl.to(
                '.crosshair-line',
                {
                    strokeDashoffset: 0,
                    duration: 0.6,
                    stagger: 0.05,
                    ease: 'power2.inOut',
                },
                '-=0.6'
            );

            // Phase 4: Text items staggered reveal
            tl.to(
                '.for-whom-item',
                {
                    opacity: 1,
                    y: 0,
                    x: 0,
                    duration: 0.7,
                    stagger: {
                        amount: 0.8,
                        from: 'start',
                    },
                    ease: 'power3.out',
                },
                '-=0.3'
            );

            // Phase 5: FOR WHOM? with scramble
            tl.to(
                forWhomRef.current,
                {
                    opacity: 1,
                    duration: 0.8,
                    ease: 'power4.out',
                    onStart: () => setScrambleActive(true),
                },
                '-=0.2'
            );

            // Phase 6: Continuous crosshair pulse
            tl.add(() => {
                gsap.to(
                    [
                        crosshair1Ref.current,
                        crosshair2Ref.current,
                        crosshair3Ref.current,
                    ],
                    {
                        scale: 1.1,
                        yoyo: true,
                        repeat: -1,
                        duration: 1.5,
                        stagger: 0.2,
                        ease: 'sine.inOut',
                    }
                );
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="relative w-full bg-white py-12 sm:py-14 md:py-16 lg:py-20 xl:py-24"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 lg:px-10 xl:max-w-[1200px] 2xl:max-w-[1400px]">
                {/* Title with Typewriter */}
                <h2
                    className="mb-8 font-arabic text-4xl font-bold text-black sm:mb-10 sm:text-5xl md:mb-12 md:text-6xl lg:mb-14 lg:text-6xl xl:mb-16 xl:text-7xl 2xl:text-8xl"
                    dir="rtl"
                >
                    {titleText}
                    <span
                        ref={cursorRef}
                        className="inline-block h-[0.9em] w-[3px] translate-y-1 bg-[#F02624] sm:w-[4px] md:w-[5px] lg:w-[6px]"
                    />
                </h2>

                {/* Card Container */}
                <div ref={cardRef} className="relative">
                    {/* Crosshair Top Left */}
                    <Crosshair
                        lineRef={crosshair1Ref}
                        className="absolute -top-3 -left-3 h-8 w-8 sm:-top-4 sm:-left-4 sm:h-10 sm:w-10 md:-top-5 md:-left-5 md:h-11 md:w-11 lg:-top-6 lg:-left-6 lg:h-12 lg:w-12"
                    />

                    {/* Crosshair Top Right */}
                    <Crosshair
                        lineRef={crosshair2Ref}
                        className="absolute -top-3 -right-3 h-8 w-8 sm:-top-4 sm:-right-4 sm:h-10 sm:w-10 md:-top-5 md:-right-5 md:h-11 md:w-11 lg:-top-6 lg:-right-6 lg:h-12 lg:w-12"
                    />

                    {/* Crosshair Bottom Right */}
                    <Crosshair
                        lineRef={crosshair3Ref}
                        className="absolute -right-3 -bottom-3 h-8 w-8 sm:-right-4 sm:-bottom-4 sm:h-10 sm:w-10 md:-right-5 md:-bottom-5 md:h-11 md:w-11 lg:-right-6 lg:-bottom-6 lg:h-12 lg:w-12"
                    />

                    {/* Black Card */}
                    <div
                        className="bg-black px-5 py-8 sm:px-6 sm:py-10 md:px-10 md:py-12 lg:px-14 lg:py-14 xl:px-16 xl:py-16 2xl:px-20 2xl:py-20"
                        dir="rtl"
                    >
                        <div className="grid grid-cols-1 gap-8 sm:gap-9 md:grid-cols-3 md:gap-8 lg:gap-10 xl:gap-12 2xl:gap-16">
                            {/* Distribute items into 3 columns */}
                            {[0, 1, 2].map((colIndex) => (
                                <div key={colIndex} className="space-y-8 sm:space-y-9 md:space-y-8 lg:space-y-10 xl:space-y-12">
                                    {data.items
                                        .filter((_, i) => i % 3 === colIndex)
                                        .map((item, i) => (
                                            <div key={i} className="for-whom-item">
                                                <p
                                                    className={`font-arabic text-base leading-relaxed sm:text-lg md:text-lg lg:text-xl xl:text-2xl ${
                                                        item.color === 'red'
                                                            ? 'text-[#F02624]'
                                                            : 'text-white'
                                                    }`}
                                                >
                                                    <span className="font-bold">{item.text}</span>{' '}
                                                    {item.description}
                                                    {item.highlight && (
                                                        <span className="font-bold text-[#F02624]">
                                                            *
                                                        </span>
                                                    )}
                                                </p>
                                            </div>
                                        ))}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* FOR WHOM? */}
                <div className="-mt-8 sm:-mt-10 md:-mt-12">
                    <h3
                        ref={forWhomRef}
                        className="text-center font-display text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl 2xl:text-[10rem]"
                        style={{
                            color: '#F02624',
                        }}
                    >
                        {scrambledText}
                    </h3>
                </div>
            </div>
        </section>
    );
}
