<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TechnologyFeature;

class TechnologyFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'title' => [
                    'en' => 'Real-time Monitoring',
                    'ar' => 'المراقبة في الوقت الفعلي',
                    'bn' => 'রিয়েল-টাইম পর্যবেক্ষণ'
                ],
                'description' => [
                    'en' => 'Advanced real-time monitoring capabilities for comprehensive system oversight and immediate response.',
                    'ar' => 'قدرات المراقبة المتقدمة في الوقت الفعلي للإشراف الشامل على النظام والاستجابة الفورية.',
                    'bn' => 'ব্যাপক সিস্টেম তত্ত্বাবধান এবং তাৎক্ষণিক প্রতিক্রিয়ার জন্য উন্নত রিয়েল-টাইম পর্যবেক্ষণ ক্ষমতা।'
                ],
                'icon' => 'monitor',
                'category' => 'monitoring',
                'benefits' => [
                    'en' => [
                        'Instant alerts and notifications',
                        'Comprehensive data visualization',
                        'Historical trend analysis',
                        'Predictive maintenance insights'
                    ],
                    'ar' => [
                        'التنبيهات والإشعارات الفورية',
                        'تصور البيانات الشامل',
                        'تحليل الاتجاهات التاريخية',
                        'رؤى الصيانة التنبؤية'
                    ],
                    'bn' => [
                        'তাৎক্ষণিক সতর্কতা এবং বিজ্ঞপ্তি',
                        'ব্যাপক ডেটা ভিজুয়ালাইজেশন',
                        'ঐতিহাসিক প্রবণতা বিশ্লেষণ',
                        'প্রেডিক্টিভ রক্ষণাবেক্ষণ অন্তর্দৃষ্টি'
                    ]
                ],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Cloud Integration',
                    'ar' => 'التكامل السحابي',
                    'bn' => 'ক্লাউড ইন্টিগ্রেশন'
                ],
                'description' => [
                    'en' => 'Seamless cloud integration for enhanced scalability, accessibility, and data management.',
                    'ar' => 'التكامل السحابي السلس لتعزيز قابلية التوسع وإمكانية الوصول وإدارة البيانات.',
                    'bn' => 'উন্নত স্কেলেবিলিটি, অ্যাক্সেসিবিলিটি এবং ডেটা ম্যানেজমেন্টের জন্য নিরবচ্ছিন্ন ক্লাউড ইন্টিগ্রেশন।'
                ],
                'icon' => 'cloud',
                'category' => 'connectivity',
                'benefits' => [
                    'en' => [
                        'Remote access and control',
                        'Automatic data backup',
                        'Scalable infrastructure',
                        'Global accessibility'
                    ],
                    'ar' => [
                        'الوصول والتحكم عن بُعد',
                        'النسخ الاحتياطي التلقائي للبيانات',
                        'البنية التحتية القابلة للتوسع',
                        'إمكانية الوصول العالمي'
                    ],
                    'bn' => [
                        'দূরবর্তী অ্যাক্সেস এবং নিয়ন্ত্রণ',
                        'স্বয়ংক্রিয় ডেটা ব্যাকআপ',
                        'স্কেলেবল অবকাঠামো',
                        'বৈশ্বিক অ্যাক্সেসিবিলিটি'
                    ]
                ],
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'AI-Powered Analytics',
                    'ar' => 'التحليلات المدعومة بالذكاء الاصطناعي',
                    'bn' => 'এআই-চালিত বিশ্লেষণ'
                ],
                'description' => [
                    'en' => 'Advanced AI algorithms for predictive analytics, pattern recognition, and intelligent insights.',
                    'ar' => 'خوارزميات الذكاء الاصطناعي المتقدمة للتحليلات التنبؤية والتعرف على الأنماط والرؤى الذكية.',
                    'bn' => 'ভবিষ্যদ্বাণীমূলক বিশ্লেষণ, প্যাটার্ন স্বীকৃতি এবং বুদ্ধিমান অন্তর্দৃষ্টির জন্য উন্নত এআই অ্যালগরিদম।'
                ],
                'icon' => 'brain',
                'category' => 'analytics',
                'benefits' => [
                    'en' => [
                        'Predictive maintenance',
                        'Anomaly detection',
                        'Performance optimization',
                        'Data-driven decisions'
                    ],
                    'ar' => [
                        'الصيانة التنبؤية',
                        'كشف الشذوذ',
                        'تحسين الأداء',
                        'القرارات المبنية على البيانات'
                    ],
                    'bn' => [
                        'ভবিষ্যদ্বাণীমূলক রক্ষণাবেক্ষণ',
                        'অস্বাভাবিকতা সনাক্তকরণ',
                        'কর্মক্ষমতা অপ্টিমাইজেশন',
                        'ডেটা-চালিত সিদ্ধান্ত'
                    ]
                ],
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Cybersecurity Protection',
                    'ar' => 'حماية الأمن السيبراني',
                    'bn' => 'সাইবার নিরাপত্তা সুরক্ষা'
                ],
                'description' => [
                    'en' => 'Comprehensive cybersecurity measures to protect industrial systems from threats and vulnerabilities.',
                    'ar' => 'تدابير الأمن السيبراني الشاملة لحماية الأنظمة الصناعية من التهديدات والثغرات الأمنية.',
                    'bn' => 'হুমকি এবং দুর্বলতা থেকে শিল্প ব্যবস্থা রক্ষা করার জন্য ব্যাপক সাইবার নিরাপত্তা ব্যবস্থা।'
                ],
                'icon' => 'shield',
                'category' => 'security',
                'benefits' => [
                    'en' => [
                        'Threat detection and prevention',
                        'Secure data transmission',
                        'Access control management',
                        'Compliance with standards'
                    ],
                    'ar' => [
                        'كشف ومنع التهديدات',
                        'نقل البيانات الآمن',
                        'إدارة التحكم في الوصول',
                        'الامتثال للمعايير'
                    ],
                    'bn' => [
                        'হুমকি সনাক্তকরণ এবং প্রতিরোধ',
                        'নিরাপদ ডেটা ট্রান্সমিশন',
                        'অ্যাক্সেস নিয়ন্ত্রণ ব্যবস্থাপনা',
                        'মানদণ্ডের সাথে সম্মতি'
                    ]
                ],
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Mobile Accessibility',
                    'ar' => 'إمكانية الوصول عبر الهاتف المحمول',
                    'bn' => 'মোবাইল অ্যাক্সেসিবিলিটি'
                ],
                'description' => [
                    'en' => 'Mobile-responsive interfaces and applications for on-the-go monitoring and control.',
                    'ar' => 'واجهات وتطبيقات متجاوبة مع الهاتف المحمول للمراقبة والتحكم أثناء التنقل.',
                    'bn' => 'চলমান মনিটরিং এবং নিয়ন্ত্রণের জন্য মোবাইল-রেসপন্সিভ ইন্টারফেস এবং অ্যাপ্লিকেশন।'
                ],
                'icon' => 'mobile',
                'category' => 'accessibility',
                'benefits' => [
                    'en' => [
                        'Remote monitoring',
                        'Mobile notifications',
                        'Touch-friendly interface',
                        'Offline capabilities'
                    ],
                    'ar' => [
                        'المراقبة عن بُعد',
                        'إشعارات الهاتف المحمول',
                        'واجهة سهلة اللمس',
                        'قدرات العمل دون اتصال'
                    ],
                    'bn' => [
                        'দূরবর্তী মনিটরিং',
                        'মোবাইল নোটিফিকেশন',
                        'স্পর্শ-বান্ধব ইন্টারফেস',
                        'অফলাইন ক্ষমতা'
                    ]
                ],
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'title' => [
                    'en' => 'Energy Efficiency',
                    'ar' => 'كفاءة الطاقة',
                    'bn' => 'শক্তি দক্ষতা'
                ],
                'description' => [
                    'en' => 'Smart energy management systems to optimize power consumption and reduce operational costs.',
                    'ar' => 'أنظمة إدارة الطاقة الذكية لتحسين استهلاك الطاقة وتقليل التكاليف التشغيلية.',
                    'bn' => 'বিদ্যুৎ খরচ অপ্টিমাইজ করতে এবং পরিচালনা খরচ কমাতে স্মার্ট শক্তি ব্যবস্থাপনা সিস্টেম।'
                ],
                'icon' => 'leaf',
                'category' => 'sustainability',
                'benefits' => [
                    'en' => [
                        'Reduced energy consumption',
                        'Cost savings',
                        'Environmental sustainability',
                        'Smart power management'
                    ],
                    'ar' => [
                        'تقليل استهلاك الطاقة',
                        'توفير التكاليف',
                        'الاستدامة البيئية',
                        'إدارة الطاقة الذكية'
                    ],
                    'bn' => [
                        'হ্রাসকৃত শক্তি খরচ',
                        'খরচ সাশ্রয়',
                        'পরিবেশগত স্থায়িত্ব',
                        'স্মার্ট পাওয়ার ম্যানেজমেন্ট'
                    ]
                ],
                'sort_order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($features as $feature) {
            TechnologyFeature::create($feature);
        }
    }
}
