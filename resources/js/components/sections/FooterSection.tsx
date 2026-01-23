import { useEffect, useRef } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

const socialLinks = [
    { name: 'Behance', icon: '/images/social/icons-01.png', url: 'https://www.behance.net/baseet464' },
    { name: 'Facebook', icon: '/images/social/icons-02.png', url: 'https://www.facebook.com/share/1893Sb8hSv/?mibextid=wwXIfr' },
    { name: 'LinkedIn', icon: '/images/social/icons-03.png', url: 'https://www.linkedin.com/in/baseet464/' },
    { name: 'Instagram', icon: '/images/social/icons-04.png', url: 'https://www.instagram.com/baseet464?igsh=ZWw4OWdibTZtcmRz&utm_source=qr' },
];

export default function FooterSection() {
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
                    {socialLinks.map((social) => (
                        <a
                            key={social.name}
                            href={social.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="social-icon opacity-0 transition-transform duration-300 hover:scale-110"
                            aria-label={social.name}
                        >
                            <img
                                src={social.icon}
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
                    All Rights Reserved @Baseet 2026
                </p>

                {/* Developer Credit */}
                <p
                    className="copyright-text mt-4 text-center text-sm text-white/40 opacity-0"
                    style={{ fontFamily: "'Termina', sans-serif" }}
                >
                    Developed by Cerebro AI
                </p>
            </div>
        </footer>
    );
}
