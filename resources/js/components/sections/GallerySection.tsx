import { useGSAP } from '@gsap/react';
import { useMemo, useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { GallerySection as GallerySectionType } from '@/types/landing';

interface GallerySectionProps {
    data: GallerySectionType;
    scrollAnchorId?: string;
    featuredHeadline?: boolean;
}

export default function GallerySection({
    data,
    scrollAnchorId,
    featuredHeadline = false,
}: GallerySectionProps) {
    const row1Images = Array.isArray(data.row_1_images) ? data.row_1_images : [];
    const row2Images = Array.isArray(data.row_2_images) ? data.row_2_images : [];
    const heading = data.heading?.trim();
    const altPrefix = heading || 'Gallery';
    const hasImageRows = row1Images.length > 0 && row2Images.length > 0;
    const sectionRef = useRef<HTMLElement>(null);
    const headlineScopeRef = useRef<HTMLDivElement>(null);
    const accentLineRef = useRef<HTMLDivElement>(null);
    const track1Ref = useRef<HTMLDivElement>(null);
    const track2Ref = useRef<HTMLDivElement>(null);
    const tween1Ref = useRef<gsap.core.Tween | null>(null);
    const tween2Ref = useRef<gsap.core.Tween | null>(null);

    const headingWords = useMemo(() => heading?.split(/\s+/).filter(Boolean) ?? [], [heading]);

    useGSAP(
        () => {
            if (!featuredHeadline || !heading || headingWords.length === 0) return;

            const container = headlineScopeRef.current;
            const line = accentLineRef.current;
            if (!container || !line) return;

            const wordInners = container.querySelectorAll<HTMLElement>('.gallery-featured-word-inner');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reduceMotion) {
                gsap.set(line, { scaleX: 1, transformOrigin: '50% 50%' });
                gsap.set(wordInners, { opacity: 1, y: 0, rotateX: 0 });

                return;
            }

            gsap.set(line, { scaleX: 0, transformOrigin: '50% 50%' });
            gsap.set(wordInners, {
                opacity: 0,
                y: 40,
                rotateX: -18,
                transformPerspective: 900,
            });

            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: container,
                    start: 'top 82%',
                    toggleActions: 'play none none reverse',
                },
            });

            tl.to(line, {
                scaleX: 1,
                duration: 0.75,
                ease: 'power3.out',
            }).to(
                wordInners,
                {
                    opacity: 1,
                    y: 0,
                    rotateX: 0,
                    stagger: 0.07,
                    duration: 0.8,
                    ease: 'power4.out',
                },
                '-=0.35'
            );

            return () => {
                tl.scrollTrigger?.kill();
                tl.kill();
            };
        },
        {
            scope: headlineScopeRef,
            dependencies: [featuredHeadline, heading],
            revertOnUpdate: true,
        }
    );

    useGSAP(
        () => {
            const track1 = track1Ref.current;
            const track2 = track2Ref.current;

            if (!track1 || !track2) return;

            const track1Width = track1.scrollWidth / 2;
            const track2Width = track2.scrollWidth / 2;

            if (track1Width <= 0 || track2Width <= 0) return;

            tween1Ref.current = gsap.to(track1, {
                x: -track1Width,
                duration: 30,
                ease: 'none',
                repeat: -1,
                modifiers: {
                    x: gsap.utils.unitize((x) => parseFloat(x) % track1Width),
                },
            });

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

    const row1Duplicated = hasImageRows ? [...row1Images, ...row1Images] : [];
    const row2Duplicated = hasImageRows ? [...row2Images, ...row2Images] : [];

    if (!heading && !hasImageRows) {
        return null;
    }

    const sectionClassName = [
        'relative w-full overflow-x-hidden overflow-y-visible bg-black py-8 md:py-16',
        featuredHeadline ? 'scroll-mt-20 pt-10 md:scroll-mt-24 md:pt-12 lg:scroll-mt-28' : '',
    ]
        .filter(Boolean)
        .join(' ');

    return (
        <section ref={sectionRef} id={scrollAnchorId} className={sectionClassName}>
            {heading && featuredHeadline ? (
                <div
                    ref={headlineScopeRef}
                    className="relative mx-auto mb-10 max-w-5xl px-4 text-center md:mb-14 lg:mb-16"
                >
                    <div
                        aria-hidden
                        className="pointer-events-none absolute left-1/2 top-1/2 h-[min(48vw,280px)] w-[min(92vw,640px)] -translate-x-1/2 -translate-y-1/2 rounded-[50%] bg-[radial-gradient(ellipse_at_center,rgba(240,38,36,0.14)_0%,transparent_68%)] md:h-[300px] md:w-[680px]"
                    />

                    <div
                        ref={accentLineRef}
                        className="relative mx-auto mb-7 h-px w-28 origin-center bg-gradient-to-r from-transparent via-[#F02624] to-transparent md:mb-9 md:w-36"
                    />

                    <div className="relative overflow-visible [perspective:1200px]">
                        <h2
                            className="py-[0.18em] text-[clamp(1.75rem,5vw,3.75rem)] font-extrabold leading-[1.42] tracking-tight text-white md:leading-[1.38]"
                            style={{ fontFamily: "'IRANSansX', sans-serif" }}
                            dir="rtl"
                        >
                            {headingWords.map((word, index) => (
                                <span
                                    key={`${word}-${index}`}
                                    className="gallery-featured-word mx-[0.15em] inline-block overflow-visible pb-[0.08em] pt-[0.12em] md:mx-[0.18em]"
                                >
                                    <span className="gallery-featured-word-inner inline-block text-white drop-shadow-[0_2px_28px_rgba(255,255,255,0.07)]">
                                        {word}
                                    </span>
                                </span>
                            ))}
                        </h2>
                    </div>
                </div>
            ) : heading ? (
                <div className="mb-6 px-4 text-center sm:mb-8 md:mb-10">
                    <h2
                        className="text-3xl font-bold text-white sm:text-4xl md:text-4xl lg:text-5xl xl:text-5xl 2xl:text-6xl"
                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        dir="rtl"
                    >
                        {heading}
                    </h2>
                </div>
            ) : null}

            {hasImageRows ? (
                <div className="mb-4 overflow-hidden md:mb-6">
                    <div ref={track1Ref} className="flex w-max gap-3 sm:gap-4 md:gap-5 lg:gap-6">
                        {row1Duplicated.map((image, index) => (
                            <div
                                key={index}
                                className="gallery-item relative w-[240px] shrink-0 overflow-hidden rounded-lg sm:w-[280px] sm:rounded-xl md:w-[340px] lg:w-[400px] xl:w-[450px] 2xl:w-[500px]"
                            >
                                <img
                                    src={`/images/${image}`}
                                    alt={`${altPrefix} ${(index % row1Images.length) + 1}`}
                                    className="h-auto w-full object-contain"
                                    loading="lazy"
                                />
                            </div>
                        ))}
                    </div>
                </div>
            ) : null}

            {hasImageRows ? (
                <div className="overflow-hidden">
                    <div ref={track2Ref} className="flex w-max gap-3 sm:gap-4 md:gap-5 lg:gap-6">
                        {row2Duplicated.map((image, index) => (
                            <div
                                key={index}
                                className="gallery-item relative w-[240px] shrink-0 overflow-hidden rounded-lg sm:w-[280px] sm:rounded-xl md:w-[340px] lg:w-[400px] xl:w-[450px] 2xl:w-[500px]"
                            >
                                <img
                                    src={`/images/${image}`}
                                    alt={`${altPrefix} ${(index % row2Images.length) + row1Images.length + 1}`}
                                    className="h-auto w-full object-contain"
                                    loading="lazy"
                                />
                            </div>
                        ))}
                    </div>
                </div>
            ) : null}
        </section>
    );
}
