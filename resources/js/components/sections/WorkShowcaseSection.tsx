import { useEffect, useRef, useState, useCallback, useMemo } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { WorkSection } from '@/types/landing';

interface WorkImage {
    src: string;
    alt: string;
}

interface WorkShowcaseSectionProps {
    images: WorkImage[];
    data: WorkSection;
}

interface LightboxState {
    src: string;
    alt: string;
    originRect: DOMRect;
    isClosing: boolean;
}

export default function WorkShowcaseSection({ images, data }: WorkShowcaseSectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const cylinderRef = useRef<HTMLDivElement>(null);
    const rotationRef = useRef({ value: 0 });
    const autoRotateRef = useRef<gsap.core.Tween | null>(null);
    const hasAnimated = useRef(false);
    const [lightbox, setLightbox] = useState<LightboxState | null>(null);
    const lightboxImageRef = useRef<HTMLImageElement>(null);
    const backdropRef = useRef<HTMLDivElement>(null);

    // Use all images dynamically
    const workImages = useMemo(() => 
        images.map((img, index) => ({ ...img, id: index })), 
        [images]
    );
    
    const totalItems = workImages.length;
    const angleStep = 360 / totalItems;
    
    // Fixed radius to maintain consistent cylinder size regardless of image count
    const radius = 700;
    // Calculate item width based on radius and number of items
    const itemWidth = Math.round(2 * radius * Math.tan(Math.PI / totalItems));

    // Update cylinder rotation
    const updateCylinder = useCallback(() => {
        if (cylinderRef.current) {
            cylinderRef.current.style.transform = `rotateY(${rotationRef.current.value}deg)`;
        }
    }, []);

    // Auto rotation
    useEffect(() => {
        autoRotateRef.current = gsap.to(rotationRef.current, {
            value: '+=360',
            duration: 40,
            repeat: -1,
            ease: 'none',
            onUpdate: updateCylinder,
        });

        return () => {
            if (autoRotateRef.current) {
                autoRotateRef.current.kill();
            }
        };
    }, [updateCylinder]);

    // Entrance animation
    useEffect(() => {
        const section = sectionRef.current;
        if (!section) return;

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;
                    
                    gsap.fromTo(
                        '.work-title',
                        { opacity: 0, y: 50 },
                        { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }
                    );

                    gsap.fromTo(
                        '.cylinder-container',
                        { opacity: 0, scale: 0.8 },
                        { opacity: 1, scale: 1, duration: 1, ease: 'power3.out', delay: 0.3 }
                    );
                }
            },
            { threshold: 0.1 }
        );

        observer.observe(section);
        return () => observer.disconnect();
    }, []);

    // Navigation
    const rotateLeft = () => {
        gsap.to(rotationRef.current, {
            value: rotationRef.current.value + angleStep,
            duration: 0.8,
            ease: 'power3.out',
            onUpdate: updateCylinder,
        });
    };

    const rotateRight = () => {
        gsap.to(rotationRef.current, {
            value: rotationRef.current.value - angleStep,
            duration: 0.8,
            ease: 'power3.out',
            onUpdate: updateCylinder,
        });
    };

    // Pause/Resume auto rotation
    const pauseRotation = () => {
        if (autoRotateRef.current) {
            autoRotateRef.current.pause();
        }
    };

    const resumeRotation = () => {
        if (autoRotateRef.current && !lightbox) {
            autoRotateRef.current.resume();
        }
    };

    // Open lightbox with animation
    const openLightbox = (e: React.MouseEvent<HTMLDivElement>, image: { src: string; alt: string }) => {
        const rect = e.currentTarget.getBoundingClientRect();
        pauseRotation();
        document.body.style.overflow = 'hidden';
        
        setLightbox({
            src: image.src,
            alt: image.alt,
            originRect: rect,
            isClosing: false,
        });
    };

    // Animate lightbox when it opens
    useEffect(() => {
        if (lightbox && !lightbox.isClosing && lightboxImageRef.current && backdropRef.current) {
            const { originRect } = lightbox;
            const img = lightboxImageRef.current;
            const backdrop = backdropRef.current;

            // Calculate final size - use standard 16:10 ratio for work images
            const maxWidth = window.innerWidth * 0.85;
            const maxHeight = window.innerHeight * 0.85;
            const aspectRatio = 16 / 10; // Standard work image ratio
            
            let finalWidth = Math.min(maxWidth, 1000);
            let finalHeight = finalWidth / aspectRatio;
            
            if (finalHeight > maxHeight) {
                finalHeight = maxHeight;
                finalWidth = finalHeight * aspectRatio;
            }

            // Set initial state - exactly where the clicked image was
            gsap.set(img, {
                position: 'fixed',
                left: originRect.left,
                top: originRect.top,
                width: originRect.width,
                height: originRect.height,
                borderRadius: 12,
                zIndex: 60,
                transformOrigin: 'center center',
            });

            gsap.set(backdrop, { opacity: 0 });

            // Create timeline for smooth animation
            const tl = gsap.timeline();

            // Fade in backdrop
            tl.to(backdrop, {
                opacity: 1,
                duration: 0.4,
                ease: 'power2.out',
            });

            // Animate image to center
            tl.to(img, {
                left: (window.innerWidth - finalWidth) / 2,
                top: (window.innerHeight - finalHeight) / 2,
                width: finalWidth,
                height: finalHeight,
                borderRadius: 8,
                duration: 0.6,
                ease: 'power3.out',
            }, '-=0.3');
        }
    }, [lightbox]);

    // Close lightbox with animation
    const closeLightbox = () => {
        if (!lightbox || !lightboxImageRef.current || !backdropRef.current) return;

        const { originRect } = lightbox;
        const img = lightboxImageRef.current;
        const backdrop = backdropRef.current;

        setLightbox(prev => prev ? { ...prev, isClosing: true } : null);

        // Create timeline for close animation
        const tl = gsap.timeline({
            onComplete: () => {
                setLightbox(null);
                document.body.style.overflow = '';
                resumeRotation();
            }
        });

        // Animate image back to original position
        tl.to(img, {
            left: originRect.left,
            top: originRect.top,
            width: originRect.width,
            height: originRect.height,
            borderRadius: 12,
            duration: 0.5,
            ease: 'power3.inOut',
        });

        // Fade out backdrop
        tl.to(backdrop, {
            opacity: 0,
            duration: 0.3,
            ease: 'power2.in',
        }, '-=0.3');
    };

    return (
        <>
            <section
                ref={sectionRef}
                className="relative w-full overflow-hidden bg-black py-16 md:py-20"
            >
                {/* Section Title */}
                <div className="work-title mb-8 text-center opacity-0 md:mb-12">
                    <h2
                        className="text-4xl font-bold text-white md:text-5xl lg:text-6xl"
                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        dir="rtl"
                    >
                        {data.title}
                    </h2>
                    <p
                        className="mt-4 text-lg text-white/60 md:text-xl"
                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        dir="rtl"
                    >
                        {data.subtitle}
                    </p>
                </div>

                {/* 3D Cylinder Container */}
                <div 
                    className="cylinder-container relative mx-auto opacity-0"
                    style={{
                        perspective: '1500px',
                        perspectiveOrigin: '50% 50%',
                        height: '400px',
                    }}
                    onMouseEnter={pauseRotation}
                    onMouseLeave={resumeRotation}
                >
                    {/* Cylinder */}
                    <div
                        ref={cylinderRef}
                        className="absolute left-1/2 top-1/2"
                        style={{
                            transformStyle: 'preserve-3d',
                            transform: 'rotateY(0deg)',
                            width: `${itemWidth}px`,
                            height: '200px',
                            marginLeft: `-${itemWidth / 2}px`,
                            marginTop: '-100px',
                        }}
                    >
                        {workImages.map((image, index) => {
                            const angle = angleStep * index;
                            return (
                                <div
                                    key={image.id}
                                    className="group absolute left-0 top-0 h-full w-full cursor-pointer overflow-hidden rounded-xl"
                                    style={{
                                        transform: `rotateY(${angle}deg) translateZ(${radius}px)`,
                                        backfaceVisibility: 'hidden',
                                        boxShadow: '0 10px 40px -10px rgba(0, 0, 0, 0.6)',
                                        transition: 'box-shadow 0.3s ease',
                                    }}
                                    onClick={(e) => openLightbox(e, image)}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.boxShadow = '0 20px 50px -10px rgba(240, 38, 36, 0.4)';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.boxShadow = '0 10px 40px -10px rgba(0, 0, 0, 0.6)';
                                    }}
                                >
                                    <img
                                        src={image.src}
                                        alt={image.alt}
                                        className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                        draggable={false}
                                    />
                                    {/* Gradient overlay */}
                                    <div 
                                        className="pointer-events-none absolute inset-0"
                                        style={{
                                            background: 'linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 100%)',
                                        }}
                                    />
                                </div>
                            );
                        })}
                    </div>

                    {/* Center glow */}
                    <div 
                        className="pointer-events-none absolute left-1/2 top-1/2 h-[300px] w-[500px] -translate-x-1/2 -translate-y-1/2"
                        style={{
                            background: 'radial-gradient(ellipse at center, rgba(240, 38, 36, 0.15) 0%, transparent 60%)',
                            filter: 'blur(40px)',
                        }}
                    />
                </div>

                {/* Navigation Buttons */}
                <div className="mt-8 flex items-center justify-center gap-6">
                    <button
                        onClick={rotateLeft}
                        className="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white/20 bg-white/5 text-white transition-all duration-300 hover:border-[#F02624] hover:bg-[#F02624]/20 hover:text-[#F02624]"
                        aria-label="Previous"
                    >
                        <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        onClick={rotateRight}
                        className="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white/20 bg-white/5 text-white transition-all duration-300 hover:border-[#F02624] hover:bg-[#F02624]/20 hover:text-[#F02624]"
                        aria-label="Next"
                    >
                        <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {/* Instructions */}
                <p
                    className="mt-4 text-center text-sm text-white/40 md:text-base"
                    style={{ fontFamily: "'IRANSansX', sans-serif" }}
                    dir="rtl"
                >
                    {data.instructions}
                </p>

            </section>

            {/* Lightbox */}
            {lightbox && (
                <>
                    {/* Backdrop */}
                    <div
                        ref={backdropRef}
                        className="fixed inset-0 z-50 cursor-pointer"
                        style={{ backgroundColor: 'rgba(0, 0, 0, 0.92)' }}
                        onClick={closeLightbox}
                    >
                        {/* Close button */}
                        <button
                            className="absolute right-6 top-6 z-[70] flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                            onClick={closeLightbox}
                        >
                            <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {/* Animated Image */}
                    <img
                        ref={lightboxImageRef}
                        src={lightbox.src}
                        alt={lightbox.alt}
                        className="pointer-events-none object-contain shadow-2xl"
                        style={{ backgroundColor: 'rgba(0,0,0,0.5)', borderRadius: '8px' }}
                        onClick={(e) => e.stopPropagation()}
                    />
                </>
            )}
        </>
    );
}
