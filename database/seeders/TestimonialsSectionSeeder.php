<?php

namespace Database\Seeders;

use App\Models\LandingPageSection;
use Illuminate\Database\Seeder;

class TestimonialsSectionSeeder extends Seeder
{
    /**
     * Seed the homepage testimonials section only (see landing_page_sections.key = testimonials).
     */
    public function run(): void
    {
        LandingPageSection::updateOrCreate(
            ['key' => 'testimonials'],
            [
                'name' => 'Testimonials Section',
                'content' => [
                    'visible' => true,
                    'navbar_link_text' => 'رأي الكامبرز',
                    'heading' => 'رأي الكامبرز',
                    'items' => [
                        [
                            'image' => 'gallery/camp-preview-02.webp',
                            'name' => 'نورا السيد',
                            'course' => 'كامب العلامات 103',
                            'content' => "كتير كنت بخلط بين الهوية البصرية وبين استراتيجية العلامة. في الكامب اتعلمت أكتب موضع واضح وأترجم القيم لقرارات تصميم مش بس شكل لطيف في الشاشة.\nدلوقتي لما بعرض على عميل، بقدر أشرح ليه الاختيار ده مش ذوق شخصي.",
                        ],
                        [
                            'image' => 'gallery/camp-preview-04.webp',
                            'name' => 'يوسف الشناوي',
                            'course' => 'كامب العلامات 102',
                            'content' => 'أنا كنت بخاف أرفع السعر أو أوضح الفرق بين باقة وباقة. التمرين على عرض القيمة وتقسيم المراحل خلّاني أشتغل بهدوء وأنا عارف السعر مترتب على إيه بالظبط.',
                        ],
                        [
                            'image' => 'gallery/camp-preview-05.webp',
                            'name' => 'مروة صالح',
                            'course' => 'كامب العلامات 103',
                            'content' => "المتابعة اللحظية فعلاً فرقت معايا: مش مجرد محاضرة تمشي وتخلص.\nكل أسبوع كنت برجع للملف وأعدّل قرارات بناءً على ملاحظات حقيقية، فحسيت إن الشغل مطور مش مجرد سلسلة فيديوهات.",
                        ],
                        [
                            'image' => 'assets/founder.webp',
                            'name' => 'عبدالله رفعت',
                            'course' => 'كامب العلامات 101',
                            'content' => 'طبّقت على مشروع عميل حقيقي خطوة بخطوة؛ من استكشاف الجمهور لحد بناء نظام بصري يخدم القصة. ده اللي كان ناقصني طول الوقت — إطار يخلّيني أمشي من غير ما ألف على نفسي.',
                        ],
                        [
                            'image' => 'gallery/camp-preview-07.webp',
                            'name' => 'هبة كمال',
                            'course' => 'كامب العلامات 102',
                            'content' => 'جو المجموعة خلّاني أشوف غلطاتي بدري: حاجات كنت بحاول أبرّرها لنفسي لقيتها بتتكسر قدام النقد الهادي.\nده خلّى ملفاتي بعد الكامب أوضح وأقصر في الكلام الزائد.',
                        ],
                        [
                            'image' => 'gallery/camp-preview-09.webp',
                            'name' => 'طارق منير',
                            'course' => 'كامب العلامات 103',
                            'content' => "بعد ما خلصت، جهّزت عرض تسليم منظم: إيه اللي اتعمل، ليه اتعمل، وإزاي العميل يستخدم النظام بعد كده.\nالعميل حسّ إنه اشترى قرارات مش مجرد ملفات مفتوحة المصدر.",
                        ],
                    ],
                ],
            ]
        );
    }
}
