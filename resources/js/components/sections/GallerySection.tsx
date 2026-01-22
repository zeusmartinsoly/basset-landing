import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

// Gallery images - duplicated for seamless loop
const row1Images = [
    'Assets web-07.png',
    'Assets web-08.png',
    'Assets web-09.png',
    'Assets web-10.png',
    'Assets web-11.png',
];

const row2Images = [
    'Assets web-12.png',
    'Assets web-13.png',
    'Assets web-14.png',
    'Assets web-15.png',
    'Assets web-16.png',
];

export default function GallerySection() {
    const sectionRef = useRef<HTMLElement>(null);
    const track1Ref = useRef<HTMLDivElement>(null);
    const track2Ref = useRef<HTMLDivElement>(null);
    const tween1Ref = useRef<gsap.core.Tween | null>(null);
    const tween2Ref = useRef<gsap.core.Tween | null>(null);

    useGSAP(
        () => {
            // Calculate the width of one set of images
            const track1 = track1Ref.current;
            const track2 = track2Ref.current;

            if (!track1 || !track2) return;

            // Get the width of the first set (half of the track since we duplicate)
            const track1Width = track1.scrollWidth / 2;
            const track2Width = track2.scrollWidth / 2;

            // Row 1 - Move LEFT (negative direction)
            tween1Ref.current = gsap.to(track1, {
                x: -track1Width,
                duration: 30,
                ease: 'none',
                repeat: -1,
                modifiers: {
                    x: gsap.utils.unitize((x) => parseFloat(x) % track1Width),
                },
            });

            // Row 2 - Move RIGHT (start from negative, go to 0)
            gsap.set(track2, { x: -track2Width });
            tween2Ref.current = gsap.to(track2, {
                x: 0,
                duration: 25,
                ease: 'none',
                repeat: -1,
                modifiers: {
                    x: gsap.utils.unitize((x) => {
                        const val = parseFloat(x);
                        return val >= 0 ? val - track2Width : val;
                    }),
                },
            });

            // Pause on hover
            const handleMouseEnter = () => {
                tween1Ref.current?.timeScale(0.2);
                tween2Ref.current?.timeScale(0.2);
            };

            const handleMouseLeave = () => {
                gsap.to([tween1Ref.current, tween2Ref.current], {
                    timeScale: 1,
                    duration: 0.5,
                    ease: 'power2.out',
                });
            };

            sectionRef.current?.addEventListener('mouseenter', handleMouseEnter);
            sectionRef.current?.addEventListener('mouseleave', handleMouseLeave);

            // Hover effect on individual images
            const allItems = sectionRef.current?.querySelectorAll('.gallery-item');
            allItems?.forEach((item) => {
                item.addEventListener('mouseenter', () => {
                    gsap.to(item, {
                        scale: 1.08,
                        zIndex: 10,
                        duration: 0.4,
                        ease: 'power2.out',
                    });
                });

                item.addEventListener('mouseleave', () => {
                    gsap.to(item, {
                        scale: 1,
                        zIndex: 1,
                        duration: 0.4,
                        ease: 'power2.out',
                    });
                });
            });

            return () => {
                sectionRef.current?.removeEventListener('mouseenter', handleMouseEnter);
                sectionRef.current?.removeEventListener('mouseleave', handleMouseLeave);
            };
        },
        { scope: sectionRef }
    );

    // Duplicate images for seamless loop
    const row1Duplicated = [...row1Images, ...row1Images];
    const row2Duplicated = [...row2Images, ...row2Images];

    return (
        <section
            ref={sectionRef}
            className="relative w-full overflow-hidden bg-black py-8 md:py-16"
        >
            {/* Row 1 - Moving Left */}
            <div className="mb-4 overflow-hidden md:mb-6">
                <div ref={track1Ref} className="flex w-max gap-4 md:gap-6">
                    {row1Duplicated.map((image, index) => (
                        <div
                            key={index}
                            className="gallery-item relative w-[300px] flex-shrink-0 overflow-hidden rounded-xl md:w-[400px] lg:w-[500px]"
                        >
                            <img
                                src={`/images/assets/${image}`}
                                alt={`Gallery ${(index % 5) + 1}`}
                                className="h-auto w-full object-contain"
                                loading="lazy"
                            />
                        </div>
                    ))}
                </div>
            </div>

            {/* Row 2 - Moving Right */}
            <div className="overflow-hidden">
                <div ref={track2Ref} className="flex w-max gap-4 md:gap-6">
                    {row2Duplicated.map((image, index) => (
                        <div
                            key={index}
                            className="gallery-item relative w-[300px] flex-shrink-0 overflow-hidden rounded-xl md:w-[400px] lg:w-[500px]"
                        >
                            <img
                                src={`/images/assets/${image}`}
                                alt={`Gallery ${(index % 5) + 6}`}
                                className="h-auto w-full object-contain"
                                loading="lazy"
                            />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
