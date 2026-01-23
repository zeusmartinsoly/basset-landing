import { Head } from '@inertiajs/react';
import Navbar from '@/components/sections/Navbar';
import HeroSection from '@/components/sections/HeroSection';
import AboutSection from '@/components/sections/AboutSection';
import FounderSection from '@/components/sections/FounderSection';
import PhasesSection from '@/components/sections/PhasesSection';
import CTASection from '@/components/sections/CTASection';
import GallerySection from '@/components/sections/GallerySection';
import ForWhomSection from '@/components/sections/ForWhomSection';
import IntroSection from '@/components/sections/IntroSection';
import WorkShowcaseSection from '@/components/sections/WorkShowcaseSection';
import FooterSection from '@/components/sections/FooterSection';

interface WorkImage {
    src: string;
    alt: string;
}

interface WelcomeProps {
    workImages: WorkImage[];
}

export default function Welcome({ workImages }: WelcomeProps) {
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
                <GallerySection />
                <ForWhomSection />
                <PhasesSection />
                <FounderSection />
                <IntroSection />
                <WorkShowcaseSection images={workImages} />
                <FooterSection />
            </div>
        </>
    );
}
