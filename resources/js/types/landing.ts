/**
 * Landing Page Section Types
 * These interfaces match the database structure for landing page sections
 */

export interface NavLink {
    text: string;
    url: string;
    external: boolean;
}

export interface NavbarSection {
    logo: string;
    cta_text: string;
    cta_url: string;
    links: NavLink[];
}

export interface HeroImages {
    bran_svg: string;
    dat_svg: string;
    number_svg: string;
    dice_left: string;
    dice_right: string;
}

export interface HeroSection {
    arabic_quote: string;
    english_text: string;
    edition: string;
    brand_name: string;
    brand_number: string;
    year: string;
    images: HeroImages;
}

export interface AboutImages {
    image_1: string;
    image_2: string;
}

export interface AboutSection {
    title: string;
    description_1: string;
    description_2: string;
    images: AboutImages;
}

export interface CtaSection {
    button_text: string;
    button_url: string;
    tagline: string;
}

export interface GallerySection {
    row_1_images: string[];
    row_2_images: string[];
}

export interface ForWhomItem {
    text: string;
    description: string;
    highlight: boolean;
    color: 'white' | 'red';
}

export interface ForWhomSection {
    title: string;
    subtitle: string;
    items: ForWhomItem[];
}

export interface Phase {
    number: string;
    title: string;
}

export interface PhasesSection {
    phases: Phase[];
    image: string;
}

export interface FounderSection {
    founder_image: string;
    background_image: string;
    founder_name: string;
    founder_title: string;
}

export interface IntroSection {
    title: string;
    paragraphs: string[];
    years_design: number;
    years_branding: number;
    award_image: string;
    cta_title: string;
    cta_button_text: string;
    cta_button_url: string;
}

export interface WorkSection {
    title: string;
    subtitle: string;
    instructions: string;
}

export interface SocialLink {
    name: string;
    icon: string;
    url: string;
}

export interface FooterLink {
    text: string;
    url: string;
}

export interface FooterSection {
    footer_links: FooterLink[];
    social_links: SocialLink[];
    copyright_text: string;
    developer_credit: string;
}

/**
 * All landing page sections combined
 */
export interface LandingPageSections {
    navbar: NavbarSection;
    hero: HeroSection;
    about: AboutSection;
    cta: CtaSection;
    gallery: GallerySection;
    for_whom: ForWhomSection;
    phases: PhasesSection;
    founder: FounderSection;
    intro: IntroSection;
    work: WorkSection;
    footer: FooterSection;
}
