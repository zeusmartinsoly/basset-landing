import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function PhasesSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const svgRef = useRef<HTMLImageElement>(null);
    const phaseItemsRef = useRef<(HTMLDivElement | null)[]>([]);

    const phases = [
        { number: '01', title: 'Foundation' },
        { number: '02', title: 'Brand Strategy Deck' },
        { number: '03', title: 'Translate Visually' },
        { number: '04', title: 'Full Brand System' },
        { number: '05', title: 'Delivery' },
    ];

    useGSAP(
        () => {
            if (!sectionRef.current) return;

            const phaseItems = phaseItemsRef.current.filter(Boolean);

            // Initial state - hide elements
            phaseItems.forEach((item) => {
                if (!item) return;
                const arrow = item.querySelector('.phase-arrow');
                const title = item.querySelector('.phase-title');
                const subtitle = item.querySelector('.phase-subtitle');

                gsap.set(arrow, { opacity: 0, x: -30, scale: 0.5 });
                gsap.set(title, { opacity: 0, x: -40 });
                gsap.set(subtitle, { opacity: 0, x: -40 });
            });

            gsap.set(svgRef.current, {
                opacity: 0,
                scale: 0.9,
                y: 40,
            });

            // Create timeline with scroll trigger
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.current,
                    start: 'top 75%',
                    once: true,
                },
            });

            // Animate each phase with stagger
            phaseItems.forEach((item, index) => {
                if (!item) return;
                const arrow = item.querySelector('.phase-arrow');
                const title = item.querySelector('.phase-title');
                const subtitle = item.querySelector('.phase-subtitle');
                const delay = index * 0.2;

                // Arrow pops in first
                tl.to(
                    arrow,
                    {
                        opacity: 1,
                        x: 0,
                        scale: 1,
                        duration: 0.4,
                        ease: 'back.out(1.7)',
                    },
                    delay
                );

                // Then phase title slides in
                tl.to(
                    title,
                    {
                        opacity: 1,
                        x: 0,
                        duration: 0.5,
                        ease: 'power3.out',
                    },
                    delay + 0.1
                );

                // Then subtitle follows
                tl.to(
                    subtitle,
                    {
                        opacity: 1,
                        x: 0,
                        duration: 0.5,
                        ease: 'power3.out',
                    },
                    delay + 0.2
                );
            });

            // SVG reveals after phases start appearing
            tl.to(
                svgRef.current,
                {
                    opacity: 1,
                    scale: 1,
                    y: 0,
                    duration: 1,
                    ease: 'power3.out',
                },
                0.3
            );

            // Add subtle floating animation to SVG after reveal
            tl.add(() => {
                gsap.to(svgRef.current, {
                    y: -15,
                    duration: 3,
                    yoyo: true,
                    repeat: -1,
                    ease: 'sine.inOut',
                });
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="relative flex min-h-[80vh] w-full items-center overflow-hidden bg-black py-16 md:py-20 lg:py-24"
        >
            <div className="mx-auto max-w-[1920px] px-6 md:px-12 lg:px-20">
                <div className="flex flex-col items-center gap-12 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
                    {/* Left Side - Phases List */}
                    <div className="w-full lg:w-1/2">
                        <div className="flex flex-col gap-8 md:gap-10 lg:gap-12">
                            {phases.map((phase, index) => (
                                <div
                                    key={phase.number}
                                    ref={(el) => {
                                        phaseItemsRef.current[index] = el;
                                    }}
                                    className="phase-item flex items-start gap-3 md:gap-4"
                                >
                                    <span className="phase-arrow font-display text-lg text-white md:text-xl lg:text-2xl">
                                        →
                                    </span>
                                    <div className="flex flex-col">
                                        <span className="phase-title font-display text-2xl font-bold tracking-tight text-white md:text-3xl lg:text-4xl">
                                            Phase O{phase.number.charAt(1)}
                                        </span>
                                        <span className="phase-subtitle font-display text-lg font-light tracking-wide text-white/90 md:text-xl lg:text-2xl">
                                            {phase.title}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Right Side - Brandat With AI SVG */}
                    <div className="flex w-full items-center justify-center lg:w-1/2 lg:justify-end">
                        <img
                            ref={svgRef}
                            src="/images/brandat-withai.svg"
                            alt="Bran—dat 103 - Now With AI"
                            className="h-auto w-full max-w-[700px] lg:max-w-[950px] xl:max-w-[1100px]"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
