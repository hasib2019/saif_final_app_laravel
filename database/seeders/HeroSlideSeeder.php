<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroSlide;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroSlides = [
            [
                'title' => [
                    'en' => 'Advanced Industrial Automation Solutions',
                    'ar' => 'حلول الأتمتة الصناعية المتقدمة',
                    'bn' => 'উন্নত শিল্প অটোমেশন সমাধান'
                ],
                'subtitle' => [
                    'en' => 'Powering the Future of Manufacturing',
                    'ar' => 'تشغيل مستقبل التصنيع',
                    'bn' => 'উৎপাদনের ভবিষ্যতকে শক্তিশালী করা'
                ],
                'description' => [
                    'en' => 'Discover cutting-edge automation technologies that transform industrial processes and boost productivity.',
                    'ar' => 'اكتشف تقنيات الأتمتة المتطورة التي تحول العمليات الصناعية وتعزز الإنتاجية.',
                    'bn' => 'অত্যাধুনিক অটোমেশন প্রযুক্তি আবিষ্কার করুন যা শিল্প প্রক্রিয়াকে রূপান্তরিত করে এবং উৎপাদনশীলতা বৃদ্ধি করে।'
                ],
                'image' => '/images/hero/automation-hero.jpg',
                'button_text' => [
                    'en' => 'Explore Solutions',
                    'ar' => 'استكشف الحلول',
                    'bn' => 'সমাধান অন্বেষণ করুন'
                ],
                'button_link' => '/products',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Smart Manufacturing Excellence',
                    'ar' => 'التميز في التصنيع الذكي',
                    'bn' => 'স্মার্ট উৎপাদন উৎকর্ষতা'
                ],
                'subtitle' => [
                    'en' => 'Industry 4.0 Ready Solutions',
                    'ar' => 'حلول جاهزة للصناعة 4.0',
                    'bn' => 'ইন্ডাস্ট্রি ৪.০ প্রস্তুত সমাধান'
                ],
                'description' => [
                    'en' => 'Embrace the digital transformation with our comprehensive suite of smart manufacturing technologies.',
                    'ar' => 'احتضن التحول الرقمي مع مجموعتنا الشاملة من تقنيات التصنيع الذكي.',
                    'bn' => 'আমাদের স্মার্ট উৎপাদন প্রযুক্তির ব্যাপক স্যুটের সাথে ডিজিটাল রূপান্তরকে আলিঙ্গন করুন।'
                ],
                'image' => '/images/hero/smart-manufacturing.jpg',
                'button_text' => [
                    'en' => 'Learn More',
                    'ar' => 'اعرف المزيد',
                    'bn' => 'আরও জানুন'
                ],
                'button_link' => '/about',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Reliable Partnership for Growth',
                    'ar' => 'شراكة موثوقة للنمو',
                    'bn' => 'বৃদ্ধির জন্য নির্ভরযোগ্য অংশীদারিত্ব'
                ],
                'subtitle' => [
                    'en' => 'Your Trusted Technology Partner',
                    'ar' => 'شريكك التقني الموثوق',
                    'bn' => 'আপনার বিশ্বস্ত প্রযুক্তি অংশীদার'
                ],
                'description' => [
                    'en' => 'Building lasting relationships through innovative solutions and exceptional service excellence.',
                    'ar' => 'بناء علاقات دائمة من خلال الحلول المبتكرة والتميز في الخدمة الاستثنائية.',
                    'bn' => 'উদ্ভাবনী সমাধান এবং ব্যতিক্রমী সেবা উৎকর্ষতার মাধ্যমে দীর্ঘস্থায়ী সম্পর্ক গড়ে তোলা।'
                ],
                'image' => '/images/hero/partnership.jpg',
                'button_text' => [
                    'en' => 'Contact Us',
                    'ar' => 'اتصل بنا',
                    'bn' => 'যোগাযোগ করুন'
                ],
                'button_link' => '/contact',
                'sort_order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($heroSlides as $slide) {
            HeroSlide::create($slide);
        }
    }
}
