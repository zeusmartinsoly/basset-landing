<?php

namespace Database\Seeders;

use App\Models\LandingPageSection;
use Illuminate\Database\Seeder;

class LandingPageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = $this->getSections();

        foreach ($sections as $key => $data) {
            LandingPageSection::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $data['name'],
                    'content' => $data['content'],
                ]
            );
        }
    }

    /**
     * Get all sections data with preserved content from original components.
     *
     * @return array<string, array{name: string, content: array<string, mixed>}>
     */
    private function getSections(): array
    {
        return [
            'navbar' => [
                'name' => 'Navbar',
                'content' => [
                    'logo' => 'brand/logo.svg',
                    'cta_text' => 'احجز مكان',
                    'cta_url' => '#register',
                    'links' => [
                        [
                            'text' => 'محتوى الكامب',
                            'url' => 'https://drive.google.com/file/d/1Y0a6btdSkxcFAWmBf4px9AWJJ5vQLAKi/view?usp=sharing',
                            'external' => true,
                        ],
                        [
                            'text' => 'أعمال الكامبرز',
                            'url' => 'https://drive.google.com/file/d/1MyTF6DDiE3kZ-98C7YgJKZ0rS2dJc90I/view?usp=sharing',
                            'external' => true,
                        ],
                        [
                            'text' => 'بيهانس',
                            'url' => 'https://www.behance.net/baseet464',
                            'external' => true,
                        ],
                    ],
                ],
            ],

            'hero' => [
                'name' => 'Hero Section',
                'content' => [
                    'arabic_quote' => "لقرارات وتحكّم .. مفيش رمي نرد\nفي فهم إمتى وإزاي ترميه.",
                    'english_text' => "Branding Is Thinking\nBefore Design.",
                    'edition' => 'THIRD EDITION',
                    'brand_name' => 'علامات',
                    'brand_number' => '103',
                    'year' => '2026',
                    'images' => [
                        'bran_svg' => 'hero/bran.svg',
                        'dat_svg' => 'hero/dat.svg',
                        'number_svg' => 'hero/number-103.svg',
                        'dice_left' => 'hero/dice-left.webp',
                        'dice_right' => 'hero/dice-right.webp',
                    ],
                ],
            ],

            'about' => [
                'name' => 'About Section',
                'content' => [
                    'title' => "ايه هو\nكامب العلامات™!",
                    'description_1' => 'هــو كامب لايــف، قايم علـــى المتابعة المباشرة ، موجه للمصممين المهتمين بصناعــة الهويات والعلامـــات التجارية اللي شــغالين فريلانس أو في شركات، وعايزيــن يطلعــوا من مرحلــة التنفيذ لمرحلة التفكير والسيطرة والإبداع.',
                    'description_2' => 'مــش كورس، ومــش ورشــة، ومش فيديوهات مســجلة. هــو بيئة متقفلة بتتعلــم فيهــا إزاي تفكّر صــح، وتاخد قرارات براندنج محســوبة، وتنفّذ وانت فاهم إنت بتعمل إيه وليه.',
                    'images' => [
                        'image_1' => 'hero/assets-web-05.webp',
                        'image_2' => 'hero/assets-web-06.webp',
                    ],
                ],
            ],

            'cta' => [
                'name' => 'CTA Section',
                'content' => [
                    'button_text' => 'احجز مكان',
                    'button_url' => '#register',
                    'tagline' => "We Don't Design Brands ..\nWe Build them!",
                ],
            ],

            'gallery' => [
                'name' => 'Gallery Section',
                'content' => [
                    'row_1_images' => [
                        'gallery/camp-preview-01.webp',
                        'gallery/camp-preview-02.webp',
                        'gallery/camp-preview-03.webp',
                        'gallery/camp-preview-04.webp',
                        'gallery/camp-preview-05.webp',
                    ],
                    'row_2_images' => [
                        'gallery/camp-preview-06.webp',
                        'gallery/camp-preview-07.webp',
                        'gallery/camp-preview-08.webp',
                        'gallery/camp-preview-09.webp',
                        'gallery/camp-preview-10.webp',
                    ],
                ],
            ],

            'for_whom' => [
                'name' => 'For Whom Section',
                'content' => [
                    'title' => 'الكامب ده ليك!',
                    'subtitle' => 'FOR WHOM?',
                    'items' => [
                        [
                            'text' => 'لو بتشتغل على Photoshop و Illustrator',
                            'description' => 'وعنـــدك أســـاس كويس في أدوات التصميم.',
                            'highlight' => true,
                            'color' => 'white',
                        ],
                        [
                            'text' => 'لو صمّمــت لوجوهات قبل كده',
                            'description' => 'حتــى لو كانت بســيطة، وعارف يعني إيه تبدأ فكرة من الصفر.',
                            'highlight' => true,
                            'color' => 'white',
                        ],
                        [
                            'text' => 'لــو مهتــم بالبراند ســتراتيجي',
                            'description' => 'أو عندك أي قصور في أي جزئية مهمــا كانــت في بروســيس البراندنج.',
                            'highlight' => false,
                            'color' => 'white',
                        ],
                        [
                            'text' => 'لــو عايز تطلع من مرحلة اللوجو',
                            'description' => 'وتدخــل في مرحلة بناء العلامة التجارية من الأساس.',
                            'highlight' => false,
                            'color' => 'white',
                        ],
                        [
                            'text' => 'لو بتــدور تــزود قيمة شــغلك وتســعيرتك',
                            'description' => 'وتشــتغل علــى مشاريع أكبر مع عملاء أتقل.',
                            'highlight' => false,
                            'color' => 'red',
                        ],
                        [
                            'text' => 'لو محتاج Framework واضح',
                            'description' => 'تمشي عليــه في بناء البراند من غير تخمين أو عشوائية.',
                            'highlight' => false,
                            'color' => 'red',
                        ],
                    ],
                ],
            ],

            'phases' => [
                'name' => 'Phases Section',
                'content' => [
                    'phases' => [
                        ['number' => '01', 'title' => 'Foundation'],
                        ['number' => '02', 'title' => 'Brand Strategy Deck'],
                        ['number' => '03', 'title' => 'Translate Visually'],
                        ['number' => '04', 'title' => 'Full Brand System'],
                        ['number' => '05', 'title' => 'Delivery'],
                    ],
                    'image' => 'brandat-withai.svg',
                ],
            ],

            'founder' => [
                'name' => 'Founder Section',
                'content' => [
                    'founder_image' => 'assets/founder.webp',
                    'background_image' => 'alamat103.svg',
                    'founder_name' => 'Mohamed Baseet',
                    'founder_title' => 'Founder',
                ],
            ],

            'intro' => [
                'name' => 'Intro Section',
                'content' => [
                    'title' => 'نتعرف؟',
                    'paragraphs' => [
                        "انا محمد , هتقولي يا \"بسيط\".\nمصمم جرافيك بخبرة {years_design} سنة في التصميم و أكثر من\n{years_branding} سـنوات في مجال البراندنج , اشـتغلت في الخليج\nوأوروبا وكندا وعملت مشـاريع كبيرة في اكتر من بلد!\nأخــدت دهبية جريدلاينرز في تصميم الهويات البصرية\nوجايزة مشروع العام!",
                        "وأخدت جايزتين (المركزين الأول والثاني) لسنتين علي\nالتوالــي في أكبر إيفنــت يخص التصميم فــي العالم\nالعربي (جرافيك ديزاين بالعربي).",
                        'جاهز تتعرف عليا وتبقى ابن خالتي؟',
                    ],
                    'years_design' => 13,
                    'years_branding' => 7,
                    'award_image' => 'assets/award.webp',
                    'cta_title' => "ابتدي رحلة بناء العلامات التجارية من استراتيجية العلامة\nوحتى عملية التسليم وعرض الهوية البصرية.",
                    'cta_button_text' => 'احجز مكان',
                    'cta_button_url' => '#register',
                ],
            ],

            'work' => [
                'name' => 'Work Showcase Section',
                'content' => [
                    'title' => 'أعمالي',
                    'subtitle' => 'مشاريع حقيقية',
                    'instructions' => 'اضغط على الصورة لعرضها',
                ],
            ],

            'footer' => [
                'name' => 'Footer Section',
                'content' => [
                    'social_links' => [
                        [
                            'name' => 'Behance',
                            'icon' => 'social/icons-01.webp',
                            'url' => 'https://www.behance.net/baseet464',
                        ],
                        [
                            'name' => 'Facebook',
                            'icon' => 'social/icons-02.webp',
                            'url' => 'https://www.facebook.com/share/1893Sb8hSv/?mibextid=wwXIfr',
                        ],
                        [
                            'name' => 'LinkedIn',
                            'icon' => 'social/icons-03.webp',
                            'url' => 'https://www.linkedin.com/in/baseet464/',
                        ],
                        [
                            'name' => 'Instagram',
                            'icon' => 'social/icons-04.webp',
                            'url' => 'https://www.instagram.com/baseet464?igsh=ZWw4OWdibTZtcmRz&utm_source=qr',
                        ],
                    ],
                    'copyright_text' => 'All Rights Reserved @Baseet 2026',
                    'developer_credit' => 'Developed by Cerebro AI',
                ],
            ],
        ];
    }
}
