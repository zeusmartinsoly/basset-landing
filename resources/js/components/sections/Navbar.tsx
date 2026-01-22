import { useGSAP } from '@gsap/react';
import { useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function Navbar() {
    const navRef = useRef<HTMLElement>(null);
    const logoRef = useRef<HTMLAnchorElement>(null);
    const linksRef = useRef<HTMLDivElement>(null);
    const ctaRef = useRef<HTMLAnchorElement>(null);

    useGSAP(
        () => {
            const tl = gsap.timeline({
                defaults: { ease: 'power3.out' },
            });

            // Set initial state (hidden & blurred)
            gsap.set([logoRef.current, linksRef.current?.children, ctaRef.current], {
                y: -30,
                opacity: 0,
                filter: 'blur(10px)',
            });

            // Reveal Navbar container first
            gsap.to(navRef.current, {
                opacity: 1,
                duration: 0.5,
            });

            // Staggered Reveal with Blur
            tl.to([logoRef.current, linksRef.current?.children, ctaRef.current], {
                y: 0,
                opacity: 1,
                filter: 'blur(0px)',
                duration: 1.2,
                stagger: 0.1, // Delay between each element
                ease: 'power4.out',
            });
        },
        { scope: navRef }
    );

    return (
        <nav
            ref={navRef}
            className="fixed top-0 right-0 left-0 z-50 bg-black opacity-0"
            style={{ fontFamily: "'IRANSansX', sans-serif" }}
        >
            <div className="mx-auto px-16">
                <div className="flex h-24 items-center justify-between lg:h-32">
                    {/* Logo - Left Side */}
                    <a ref={logoRef} href="/" className="flex-shrink-0 block">
                        <img
                            src="/images/logos/logo.svg"
                            alt="Baseet"
                            className="h-10 w-auto lg:h-12"
                        />
                    </a>

                    {/* Navigation + CTA - Right Side */}
                    <div
                        className="flex items-center gap-12 lg:gap-16"
                        dir="rtl"
                    >
                        {/* CTA Button - Big rounded red */}
                        <a
                            ref={ctaRef}
                            href="#register"
                            className="rounded-full bg-[#F02624] px-10 py-4 text-xl font-black text-white transition-colors duration-300 hover:bg-[#D62839] lg:px-12 lg:py-5 lg:text-2xl"
                        >
                            احجز مكان
                        </a>

                        {/* Nav Links */}
                        <div ref={linksRef} className="hidden items-center gap-10 md:flex lg:gap-14">
                            <a
                                href="#content"
                                className="text-lg font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                محتوى الكامب
                            </a>
                            <a
                                href="#works"
                                className="text-lg font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                أعمال الكامبرز
                            </a>
                            <a
                                href="#behance"
                                className="text-lg font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                بيهانس
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    );
}
