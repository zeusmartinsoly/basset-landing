import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function FounderSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const imageRef = useRef<HTMLImageElement>(null);
    const imageContainerRef = useRef<HTMLDivElement>(null);
    const maskRef = useRef<HTMLDivElement>(null);

    useGSAP(
        () => {
            // Initial state
            gsap.set('.founder-svg', {
                opacity: 0,
                scale: 0.95,
            });
            gsap.set(maskRef.current, {
                clipPath: 'circle(0% at 50% 50%)',
            });
            gsap.set(imageRef.current, {
                scale: 1.2,
            });

            // Create timeline with scroll trigger
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.current,
                    start: 'top 70%',
                    once: true,
                },
            });

            // SVG fades in and scales up
            tl.to('.founder-svg', {
                opacity: 1,
                scale: 1,
                duration: 1,
                ease: 'power3.out',
            });

            // Circle mask reveals the image
            tl.to(
                maskRef.current,
                {
                    clipPath: 'circle(100% at 50% 50%)',
                    duration: 1.4,
                    ease: 'power4.inOut',
                },
                '-=0.6'
            );

            // Image zooms out slightly for cinematic effect
            tl.to(
                imageRef.current,
                {
                    scale: 1,
                    duration: 1.8,
                    ease: 'power2.out',
                },
                '-=1.4'
            );

        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="relative flex min-h-[50vh] w-full items-center justify-center bg-white py-8 sm:min-h-[60vh] sm:py-12 md:min-h-[75vh] md:py-16 lg:min-h-[90vh] lg:py-32"
        >
            {/* Background SVG - alamat103 (smaller) */}
            <div className="founder-svg pointer-events-none absolute inset-0 z-0 flex items-center justify-center">
                <img
                    src="/images/alamat103.svg"
                    alt="Bran—dat 103"
                    className="h-auto w-[95%] object-contain opacity-100 sm:w-[90%] md:w-[80%] lg:w-[70%] xl:w-[65%]"
                />
            </div>

            {/* Founder Image - Center, on top of logo */}
            <div
                ref={imageContainerRef}
                className="absolute left-1/2 top-1/2 z-20 -translate-x-1/2 -translate-y-1/2"
            >
                {/* Circle Mask Container */}
                <div
                    ref={maskRef}
                    className="overflow-hidden rounded-lg"
                    style={{ clipPath: 'circle(0% at 50% 50%)' }}
                >
                    <img
                        ref={imageRef}
                        src="/images/assets/founder.png"
                        alt="Mohamed Baseet - Founder"
                        className="h-auto w-[280px] object-contain sm:w-[320px] md:w-[400px] lg:w-[500px] xl:w-[600px]"
                    />
                </div>
            </div>
        </section>
    );
}
