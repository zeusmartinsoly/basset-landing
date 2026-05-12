import { useGSAP } from '@gsap/react';
import { useRef, useState, type MouseEvent } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { NavbarSection } from '@/types/landing';

const DEFAULT_CONTACT_NAV_TEXT = 'تواصل معنا';
const DEFAULT_CAMPERS_NAV_TEXT = 'أعمال الكامبرز';

function smoothScrollToHash(hash: string): boolean {
    if (!hash.startsWith('#') || hash.length < 2) {
        return false;
    }

    const id = decodeURIComponent(hash.slice(1));
    const el = document.getElementById(id);

    if (!el) {
        return false;
    }

    const reducedMotion =
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    el.scrollIntoView({
        behavior: reducedMotion ? 'auto' : 'smooth',
        block: 'start',
    });

    return true;
}

interface NavbarProps {
    data: NavbarSection;
    contactSectionVisible?: boolean;
    contactNavLinkText?: string | null;
    campersWorksSectionVisible?: boolean;
    campersWorksNavLinkText?: string | null;
    campersWorksHeading?: string | null;
}

export default function Navbar({
    data,
    contactSectionVisible = true,
    contactNavLinkText,
    campersWorksSectionVisible = true,
    campersWorksNavLinkText,
    campersWorksHeading,
}: NavbarProps) {
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

    const contactLink = {
        text: (contactNavLinkText ?? '').trim() || DEFAULT_CONTACT_NAV_TEXT,
        url: '#contact-waitlist',
        external: false as const,
    };

    const campersWorksLink =
        campersWorksSectionVisible !== false
            ? {
                  text:
                      (campersWorksNavLinkText ?? '').trim() ||
                      (campersWorksHeading ?? '').trim() ||
                      DEFAULT_CAMPERS_NAV_TEXT,
                  url: '#campers-works',
                  external: false as const,
              }
            : null;

    const desktopNavLinks = [
        ...(contactSectionVisible ? [contactLink] : []),
        ...(campersWorksLink ? [campersWorksLink] : []),
        ...data.links,
    ];

    const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

    const handleHashNavClick = (
        event: MouseEvent<HTMLAnchorElement>,
        link: { url: string; external: boolean },
    ): void => {
        if (link.external || !link.url.startsWith('#')) {
            return;
        }

        if (smoothScrollToHash(link.url)) {
            event.preventDefault();
            setIsMenuOpen(false);
        }
    };

    return (
        <nav
            ref={navRef}
            className="fixed top-0 right-0 left-0 z-50 bg-black opacity-0"
            style={{ fontFamily: "'IRANSansX', sans-serif" }}
        >
            <div className="mx-auto px-4 sm:px-6 md:px-10 lg:px-16">
                <div className="flex h-16 items-center justify-between sm:h-18 md:h-20 lg:h-24 xl:h-28 2xl:h-32">
                    {/* Logo - Left Side */}
                    <a ref={logoRef} href="/" className="block flex-shrink-0">
                        <img
                            src={`/images/${data.logo}`}
                            alt="Baseet"
                            className="h-5 w-auto sm:h-6 md:h-7 lg:h-8 xl:h-9 2xl:h-10"
                        />
                    </a>

                    {/* Desktop Navigation + CTA - Right Side */}
                    <div className="hidden items-center gap-6 md:flex lg:gap-10 xl:gap-14 2xl:gap-16" dir="rtl">
                        {/* CTA Button */}
                        <a
                            ref={ctaRef}
                            href={data.cta_url}
                            className="rounded-full bg-[#F02624] px-4 py-2 text-sm font-black text-white transition-colors duration-300 hover:bg-[#D62839] lg:px-6 lg:py-3 lg:text-base xl:px-8 xl:py-3.5 xl:text-lg 2xl:px-10 2xl:py-4 2xl:text-xl"
                        >
                            {data.cta_text}
                        </a>

                        {/* Nav Links */}
                        <div ref={linksRef} className="hidden items-center gap-4 lg:flex lg:gap-8 xl:gap-10 2xl:gap-14">
                            {desktopNavLinks.map(
                                (link, index) => (
                                    <a
                                        key={`${link.url}-${link.text}-${index}`}
                                        href={link.url}
                                        target={link.external ? '_blank' : undefined}
                                        rel={link.external ? 'noopener noreferrer' : undefined}
                                        onClick={(event) => handleHashNavClick(event, link)}
                                        className="text-sm font-bold text-white transition-colors duration-300 hover:text-white/70 lg:text-base xl:text-lg 2xl:text-xl"
                                    >
                                        {link.text}
                                    </a>
                                ))}
                        </div>
                    </div>

                    {/* Mobile: CTA + Menu Button */}
                    <div className="flex items-center gap-3 md:hidden" dir="rtl">
                        {/* CTA */}
                        <a
                            href={data.cta_url}
                            className="rounded-full bg-[#F02624] px-4 py-2 text-xs font-bold text-white transition-colors duration-300 hover:bg-[#D62839]"
                        >
                            {data.cta_text}
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
                    {desktopNavLinks.map(
                        (link, index, arr) => (
                            <a
                                key={`${link.url}-${link.text}-${index}`}
                                href={link.url}
                                target={link.external ? '_blank' : undefined}
                                rel={link.external ? 'noopener noreferrer' : undefined}
                                onClick={(event) => handleHashNavClick(event, link)}
                                className={`py-3 text-lg font-bold text-white transition-colors duration-300 hover:text-white/70 ${
                                    index < arr.length - 1 ? 'border-b border-white/10' : ''
                                }`}
                            >
                                {link.text}
                            </a>
                        ))}
                </div>
            </div>
        </nav>
    );
}
