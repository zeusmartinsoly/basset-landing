export default function ForWhomSection() {
    return (
        <section className="relative w-full bg-white py-16 md:py-24">
            <div className="mx-auto max-w-[1400px] px-4 md:px-8">
                {/* Title */}
                <h2
                    className="mb-12 font-arabic text-6xl font-bold text-black md:mb-16 md:text-7xl lg:text-8xl"
                    dir="rtl"
                >
                    الكامب ده ليك!
                </h2>

                {/* Card Container */}
                <div className="relative">
                    {/* Crosshair Top Left */}
                    <svg
                        className="absolute -top-4 -left-4 h-10 w-10 md:-top-6 md:-left-6 md:h-12 md:w-12"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <line
                            x1="20"
                            y1="0"
                            x2="20"
                            y2="40"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                        <line
                            x1="0"
                            y1="20"
                            x2="40"
                            y2="20"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                    </svg>

                    {/* Crosshair Top Right */}
                    <svg
                        className="absolute -top-4 -right-4 h-10 w-10 md:-top-6 md:-right-6 md:h-12 md:w-12"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <line
                            x1="20"
                            y1="0"
                            x2="20"
                            y2="40"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                        <line
                            x1="0"
                            y1="20"
                            x2="40"
                            y2="20"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                    </svg>

                    {/* Crosshair Bottom Right */}
                    <svg
                        className="absolute -right-4 -bottom-4 h-10 w-10 md:-right-6 md:-bottom-6 md:h-12 md:w-12"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <line
                            x1="20"
                            y1="0"
                            x2="20"
                            y2="40"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                        <line
                            x1="0"
                            y1="20"
                            x2="40"
                            y2="20"
                            stroke="#F02624"
                            strokeWidth="1.5"
                        />
                    </svg>

                    {/* Black Card */}
                    <div
                        className="bg-black px-8 py-12 md:px-14 md:py-16 lg:px-20 lg:py-20"
                        dir="rtl"
                    >
                        <div className="grid grid-cols-1 gap-10 md:grid-cols-3 md:gap-12 lg:gap-16">
                            {/* Column 1 */}
                            <div className="space-y-10 md:space-y-12">
                                {/* Item 1 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-white md:text-2xl">
                                        <span className="font-bold">
                                            لو بتشتغل على Photoshop و
                                            Illustrator
                                        </span>{' '}
                                        وعنـــدك أســـاس كويس في أدوات التصميم.
                                        <span className="font-bold text-[#F02624]">
                                            *
                                        </span>
                                    </p>
                                </div>

                                {/* Item 4 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-white md:text-2xl">
                                        <span className="font-bold">
                                            لو صمّمــت لوجوهات قبل كده
                                        </span>{' '}
                                        حتــى لو كانت بســيطة، وعارف يعني إيه
                                        تبدأ فكرة من الصفر.
                                        <span className="font-bold text-[#F02624]">
                                            *
                                        </span>
                                    </p>
                                </div>
                            </div>

                            {/* Column 2 */}
                            <div className="space-y-10 md:space-y-12">
                                {/* Item 2 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-white md:text-2xl">
                                        <span className="font-bold">
                                            لــو مهتــم بالبراند ســتراتيجي
                                        </span>{' '}
                                        أو عندك أي قصور في أي جزئية مهمــا
                                        كانــت في بروســيس البراندنج.
                                    </p>
                                </div>

                                {/* Item 5 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-white md:text-2xl">
                                        <span className="font-bold">
                                            لــو عايز تطلع من مرحلة اللوجو
                                        </span>{' '}
                                        وتدخــل في مرحلة بناء العلامة التجارية
                                        من الأساس.
                                    </p>
                                </div>
                            </div>

                            {/* Column 3 */}
                            <div className="space-y-10 md:space-y-12">
                                {/* Item 3 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-[#F02624] md:text-2xl">
                                        <span className="font-bold">
                                            لو بتــدور تــزود قيمة شــغلك
                                            وتســعيرتك
                                        </span>{' '}
                                        وتشــتغل علــى مشاريع أكبر مع عملاء
                                        أتقل.
                                    </p>
                                </div>

                                {/* Item 6 */}
                                <div>
                                    <p className="font-arabic text-xl leading-relaxed text-[#F02624] md:text-2xl">
                                        <span className="font-bold">
                                            لو محتاج Framework واضح
                                        </span>{' '}
                                        تمشي عليــه في بناء البراند من غير تخمين
                                        أو عشوائية.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* FOR WHOM? */}
                <div className="-mt-12">
                    <h3
                        className="font-display text-7xl md:text-9xl lg:text-[10rem] text-center"
                        style={{
                            color: '#F02624',
                        }}
                    >
                        FOR WHOM?
                    </h3>
                </div>
            </div>
        </section>
    );
}
