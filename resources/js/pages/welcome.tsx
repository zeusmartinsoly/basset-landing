import { Head } from '@inertiajs/react';
import AboutSection from '@/components/sections/AboutSection';
import CTASection from '@/components/sections/CTASection';
import FooterSection from '@/components/sections/FooterSection';
import ForWhomSection from '@/components/sections/ForWhomSection';
import FounderSection from '@/components/sections/FounderSection';
import GallerySection from '@/components/sections/GallerySection';
import HeroSection from '@/components/sections/HeroSection';
import IntroSection from '@/components/sections/IntroSection';
import Navbar from '@/components/sections/Navbar';
import PhasesSection from '@/components/sections/PhasesSection';
import WorkShowcaseSection from '@/components/sections/WorkShowcaseSection';
import JsonLd from '@/components/seo/JsonLd';
import type { LandingPageSections } from '@/types/landing';

interface WorkImage {
    src: string;
    alt: string;
}

interface SeoSettings {
    site_title: string;
    site_description: string | null;
    keywords: string | null;
    author: string | null;
    og_title: string | null;
    og_description: string | null;
    og_image: string | null;
    twitter_handle: string | null;
    twitter_card_type: string;
    organization_name: string | null;
    organization_logo: string | null;
    course_name: string | null;
    course_description: string | null;
    canonical_url: string | null;
    google_site_verification: string | null;
    custom_head_scripts: string | null;
}

interface WelcomeProps {
    workImages: WorkImage[];
    seo: SeoSettings;
    appUrl: string;
    sections: LandingPageSections;
}

export default function Welcome({ workImages, seo, appUrl, sections }: WelcomeProps) {
    const ogTitle = seo.og_title || seo.site_title;
    const ogDescription = seo.og_description || seo.site_description;
    const ogImage = seo.og_image ? `${appUrl}${seo.og_image}` : null;
    const canonicalUrl = seo.canonical_url || appUrl;

    return (
        <>
            <Head title={seo.site_title}>
                {/* Basic Meta Tags */}
                <meta
                    head-key="description"
                    name="description"
                    content={seo.site_description || ''}
                />
                {seo.keywords && (
                    <meta head-key="keywords" name="keywords" content={seo.keywords} />
                )}
                {seo.author && (
                    <meta head-key="author" name="author" content={seo.author} />
                )}
                <meta head-key="robots" name="robots" content="index, follow" />

                {/* Canonical URL */}
                <link head-key="canonical" rel="canonical" href={canonicalUrl} />

                {/* Open Graph Tags */}
                <meta head-key="og:type" property="og:type" content="website" />
                <meta head-key="og:locale" property="og:locale" content="ar_SA" />
                <meta head-key="og:title" property="og:title" content={ogTitle} />
                {ogDescription && (
                    <meta
                        head-key="og:description"
                        property="og:description"
                        content={ogDescription}
                    />
                )}
                <meta head-key="og:url" property="og:url" content={canonicalUrl} />
                {ogImage && (
                    <>
                        <meta head-key="og:image" property="og:image" content={ogImage} />
                        <meta
                            head-key="og:image:width"
                            property="og:image:width"
                            content="1200"
                        />
                        <meta
                            head-key="og:image:height"
                            property="og:image:height"
                            content="630"
                        />
                    </>
                )}
                {seo.organization_name && (
                    <meta
                        head-key="og:site_name"
                        property="og:site_name"
                        content={seo.organization_name}
                    />
                )}

                {/* Twitter Card Tags */}
                <meta
                    head-key="twitter:card"
                    name="twitter:card"
                    content={seo.twitter_card_type || 'summary_large_image'}
                />
                <meta head-key="twitter:title" name="twitter:title" content={ogTitle} />
                {ogDescription && (
                    <meta
                        head-key="twitter:description"
                        name="twitter:description"
                        content={ogDescription}
                    />
                )}
                {ogImage && (
                    <meta head-key="twitter:image" name="twitter:image" content={ogImage} />
                )}
                {seo.twitter_handle && (
                    <meta
                        head-key="twitter:site"
                        name="twitter:site"
                        content={`@${seo.twitter_handle.replace('@', '')}`}
                    />
                )}

                {/* Google Site Verification */}
                {seo.google_site_verification && (
                    <meta
                        head-key="google-site-verification"
                        name="google-site-verification"
                        content={seo.google_site_verification}
                    />
                )}

                {/* JSON-LD Structured Data */}
                <JsonLd seo={seo} appUrl={appUrl} />
            </Head>

            <div
                className="min-h-screen bg-[#0A0A0A]"
                style={{ fontFamily: "'IRANSansX', sans-serif" }}
            >
                <Navbar data={sections.navbar} />
                <HeroSection data={sections.hero} />
                <AboutSection data={sections.about} />
                <CTASection data={sections.cta} />
                <GallerySection data={sections.gallery} />
                <ForWhomSection data={sections.for_whom} />
                <PhasesSection data={sections.phases} />
                <FounderSection data={sections.founder} />
                <IntroSection data={sections.intro} />
                <WorkShowcaseSection images={workImages} data={sections.work} />
                <FooterSection data={sections.footer} />
            </div>
        </>
    );
}
