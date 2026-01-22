import { Head } from '@inertiajs/react';
import Navbar from '@/components/sections/Navbar';
import HeroSection from '@/components/sections/HeroSection';
import AboutSection from '@/components/sections/AboutSection';
import CTASection from '@/components/sections/CTASection';

export default function Welcome() {
    return (
        <>
            <Head title="Baseet - Bran—dat 103">
                <meta
                    name="description"
                    content="كامب مكثف متخصص في صناعة الهوية البصرية والعلامات التجارية"
                />
            </Head>

            <div className="min-h-screen bg-[#0A0A0A]" style={{ fontFamily: "'IRANSansX', sans-serif" }}>
                <Navbar />
                <HeroSection />
                <AboutSection />
                <CTASection />
            </div>
        </>
    );
}
