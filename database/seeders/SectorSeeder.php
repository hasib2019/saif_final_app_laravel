<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sector;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            [
                'name' => [
                    'en' => 'Manufacturing',
                    'ar' => 'التصنيع',
                    'bn' => 'উৎপাদন'
                ],
                'description' => [
                    'en' => 'Advanced automation solutions for modern manufacturing processes, improving efficiency and quality.',
                    'ar' => 'حلول الأتمتة المتقدمة لعمليات التصنيع الحديثة، تحسين الكفاءة والجودة.',
                    'bn' => 'আধুনিক উৎপাদন প্রক্রিয়ার জন্য উন্নত অটোমেশন সমাধান, দক্ষতা এবং গুণমান উন্নত করে।'
                ],
                'icon' => 'factory',
                'image' => '/images/sectors/manufacturing.jpg',
                'use_cases' => [
                    'en' => [
                        'Assembly line automation',
                        'Quality control systems',
                        'Production monitoring',
                        'Inventory management'
                    ],
                    'ar' => [
                        'أتمتة خط التجميع',
                        'أنظمة مراقبة الجودة',
                        'مراقبة الإنتاج',
                        'إدارة المخزون'
                    ],
                    'bn' => [
                        'অ্যাসেম্বলি لাইন অটোমেশন',
                        'গুণমান নিয়ন্ত্রণ সিস্টেম',
                        'উৎপাদন পর্যবেক্ষণ',
                        'ইনভেন্টরি ব্যবস্থাপনা'
                    ]
                ],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Oil & Gas',
                    'ar' => 'النفط والغاز',
                    'bn' => 'তেল ও গ্যাস'
                ],
                'description' => [
                    'en' => 'Robust automation and control systems designed for the demanding oil and gas industry.',
                    'ar' => 'أنظمة الأتمتة والتحكم القوية المصممة لصناعة النفط والغاز المتطلبة.',
                    'bn' => 'চাহিদাপূর্ণ তেল ও গ্যাস শিল্পের জন্য ডিজাইন করা শক্তিশালী অটোমেশন এবং নিয়ন্ত্রণ সিস্টেম।'
                ],
                'icon' => 'oil-pump',
                'image' => '/images/sectors/oil-gas.jpg',
                'use_cases' => [
                    'en' => [
                        'Pipeline monitoring',
                        'Refinery automation',
                        'Safety systems',
                        'Environmental monitoring'
                    ],
                    'ar' => [
                        'مراقبة خطوط الأنابيب',
                        'أتمتة المصافي',
                        'أنظمة السلامة',
                        'المراقبة البيئية'
                    ],
                    'bn' => [
                        'পাইপলাইন পর্যবেক্ষণ',
                        'রিফাইনারি অটোমেশন',
                        'নিরাপত্তা সিস্টেম',
                        'পরিবেশগত পর্যবেক্ষণ'
                    ]
                ],
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Water Treatment',
                    'ar' => 'معالجة المياه',
                    'bn' => 'পানি শোধন'
                ],
                'description' => [
                    'en' => 'Intelligent water treatment solutions ensuring clean, safe water for communities and industries.',
                    'ar' => 'حلول معالجة المياه الذكية لضمان المياه النظيفة والآمنة للمجتمعات والصناعات.',
                    'bn' => 'সম্প্রদায় এবং শিল্পের জন্য পরিষ্কার, নিরাপদ পানি নিশ্চিত করে বুদ্ধিমান পানি শোধন সমাধান।'
                ],
                'icon' => 'water-drop',
                'image' => '/images/sectors/water-treatment.jpg',
                'use_cases' => [
                    'en' => [
                        'Water quality monitoring',
                        'Treatment process automation',
                        'Distribution control',
                        'Waste water management'
                    ],
                    'ar' => [
                        'مراقبة جودة المياه',
                        'أتمتة عمليات المعالجة',
                        'التحكم في التوزيع',
                        'إدارة مياه الصرف'
                    ],
                    'bn' => [
                        'পানির গুণমান পর্যবেক্ষণ',
                        'শোধন প্রক্রিয়া অটোমেশন',
                        'বিতরণ নিয়ন্ত্রণ',
                        'বর্জ্য পানি ব্যবস্থাপনা'
                    ]
                ],
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Power Generation',
                    'ar' => 'توليد الطاقة',
                    'bn' => 'বিদ্যুৎ উৎপাদন'
                ],
                'description' => [
                    'en' => 'Reliable power generation control systems for sustainable and efficient energy production.',
                    'ar' => 'أنظمة التحكم الموثوقة في توليد الطاقة لإنتاج الطاقة المستدامة والفعالة.',
                    'bn' => 'টেকসই এবং দক্ষ শক্তি উৎপাদনের জন্য নির্ভরযোগ্য বিদ্যুৎ উৎপাদন নিয়ন্ত্রণ সিস্টেম।'
                ],
                'icon' => 'lightning-bolt',
                'image' => '/images/sectors/power-generation.jpg',
                'use_cases' => [
                    'en' => [
                        'Grid management',
                        'Renewable energy integration',
                        'Load balancing',
                        'Power quality monitoring'
                    ],
                    'ar' => [
                        'إدارة الشبكة',
                        'تكامل الطاقة المتجددة',
                        'توازن الأحمال',
                        'مراقبة جودة الطاقة'
                    ],
                    'bn' => [
                        'গ্রিড ব্যবস্থাপনা',
                        'নবায়নযোগ্য শক্তি একীকরণ',
                        'লোড ব্যালেন্সিং',
                        'বিদ্যুৎ গুণমান পর্যবেক্ষণ'
                    ]
                ],
                'sort_order' => 4,
                'is_active' => true
            ]
        ];

        foreach ($sectors as $sector) {
            Sector::create($sector);
        }
    }
}
