import { useEffect, useRef, useState } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

// Simple Counter Component
function AnimatedCounter({ target, duration = 2 }: { target: number; duration?: number }) {
    const [count, setCount] = useState(0);
    const [hasAnimated, setHasAnimated] = useState(false);
    const ref = useRef<HTMLSpanElement>(null);

    useEffect(() => {
        const element = ref.current;
        if (!element) return;

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !hasAnimated) {
                    setHasAnimated(true);
                    const startTime = Date.now();
                    const animate = () => {
                        const elapsed = Date.now() - startTime;
                        const progress = Math.min(elapsed / (duration * 1000), 1);
                        const easeProgress = 1 - Math.pow(1 - progress, 3);
                        setCount(Math.floor(easeProgress * target));
                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        } else {
                            setCount(target);
                        }
                    };
                    requestAnimationFrame(animate);
                }
            },
            { threshold: 0.1 }
        );

        observer.observe(element);
        return () => observer.disconnect();
    }, [target, duration, hasAnimated]);

    return (
        <span 
            ref={ref}
            style={{
                textShadow: '0 0 10px rgba(245, 158, 11, 0.8), 0 0 20px rgba(245, 158, 11, 0.5), 0 0 40px rgba(245, 158, 11, 0.3)',
            }}
        >
            {count}
        </span>
    );
}

export default function IntroSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const hasAnimated = useRef(false);

    useEffect(() => {
        const section = sectionRef.current;
        if (!section || hasAnimated.current) return;

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;
                    runAnimation();
                }
            },
            { threshold: 0.2 }
        );

        observer.observe(section);
        return () => observer.disconnect();
    }, []);

    const runAnimation = () => {
        const section = sectionRef.current;
        if (!section) return;

        const awardImage = section.querySelector('.award-image');
        const awardGlow = section.querySelector('.award-glow');
        const title = section.querySelector('.intro-title');
        const texts = section.querySelectorAll('.intro-text');
        const cta = section.querySelector('.intro-cta');

        // Create main timeline
        const tl = gsap.timeline();

        // 1. Award image scales up and appears
        if (awardImage) {
            tl.fromTo(
                awardImage,
                { scale: 0.7, opacity: 0 },
                { scale: 1, opacity: 1, duration: 1, ease: 'back.out(1.7)' }
            );
        }

        // 2. Glow pulsing animation (always visible, just add pulse)
        if (awardGlow) {
            gsap.to(awardGlow, {
                opacity: 0.5,
                scale: 1.15,
                duration: 2,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
            });
        }

        // 3. Title appears (start early, same time as award)
        if (title) {
            tl.fromTo(
                title,
                { opacity: 0, y: 40 },
                { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' },
                0.2
            );
        }

        // 4. Text paragraphs appear with stagger (start early so counter is visible)
        if (texts.length > 0) {
            tl.fromTo(
                texts,
                { opacity: 0, y: 30 },
                { opacity: 1, y: 0, duration: 0.5, stagger: 0.15, ease: 'power3.out' },
                0.4
            );
        }

        // 5. CTA appears
        if (cta) {
            tl.fromTo(
                cta,
                { opacity: 0, y: 30 },
                { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' },
                '-=0.2'
            );
        }
    };

    return (
        <section
            ref={sectionRef}
            className="relative w-full bg-black py-16 md:py-24 lg:py-32"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {/* Main Content - Two Columns */}
                <div className="flex flex-col items-center gap-8 md:flex-row md:items-start md:gap-12 lg:gap-16">
                    {/* Left Side - Award Image with Glow Effect */}
                    <div className="relative w-full flex-shrink-0 md:w-[40%] lg:w-[35%]">
                        {/* Golden Glow Behind Award - Always visible */}
                        <div
                            className="award-glow pointer-events-none absolute inset-0 z-0"
                            style={{
                                background: 'radial-gradient(circle at 50% 50%, rgba(255, 193, 7, 0.6) 0%, rgba(255, 152, 0, 0.4) 40%, transparent 70%)',
                                filter: 'blur(60px)',
                            }}
                        />
                        
                        {/* Award Image */}
                        <img
                            src="/images/assets/award.webp"
                            alt="Award 2025 Edition - Business Wheel Gold Winner"
                            className="award-image relative z-10 h-auto w-full object-contain opacity-0"
                        />
                    </div>

                    {/* Right Side - Text Content */}
                    <div className="w-full text-right md:w-[60%] lg:w-[65%]" dir="rtl">
                        <h2
                            className="intro-title mb-6 text-4xl font-bold text-white opacity-0 md:mb-8 md:text-5xl lg:text-6xl"
                            style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        >
                            نتعرف؟
                        </h2>

                        <div
                            className="space-y-4 text-base leading-[1.5] text-white/90 md:space-y-5 md:text-lg lg:text-xl"
                            style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        >
                            <p className="intro-text text-xl leading-[1.5] text-white opacity-0 md:text-2xl lg:text-3xl">
                                انا محمد , هتقولي يا "بسيط".
                                <br />
                                مصمم جرافيك بخبرة <span className="font-bold text-amber-400"><AnimatedCounter target={13} /></span> سنة في التصميم و أكثر من
                                <br />
                                <span className="font-bold text-amber-400"><AnimatedCounter target={7} /></span> سـنوات في مجال البراندنج , اشـتغلت في الخليج
                                <br />
                                وأوروبا وكندا وعملت مشـاريع كبيرة في اكتر من بلد!
                                <br />
                                أخــدت دهبية جريدلاينرز في تصميم الهويات البصرية
                                <br />
                                وجايزة مشروع العام!
                            </p>

                            <p className="intro-text text-xl leading-[1.5] text-white opacity-0 md:text-2xl lg:text-3xl">
                                وأخدت جايزتين (المركزين الأول والثاني) لسنتين علي
                                <br />
                                التوالــي في أكبر إيفنــت يخص التصميم فــي العالم
                                <br />
                                العربي (جرافيك ديزاين بالعربي).
                            </p>

                            <p className="intro-text text-xl leading-[1.5] text-white opacity-0 md:text-2xl lg:text-3xl">
                                جاهز تتعرف عليا وتبقى ابن خالتي؟
                            </p>
                        </div>
                    </div>
                </div>

                {/* Bottom CTA Section */}
                <div className="intro-cta mt-16 text-center opacity-0 md:mt-20 lg:mt-24">
                    <p
                        className="mx-auto mb-8 max-w-3xl text-xl leading-[1.5] text-white md:text-2xl lg:text-3xl"
                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                        dir="rtl"
                    >
                        ابتدي رحلة بناء العلامات التجارية من استراتيجية العلامة
                        <br />
                        وحتى عملية التسليم وعرض الهوية البصرية.
                    </p>

                    <a
                        href="#register"
                        className="inline-block rounded-full bg-[#F02624] px-12 py-4 text-xl font-bold text-white transition-all duration-300 hover:scale-105 hover:bg-[#D62839] md:px-16 md:py-5 md:text-2xl"
                        style={{ fontFamily: "'IRANSansX', sans-serif" }}
                    >
                        احجز مكان
                    </a>
                </div>
            </div>
        </section>
    );
}
