<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyInfo;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\PressRelease;
use App\Models\Partner;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Company Info
        CompanyInfo::create([
            'mission' => [
                'en' => 'To empower businesses through cutting-edge technology solutions that drive growth and innovation.',
                'ar' => 'تمكين الشركات من خلال حلول التكنولوجيا المتطورة التي تدفع النمو والابتكار.'
            ],
            'history' => [
                'en' => 'Founded in 2015, Derown Technology has grown from a small startup to a leading technology solutions provider, serving clients worldwide with innovative software development and digital transformation services.',
                'ar' => 'تأسست تكنولوجيا ديراون في عام 2015، ونمت من شركة ناشئة صغيرة إلى مزود رائد لحلول التكنولوجيا، تخدم العملاء في جميع أنحاء العالم بخدمات تطوير البرمجيات المبتكرة والتحول الرقمي.'
            ],
            'values' => [
                'en' => 'Innovation, Excellence, Integrity, Customer Focus, Teamwork',
                'ar' => 'الابتكار، التميز، النزاهة، التركيز على العملاء، العمل الجماعي'
            ],
            'initiatives' => [
                'en' => 'We are committed to sustainability, community development, and fostering innovation through our various corporate social responsibility programs.',
                'ar' => 'نحن ملتزمون بالاستدامة وتنمية المجتمع وتعزيز الابتكار من خلال برامج المسؤولية الاجتماعية المختلفة للشركات.'
            ],
            'is_active' => true
        ]);

        // Create Product Categories
        $webCategory = ProductCategory::firstOrCreate(
            ['slug' => 'web-development'],
            [
                'name' => [
                    'en' => 'Web Development',
                    'ar' => 'تطوير الويب'
                ],
                'description' => [
                    'en' => 'Custom web applications and websites built with modern technologies.',
                    'ar' => 'تطبيقات ومواقع ويب مخصصة مبنية بتقنيات حديثة.'
                ],
                'is_active' => true,
                'sort_order' => 1
            ]
        );

        $mobileCategory = ProductCategory::firstOrCreate(
            ['slug' => 'mobile-development'],
            [
                'name' => [
                    'en' => 'Mobile Development',
                    'ar' => 'تطوير الهاتف المحمول'
                ],
                'description' => [
                    'en' => 'Native and cross-platform mobile applications for iOS and Android.',
                    'ar' => 'تطبيقات الهاتف المحمول الأصلية ومتعددة المنصات لنظامي iOS و Android.'
                ],
                'is_active' => true,
                'sort_order' => 2
            ]
        );

        $cloudCategory = ProductCategory::firstOrCreate(
            ['slug' => 'cloud-solutions'],
            [
                'name' => [
                    'en' => 'Cloud Solutions',
                    'ar' => 'حلول السحابة'
                ],
                'description' => [
                    'en' => 'Scalable cloud infrastructure and services for modern businesses.',
                    'ar' => 'بنية تحتية سحابية قابلة للتطوير وخدمات للشركات الحديثة.'
                ],
                'is_active' => true,
                'sort_order' => 3
            ]
        );

        // Create Products (only if none exist)
        if (Product::count() == 0) {
            Product::create([
                'name' => [
                    'en' => 'E-Commerce Platform',
                    'ar' => 'منصة التجارة الإلكترونية'
                ],
                'description' => [
                    'en' => 'Complete e-commerce solution with advanced features for online businesses.',
                    'ar' => 'حل تجارة إلكترونية كامل مع ميزات متقدمة للأعمال التجارية عبر الإنترنت.'
                ],
                'specifications' => [
                    'en' => 'Multi-vendor support, Payment gateway integration, Inventory management, Analytics dashboard',
                    'ar' => 'دعم متعدد البائعين، تكامل بوابة الدفع، إدارة المخزون، لوحة تحليلات'
                ],
                'category_id' => $webCategory->id,
                'is_active' => true,
                'sort_order' => 1
            ]);

            Product::create([
                'name' => [
                    'en' => 'Mobile Banking App',
                    'ar' => 'تطبيق الخدمات المصرفية المحمولة'
                ],
                'description' => [
                    'en' => 'Secure mobile banking application with biometric authentication.',
                    'ar' => 'تطبيق خدمات مصرفية محمولة آمن مع مصادقة بيومترية.'
                ],
                'specifications' => [
                    'en' => 'Biometric login, Real-time transactions, Bill payments, Investment tracking',
                    'ar' => 'تسجيل دخول بيومتري، معاملات فورية، دفع الفواتير، تتبع الاستثمارات'
                ],
                'category_id' => $mobileCategory->id,
                'is_active' => true,
                'sort_order' => 2
            ]);

            Product::create([
                'name' => [
                    'en' => 'Cloud Infrastructure Management',
                    'ar' => 'إدارة البنية التحتية السحابية'
                ],
                'description' => [
                    'en' => 'Comprehensive cloud management platform for enterprise infrastructure.',
                    'ar' => 'منصة إدارة سحابية شاملة للبنية التحتية للمؤسسات.'
                ],
                'specifications' => [
                    'en' => 'Auto-scaling, Load balancing, Monitoring & alerts, Cost optimization',
                    'ar' => 'التوسع التلقائي، توزيع الأحمال، المراقبة والتنبيهات، تحسين التكلفة'
                ],
                'category_id' => $cloudCategory->id,
                'is_active' => true,
                'sort_order' => 3
            ]);
        }

        // Create Press Releases
        PressRelease::create([
            'title' => [
                'en' => 'Derown Technology Launches Revolutionary AI Platform',
                'ar' => 'تكنولوجيا ديراون تطلق منصة ذكاء اصطناعي ثورية'
            ],
            'content' => [
                'en' => 'Derown Technology today announced the launch of its groundbreaking AI platform that will transform how businesses operate. The platform combines machine learning, natural language processing, and computer vision to deliver unprecedented automation capabilities.',
                'ar' => 'أعلنت تكنولوجيا ديراون اليوم عن إطلاق منصة الذكاء الاصطناعي الرائدة التي ستحول طريقة عمل الشركات. تجمع المنصة بين التعلم الآلي ومعالجة اللغة الطبيعية ورؤية الكمبيوتر لتقديم قدرات أتمتة غير مسبوقة.'
            ],
            'description' => [
                'en' => 'Revolutionary AI platform launched to transform business operations.',
                'ar' => 'إطلاق منصة ذكاء اصطناعي ثورية لتحويل العمليات التجارية.'
            ],
            'published_at' => now()->subDays(5),
            'is_active' => true
        ]);

        PressRelease::create([
            'title' => [
                'en' => 'Partnership with Global Tech Leader Announced',
                'ar' => 'الإعلان عن شراكة مع رائد تقني عالمي'
            ],
            'content' => [
                'en' => 'We are excited to announce our strategic partnership with a leading global technology company. This collaboration will enhance our ability to deliver cutting-edge solutions to our clients worldwide.',
                'ar' => 'نحن متحمسون للإعلان عن شراكتنا الاستراتيجية مع شركة تكنولوجيا عالمية رائدة. ستعزز هذه الشراكة قدرتنا على تقديم حلول متطورة لعملائنا في جميع أنحاء العالم.'
            ],
            'description' => [
                'en' => 'Strategic partnership to enhance global technology solutions.',
                'ar' => 'شراكة استراتيجية لتعزيز حلول التكنولوجيا العالمية.'
            ],
            'published_at' => now()->subDays(10),
            'is_active' => true
        ]);

        // Create Partners
        Partner::create([
            'name' => 'TechCorp Solutions',
            'description' => [
                'en' => 'Leading technology consulting firm specializing in digital transformation.',
                'ar' => 'شركة استشارات تكنولوجية رائدة متخصصة في التحول الرقمي.'
            ],
            'website_url' => 'https://techcorp.com',
            'is_active' => true,
            'sort_order' => 1
        ]);

        Partner::create([
            'name' => 'Innovation Labs',
            'description' => [
                'en' => 'Research and development partner for emerging technologies.',
                'ar' => 'شريك البحث والتطوير للتقنيات الناشئة.'
            ],
            'website_url' => 'https://innovationlabs.com',
            'is_active' => true,
            'sort_order' => 2
        ]);

        Partner::create([
            'name' => 'Global Systems Inc',
            'description' => [
                'en' => 'International systems integration and implementation partner.',
                'ar' => 'شريك دولي لتكامل وتنفيذ الأنظمة.'
            ],
            'website_url' => 'https://globalsystems.com',
            'is_active' => true,
            'sort_order' => 3
        ]);
    }
}