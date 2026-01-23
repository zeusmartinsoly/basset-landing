import { useGSAP } from '@gsap/react';
import { useRef, useState } from 'react';
import { gsap } from '@/lib/animations/gsap-setup';

export default function AboutSection() {
    const sectionRef = useRef<HTMLElement>(null);
    const titleRef = useRef<HTMLHeadingElement>(null);
    const descriptionRef = useRef<HTMLParagraphElement>(null);
    const diceRef = useRef<HTMLImageElement>(null);
    const cursorRef = useRef<HTMLSpanElement>(null);
    const [titleText, setTitleText] = useState('');
    const [descText, setDescText] = useState('');

    // Second block refs
    const secondBlockRef = useRef<HTMLDivElement>(null);
    const dice2Ref = useRef<HTMLImageElement>(null);
    const cursor2Ref = useRef<HTMLSpanElement>(null);
    const [desc2Text, setDesc2Text] = useState('');

    const fullTitle = 'ايه هو\nكامب العلامات™!';
    const fullDescription =
        'هــو كامب لايــف، قايم علـــى المتابعة المباشرة ، موجه للمصممين المهتمين بصناعــة الهويات والعلامـــات التجارية اللي شــغالين فريلانس أو في شركات، وعايزيــن يطلعــوا من مرحلــة التنفيذ لمرحلة التفكير والسيطرة والإبداع.';

    const fullDescription2 =
        'مــش كورس، ومــش ورشــة، ومش فيديوهات مســجلة. هــو بيئة متقفلة بتتعلــم فيهــا إزاي تفكّر صــح، وتاخد قرارات براندنج محســوبة، وتنفّذ وانت فاهم إنت بتعمل إيه وليه.';

    // Helper to start second block animation
    const startSecondBlock = () => {
        // Dice 2 entrance animation
        gsap.fromTo(
            dice2Ref.current,
            {
                scale: 0.5,
                opacity: 0,
                y: 50,
            },
            {
                scale: 1,
                opacity: 1,
                y: 0,
                duration: 1.2,
                ease: 'power3.out',
            }
        );

        // Typewriter effect for description 2
        let desc2Index = 0;
        const typeDescription2 = () => {
            if (desc2Index <= fullDescription2.length) {
                setDesc2Text(fullDescription2.slice(0, desc2Index));
                desc2Index++;
                gsap.delayedCall(0.02, typeDescription2);
            } else {
                // Hide cursor when done
                gsap.to(cursor2Ref.current, {
                    opacity: 0,
                    duration: 0.3,
                });
            }
        };

        // Start typewriter
        gsap.delayedCall(0.5, typeDescription2);

        // Dice 2 floating animation (infinite)
        gsap.to(dice2Ref.current, {
            y: 20,
            duration: 4,
            yoyo: true,
            repeat: -1,
            ease: 'sine.inOut',
            delay: 1.2,
        });
    };

    // All animations
    useGSAP(
        () => {
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.current,
                    start: 'top 80%',
                    once: true,
                },
            });

            // Dice entrance animation - no rotation
            tl.fromTo(
                diceRef.current,
                {
                    scale: 0.5,
                    opacity: 0,
                    y: 50,
                },
                {
                    scale: 1,
                    opacity: 1,
                    y: 0,
                    duration: 1.2,
                    ease: 'power3.out',
                }
            );

            // Typewriter effect for title
            let titleIndex = 0;
            const typeTitle = () => {
                if (titleIndex <= fullTitle.length) {
                    setTitleText(fullTitle.slice(0, titleIndex));
                    titleIndex++;
                    gsap.delayedCall(0.08, typeTitle);
                } else {
                    // Start description after title
                    gsap.delayedCall(0.3, typeDescription);
                }
            };

            // Typewriter effect for description
            let descIndex = 0;
            const typeDescription = () => {
                if (descIndex <= fullDescription.length) {
                    setDescText(fullDescription.slice(0, descIndex));
                    descIndex++;
                    gsap.delayedCall(0.02, typeDescription);
                } else {
                    // Hide cursor when done
                    gsap.to(cursorRef.current, {
                        opacity: 0,
                        duration: 0.3,
                    });
                    // Start second block after first description finishes
                    gsap.delayedCall(0.5, startSecondBlock);
                }
            };

            // Start typewriter after dice animation
            tl.add(() => {
                typeTitle();
            }, '-=0.5');

            // Cursor blinking
            gsap.to(cursorRef.current, {
                opacity: 0,
                repeat: -1,
                yoyo: true,
                duration: 0.5,
                ease: 'steps(1)',
            });

            // Cursor 2 blinking (start hidden, will show when second block starts)
            gsap.set(cursor2Ref.current, { opacity: 0 });
            gsap.to(cursor2Ref.current, {
                opacity: 0,
                repeat: -1,
                yoyo: true,
                duration: 0.5,
                ease: 'steps(1)',
            });

            // Dice floating animation (infinite) - no rotation
            gsap.to(diceRef.current, {
                y: 20,
                duration: 3.5,
                yoyo: true,
                repeat: -1,
                ease: 'sine.inOut',
                delay: 1.2,
            });
        },
        { scope: sectionRef }
    );

    return (
        <section
            ref={sectionRef}
            className="relative min-h-screen w-full overflow-hidden bg-black py-16 md:py-24"
        >
            <div className="mx-auto max-w-7xl px-4 sm:px-6 md:px-10 lg:px-16">
                <div className="flex flex-col-reverse items-center gap-8 lg:flex-row lg:items-start lg:justify-between lg:gap-16">
                    {/* Text Content - Left Side */}
                    <div className="w-full lg:w-1/2" dir="rtl">
                        {/* Title with Typewriter */}
                        <h2
                            ref={titleRef}
                            className="mb-4 font-arabic text-4xl font-bold leading-tight text-white sm:mb-6 sm:text-5xl md:mb-8 md:text-6xl lg:text-7xl"
                        >
                            {titleText.split('\n').map((line, i) => (
                                <span key={i}>
                                    {line}
                                    {i === 0 && <br />}
                                </span>
                            ))}
                            <span
                                ref={cursorRef}
                                className="inline-block h-[1em] w-[3px] translate-y-1 bg-[#F02624]"
                            />
                        </h2>

                        {/* Description with Typewriter */}
                        <p
                            ref={descriptionRef}
                            className="font-arabic text-xl leading-relaxed text-white sm:text-2xl md:text-3xl lg:text-4xl"
                        >
                            {descText}
                        </p>
                    </div>

                    {/* Dice - Right Side */}
                    <div className="relative w-full lg:w-1/2">
                        <img
                            ref={diceRef}
                            src="/images/hero/assets-web-05.webp"
                            alt="Camp Image"
                            className="mx-auto w-[70%] max-w-[500px] object-contain opacity-0 sm:w-[60%] lg:mx-0 lg:mr-0 lg:ml-auto lg:w-full"
                            style={{
                                willChange: 'transform',
                            }}
                        />
                    </div>
                </div>

                {/* Second Block - Dice Left, Text Right */}
                <div
                    ref={secondBlockRef}
                    className="mt-24 flex flex-col items-center gap-8 md:mt-32 lg:flex-row lg:items-center lg:justify-between lg:gap-16"
                >
                    {/* Dice - Left Side */}
                    <div className="relative order-2 w-full lg:order-1 lg:w-1/2">
                        <img
                            ref={dice2Ref}
                            src="/images/hero/assets-web-06.webp"
                            alt="Camp Image"
                            className="mx-auto w-[70%] max-w-[500px] object-contain opacity-0 sm:w-[60%] lg:mx-0 lg:ml-0 lg:mr-auto lg:w-full"
                            style={{
                                willChange: 'transform',
                            }}
                        />
                    </div>

                    {/* Text Content - Right Side */}
                    <div className="order-1 w-full lg:order-2 lg:w-1/2" dir="rtl">
                        <p className="font-arabic text-xl leading-relaxed text-white sm:text-2xl md:text-3xl lg:text-4xl">
                            {desc2Text}
                            <span
                                ref={cursor2Ref}
                                className="inline-block h-[1em] w-[3px] translate-y-1 bg-[#F02624]"
                            />
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
