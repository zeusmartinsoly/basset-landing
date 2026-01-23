import { useGSAP } from '@gsap/react';
import { useRef, useEffect } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function FounderSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const imageRef = useRef<HTMLImageElement>(null);
    const imageContainerRef = useRef<HTMLDivElement>(null);
    const maskRef = useRef<HTMLDivElement>(null);

    // 3D Tilt Effect on mouse move (Desktop only, only when section is in view)
    useEffect(() => {
        const imageContainer = imageContainerRef.current;
        const section = sectionRef.current;
        if (!imageContainer || !section) return;

        // Skip on touch devices
        const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (isTouch) return;

        let isInView = false;

        // Track if section is in view
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    isInView = entry.isIntersecting;
                    
                    // Reset rotation when leaving view
                    if (!isInView) {
                        gsap.to(imageContainer, {
                            rotateX: 0,
                            rotateY: 0,
                            duration: 0.3,
                            ease: 'power2.out',
                        });
                    }
                });
            },
            { threshold: 0.1 }
        );

        observer.observe(section);

        const handleMouseMove = (e: MouseEvent) => {
            // Only apply effect if section is in view
            if (!isInView) return;

            const rect = imageContainer.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const mouseX = e.clientX - centerX;
            const mouseY = e.clientY - centerY;

            // Calculate rotation (max 12 degrees)
            const rotateY = (mouseX / (rect.width / 2)) * 12;
            const rotateX = -(mouseY / (rect.height / 2)) * 12;

            gsap.to(imageContainer, {
                rotateX: rotateX,
                rotateY: rotateY,
                duration: 0.5,
                ease: 'power2.out',
                transformPerspective: 1000,
            });
        };

        const handleMouseLeave = () => {
            gsap.to(imageContainer, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.7,
                ease: 'elastic.out(1, 0.5)',
            });
        };

        window.addEventListener('mousemove', handleMouseMove);
        imageContainer.addEventListener('mouseleave', handleMouseLeave);

        return () => {
            observer.disconnect();
            window.removeEventListener('mousemove', handleMouseMove);
            imageContainer.removeEventListener('mouseleave', handleMouseLeave);
        };
    }, []);

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

            // Add subtle floating animation after reveal
            tl.add(() => {
                gsap.to(imageContainerRef.current, {
                    y: -10,
                    duration: 2.5,
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

            {/* Founder Image - Center, on top of logo with 3D tilt */}
            <div
                ref={imageContainerRef}
                className="relative z-20 flex items-center justify-center"
                style={{ transformStyle: 'preserve-3d' }}
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
                        className="h-auto w-[85%] max-w-[700px] object-contain sm:max-w-[380px] md:max-w-[450px] lg:max-w-[550px] xl:w-[90%] xl:max-w-none"
                    />
                </div>
            </div>
        </section>
    );
}
