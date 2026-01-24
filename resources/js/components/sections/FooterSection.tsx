import { useEffect, useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';
import type { FooterSection as FooterSectionType } from '@/types/landing';

interface FooterSectionProps {
    data: FooterSectionType;
}

export default function FooterSection({ data }: FooterSectionProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const hasAnimated = useRef(false);

    useEffect(() => {
        const section = sectionRef.current;
        if (!section) return;

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;

                    // Animate social icons
                    gsap.fromTo(
                        '.social-icon',
                        { opacity: 0, y: 30, scale: 0.8 },
                        { 
                            opacity: 1, 
                            y: 0, 
                            scale: 1,
                            duration: 0.6, 
                            stagger: 0.1,
                            ease: 'back.out(1.7)',
                        }
                    );

                    // Animate copyright text
                    gsap.fromTo(
                        '.copyright-text',
                        { opacity: 0, y: 20 },
                        { opacity: 1, y: 0, duration: 0.6, delay: 0.4, ease: 'power3.out' }
                    );
                }
            },
            { threshold: 0.3 }
        );

        observer.observe(section);
        return () => observer.disconnect();
    }, []);

    return (
        <footer
            ref={sectionRef}
            className="relative w-full bg-black py-8 sm:py-10 md:py-12 lg:py-14"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-8 lg:px-10">
                {/* Footer Links */}
                {data.footer_links && data.footer_links.length > 0 && (
                    <div className="mb-6 flex items-center justify-center gap-4 sm:mb-7 sm:gap-5 md:mb-8 md:gap-6 lg:gap-8">
                        {data.footer_links.map((link, index) => (
                            <a
                                key={index}
                                href={link.url}
                                className="footer-link text-sm text-white/70 transition-colors duration-300 hover:text-[#D4A853] sm:text-base md:text-base lg:text-lg"
                                style={{ fontFamily: "'IRANSansX', sans-serif" }}
                            >
                                {link.text}
                            </a>
                        ))}
                    </div>
                )}

                {/* Social Icons */}
                <div className="flex items-center justify-center gap-4 sm:gap-5 md:gap-6 lg:gap-8">
                    {data.social_links.map((social) => (
                        <a
                            key={social.name}
                            href={social.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="social-icon opacity-0 transition-transform duration-300 hover:scale-110"
                            aria-label={social.name}
                        >
                            <img
                                src={`/images/${social.icon}`}
                                alt={social.name}
                                className="h-10 w-10 object-contain sm:h-11 sm:w-11 md:h-12 md:w-12 lg:h-14 lg:w-14"
                            />
                        </a>
                    ))}
                </div>

                {/* Copyright */}
                <p
                    className="copyright-text mt-8 text-center text-sm text-white/70 opacity-0 sm:mt-9 sm:text-base md:mt-10 md:text-base lg:mt-12 lg:text-lg"
                    style={{ fontFamily: "'Termina', sans-serif" }}
                >
                    {data.copyright_text}
                </p>

                {/* Developer Credit */}
                <p
                    className="copyright-text mt-3 text-center text-xs text-white/40 opacity-0 sm:mt-3.5 sm:text-sm md:mt-4"
                    style={{ fontFamily: "'Termina', sans-serif" }}
                >
                    {data.developer_credit}
                </p>
            </div>
        </footer>
    );
}
