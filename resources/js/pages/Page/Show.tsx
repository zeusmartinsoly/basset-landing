import { Head, Link } from '@inertiajs/react';

interface PageData {
    title: string;
    content: string;
    meta_title: string;
    meta_description: string | null;
}

interface ShowProps {
    page: PageData;
}

export default function Show({ page }: ShowProps) {
    return (
        <>
            <Head>
                <title>{page.meta_title}</title>
                {page.meta_description && (
                    <meta name="description" content={page.meta_description} />
                )}
            </Head>

            <div className="min-h-screen bg-[#1a1a1a]">
                {/* Simple Header */}
                <header className="border-b border-white/10 bg-black/50 backdrop-blur-sm">
                    <div className="mx-auto max-w-4xl px-4 py-4">
                        <Link
                            href="/"
                            className="inline-flex items-center gap-2 text-white/70 transition-colors hover:text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            <span>العودة للرئيسية</span>
                        </Link>
                    </div>
                </header>

                {/* Page Content */}
                <main className="mx-auto max-w-4xl px-4 py-12">
                    <article dir="rtl" className="prose prose-lg prose-invert max-w-none">
                        <h1 className="mb-8 text-3xl font-bold text-white md:text-4xl">
                            {page.title}
                        </h1>

                        <div
                            dir="rtl"
                            className="text-white/80 [&_a]:text-[#D4A853] [&_a]:underline [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-white [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-white [&_li]:my-2 [&_ol]:list-decimal [&_ol]:pr-6 [&_p]:my-4 [&_p]:leading-relaxed [&_ul]:list-disc [&_ul]:pr-6"
                            dangerouslySetInnerHTML={{ __html: page.content }}
                        />
                    </article>
                </main>

                {/* Simple Footer */}
                <footer className="border-t border-white/10 bg-black/50">
                    <div className="mx-auto max-w-4xl px-4 py-6 text-center text-sm text-white/50">
                        © {new Date().getFullYear()} Baseet Landing. All rights reserved.
                    </div>
                </footer>
            </div>
        </>
    );
}
