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
            className="relative w-full bg-black py-10 md:py-14"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {/* Social Icons */}
                <div className="flex items-center justify-center gap-6 md:gap-8">
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
                                className="h-12 w-12 object-contain md:h-14 md:w-14"
                            />
                        </a>
                    ))}
                </div>

                {/* Copyright */}
                <p
                    className="copyright-text mt-10 text-center text-base text-white/70 opacity-0 md:mt-12 md:text-lg"
                    style={{ fontFamily: "'Termina', sans-serif" }}
                >
                    {data.copyright_text}
                </p>

                {/* Developer Credit */}
                <p
                    className="copyright-text mt-4 text-center text-sm text-white/40 opacity-0"
                    style={{ fontFamily: "'Termina', sans-serif" }}
                >
                    {data.developer_credit}
                </p>
            </div>
        </footer>
    );
}
