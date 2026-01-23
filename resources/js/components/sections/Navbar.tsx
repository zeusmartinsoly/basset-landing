import { useGSAP } from '@gsap/react';
import { useRef, useState } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function Navbar() {
    const navRef = useRef<HTMLElement>(null);
    const logoRef = useRef<HTMLAnchorElement>(null);
    const linksRef = useRef<HTMLDivElement>(null);
    const ctaRef = useRef<HTMLAnchorElement>(null);
    const mobileMenuRef = useRef<HTMLDivElement>(null);
    const [isMenuOpen, setIsMenuOpen] = useState(false);

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
                stagger: 0.1,
                ease: 'power4.out',
            });
        },
        { scope: navRef }
    );

    // Mobile menu animation
    useGSAP(
        () => {
            if (!mobileMenuRef.current) return;

            if (isMenuOpen) {
                gsap.to(mobileMenuRef.current, {
                    height: 'auto',
                    opacity: 1,
                    duration: 0.4,
                    ease: 'power3.out',
                });
                gsap.fromTo(
                    mobileMenuRef.current.querySelectorAll('a'),
                    { y: 20, opacity: 0 },
                    { y: 0, opacity: 1, stagger: 0.08, duration: 0.4, ease: 'power3.out' }
                );
            } else {
                gsap.to(mobileMenuRef.current, {
                    height: 0,
                    opacity: 0,
                    duration: 0.3,
                    ease: 'power3.in',
                });
            }
        },
        { dependencies: [isMenuOpen] }
    );

    const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

    return (
        <nav
            ref={navRef}
            className="fixed top-0 right-0 left-0 z-50 bg-black opacity-0"
            style={{ fontFamily: "'IRANSansX', sans-serif" }}
        >
            <div className="mx-auto px-4 sm:px-6 md:px-10 lg:px-16">
                <div className="flex h-16 items-center justify-between sm:h-20 lg:h-32">
                    {/* Logo - Left Side */}
                    <a ref={logoRef} href="/" className="block flex-shrink-0">
                        <img
                            src="/images/brand/logo.svg"
                            alt="Baseet"
                            className="h-6 w-auto sm:h-8 lg:h-10"
                        />
                    </a>

                    {/* Desktop Navigation + CTA - Right Side */}
                    <div className="hidden items-center gap-8 md:flex lg:gap-16" dir="rtl">
                        {/* CTA Button */}
                        <a
                            ref={ctaRef}
                            href="#register"
                            className="rounded-full bg-[#F02624] px-5 py-2.5 text-sm font-black text-white transition-colors duration-300 hover:bg-[#D62839] lg:px-10 lg:py-4 lg:text-xl"
                        >
                            احجز مكان
                        </a>

                        {/* Nav Links */}
                        <div ref={linksRef} className="hidden items-center gap-6 lg:flex lg:gap-14">
                            <a
                                href="https://drive.google.com/file/d/1Y0a6btdSkxcFAWmBf4px9AWJJ5vQLAKi/view?usp=sharing"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-base font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                محتوى الكامب
                            </a>
                            <a
                                href="https://drive.google.com/file/d/1MyTF6DDiE3kZ-98C7YgJKZ0rS2dJc90I/view?usp=sharing"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-base font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                أعمال الكامبرز
                            </a>
                            <a
                                href="https://www.behance.net/baseet464"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-base font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-xl"
                            >
                                بيهانس
                            </a>
                        </div>
                    </div>

                    {/* Mobile: CTA + Menu Button */}
                    <div className="flex items-center gap-3 md:hidden" dir="rtl">
                        {/* CTA */}
                        <a
                            href="#register"
                            className="rounded-full bg-[#F02624] px-4 py-2 text-xs font-bold text-white transition-colors duration-300 hover:bg-[#D62839]"
                        >
                            احجز مكان
                        </a>

                        {/* Hamburger Menu Button */}
                        <button
                            onClick={toggleMenu}
                            className="relative flex h-10 w-10 items-center justify-center text-white"
                            aria-label="Toggle menu"
                        >
                            <div className="flex flex-col gap-1.5">
                                <span
                                    className={`h-0.5 w-6 bg-white transition-all duration-300 ${
                                        isMenuOpen ? 'translate-y-2 rotate-45' : ''
                                    }`}
                                />
                                <span
                                    className={`h-0.5 w-6 bg-white transition-all duration-300 ${
                                        isMenuOpen ? 'opacity-0' : ''
                                    }`}
                                />
                                <span
                                    className={`h-0.5 w-6 bg-white transition-all duration-300 ${
                                        isMenuOpen ? '-translate-y-2 -rotate-45' : ''
                                    }`}
                                />
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile Menu */}
            <div
                ref={mobileMenuRef}
                className="h-0 overflow-hidden bg-black opacity-0 md:hidden"
                dir="rtl"
            >
                <div className="flex flex-col gap-4 px-4 pb-6">
                    <a
                        href="https://drive.google.com/file/d/1Y0a6btdSkxcFAWmBf4px9AWJJ5vQLAKi/view?usp=sharing"
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                        className="border-b border-white/10 py-3 text-lg font-bold text-white transition-colors duration-300 hover:text-white/70"
                    >
                        محتوى الكامب
                    </a>
                    <a
                        href="https://drive.google.com/file/d/1MyTF6DDiE3kZ-98C7YgJKZ0rS2dJc90I/view?usp=sharing"
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                        className="border-b border-white/10 py-3 text-lg font-bold text-white transition-colors duration-300 hover:text-white/70"
                    >
                        أعمال الكامبرز
                    </a>
                    <a
                        href="https://www.behance.net/baseet464"
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                        className="py-3 text-lg font-bold text-white transition-colors duration-300 hover:text-white/70"
                    >
                        بيهانس
                    </a>
                </div>
            </div>
        </nav>
    );
}
