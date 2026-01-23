interface JsonLdProps {
    seo: {
        site_title: string;
        site_description: string | null;
        organization_name: string | null;
        organization_logo: string | null;
        course_name: string | null;
        course_description: string | null;
    };
    appUrl: string;
}

export default function JsonLd({ seo, appUrl }: JsonLdProps) {
    const organizationLogo = seo.organization_logo
        ? `${appUrl}${seo.organization_logo}`
        : `${appUrl}/images/brand/logo.svg`;

    // Organization Schema
    const organizationSchema = {
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name: seo.organization_name || 'Baseet',
        url: appUrl,
        logo: organizationLogo,
        sameAs: [],
    };

    // WebSite Schema
    const websiteSchema = {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: seo.site_title,
        url: appUrl,
        description: seo.site_description,
        inLanguage: 'ar',
        publisher: {
            '@type': 'Organization',
            name: seo.organization_name || 'Baseet',
        },
    };

    // Course Schema (for the intensive camp)
    const courseSchema = seo.course_name
        ? {
              '@context': 'https://schema.org',
              '@type': 'Course',
              name: seo.course_name,
              description: seo.course_description || seo.site_description,
              provider: {
                  '@type': 'Organization',
                  name: seo.organization_name || 'Baseet',
                  url: appUrl,
              },
              inLanguage: 'ar',
              courseMode: 'Online',
              hasCourseInstance: {
                  '@type': 'CourseInstance',
                  courseMode: 'Online',
              },
          }
        : null;

    return (
        <>
            <script
                type="application/ld+json"
                dangerouslySetInnerHTML={{
                    __html: JSON.stringify(organizationSchema),
                }}
            />
            <script
                type="application/ld+json"
                dangerouslySetInnerHTML={{
                    __html: JSON.stringify(websiteSchema),
                }}
            />
            {courseSchema && (
                <script
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{
                        __html: JSON.stringify(courseSchema),
                    }}
                />
            )}
        </>
    );
}
