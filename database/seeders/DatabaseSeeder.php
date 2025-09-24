<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wing;
use App\Models\region;
use App\Models\Driver;
use App\Models\Teacher;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) المستخدم
        User::create([
            'name' => 'لطفي باشادي',
            'password' => Hash::make('123456789')
        ]);

        // 2) الأجنحة
        $wings = [
            ['Name' => 'الحمد'],
            ['Name' => 'البادية'],
            ['Name' => 'القعيطي'],
        ];
        Wing::insert($wings);

        // 3) المناطق + المواقف
        $regions = [
            ['id' => 1, 'parent_id' => null, 'Name' => 'القطن - السوق'],
            ['id' => 2, 'parent_id' => 1, 'Name' => 'موقف السوق الرئيسي'],
            ['id' => 3, 'parent_id' => null, 'Name' => 'العرض'],
            ['id' => 4, 'parent_id' => 3, 'Name' => 'موقف العرض'],
            ['id' => 5, 'parent_id' => null, 'Name' => 'العقاد'],
            ['id' => 6, 'parent_id' => 5, 'Name' => 'موقف العقاد'],
            ['id' => 7, 'parent_id' => null, 'Name' => 'الكسر'],
            ['id' => 8, 'parent_id' => 7, 'Name' => 'موقف الكسر'],
            ['id' => 9, 'parent_id' => null, 'Name' => 'الخشعة'],
            ['id' => 10, 'parent_id' => 9, 'Name' => 'موقف الخشعة'],
            ['id' => 11, 'parent_id' => null, 'Name' => 'البادية'],
            ['id' => 12, 'parent_id' => 11, 'Name' => 'موقف البادية'],
            ['id' => 13, 'parent_id' => null, 'Name' => 'الخبة'],
            ['id' => 14, 'parent_id' => 13, 'Name' => 'موقف الخبة'],
            ['id' => 15, 'parent_id' => null, 'Name' => 'بور'],
            ['id' => 16, 'parent_id' => 15, 'Name' => 'موقف بور'],
            ['id' => 17, 'parent_id' => null, 'Name' => 'هينن'],
            ['id' => 18, 'parent_id' => 17, 'Name' => 'موقف هينن'],
            ['id' => 19, 'parent_id' => null, 'Name' => 'العقاد الغربي'],
            ['id' => 20, 'parent_id' => 19, 'Name' => 'موقف العقاد الغربي'],
            ['id' => 21, 'parent_id' => null, 'Name' => 'رسب'],
            ['id' => 22, 'parent_id' => 21, 'Name' => 'موقف رسب'],
            ['id' => 23, 'parent_id' => null, 'Name' => 'حذو'],
            ['id' => 24, 'parent_id' => 23, 'Name' => 'موقف حذو'],
            ['id' => 25, 'parent_id' => null, 'Name' => 'ساه'],
            ['id' => 26, 'parent_id' => 25, 'Name' => 'موقف ساه'],
            ['id' => 27, 'parent_id' => null, 'Name' => 'الخشعة الغربية'],
            ['id' => 28, 'parent_id' => 27, 'Name' => 'موقف الخشعة الغربية'],
            ['id' => 29, 'parent_id' => null, 'Name' => 'الغرف'],
            ['id' => 30, 'parent_id' => 29, 'Name' => 'موقف الغرف'],
        ];
        DB::table('regions')->insert($regions);

        // 4) المعلمين
        Teacher::insert([
            ['Name' => 'عبدالله', 'Sex' => 'ذكر'],
            ['Name' => 'علي', 'Sex' => 'ذكر'],
            ['Name' => 'فاطمة', 'Sex' => 'أنثى'],
            ['Name' => 'ساره', 'Sex' => 'أنثى'],
        ]);

        // 5) السائقين (20)
      DB::table('drivers')->insert([
    [
        'id' => 1,
        'Name' => 'أبو محمد',
        'IDNo' => 'D1001',
        'Phone' => '050111111',
        'LicenseNo' => 'L1001',
        'Bus_type' => 'هايس',
        'No_Passengers' => '14',
        'Picture' => null,
        'Ownership' => 'ملك',
        'wing_id' => 1,
        'CheckUp' => true,
        'Behavior' => true,
        'Form' => true,
        'Fitnes' => true,
        'region_id' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'id' => 2,
        'Name' => 'أبو أحمد',
        'IDNo' => 'D1002',
        'Phone' => '050111111',
        'LicenseNo' => 'L1002',
        'Bus_type' => 'كوستر',
        'No_Passengers' => '26',
        'Picture' => null,
        'Ownership' => 'إيجار',
        'wing_id' => 2,
        'CheckUp' => true,
        'Behavior' => false,
        'Form' => true,
        'Fitnes' => true,
        'region_id' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'id' => 3,
        'Name' => 'أبو علي',
        'IDNo' => 'D1003',
        'Phone' => '050111111',
        'LicenseNo' => 'L1003',
        'Bus_type' => 'كوستر',
        'No_Passengers' => '30',
        'Picture' => null,
        'Ownership' => 'إيجار',
        'wing_id' => 3,
        'CheckUp' => true,
        'Behavior' => true,
        'Form' => true,
        'Fitnes' => true,
        'region_id' => 6,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'id'=>4,
        'Name' => 'أبو سالم',
        'IDNo' => 'D1004',
        'Phone' => '050111111',
        'LicenseNo' => 'L1004',
        'Bus_type' => 'هايس',
        'No_Passengers' => '14',
        'Picture' => null,
        'Ownership' => 'ملك',
        'wing_id' => 3,
        'CheckUp' => true,
        'Behavior' => true,
        'Form' => true,
        'Fitnes' => true,
        'region_id' => 8,
        'created_at' => now(),
        'updated_at' => now(),
    ]
   
    
]);


// 6) الطلاب (20)
DB::table('students')->insert([
    ['id' => 1,  'Name' => 'أحمد محمد',    'Grade' => 'الأول',  'Sex' => 'ذكر',  'Phone' => '0500000001', 'Stu_position' => 'موقف السوق الرئيسي',   'wing_id' => 1, 'Division' => 'أ', 'region_id' => 2,  'teacher_id' => 1],
    ['id' => 2,  'Name' => 'سارة علي',     'Grade' => 'الثاني', 'Sex' => 'أنثى', 'Phone' => '0500000002', 'Stu_position' => 'موقف العرض',            'wing_id' => 2, 'Division' => 'ب', 'region_id' => 4,  'teacher_id' => 2],
    ['id' => 3,  'Name' => 'خالد عمر',     'Grade' => 'الثالث', 'Sex' => 'ذكر',  'Phone' => '0500000003', 'Stu_position' => 'موقف العقاد',           'wing_id' => 3, 'Division' => 'ج', 'region_id' => 6,  'teacher_id' => 3],
    ['id' => 4,  'Name' => 'ليلى حسن',    'Grade' => 'الرابع', 'Sex' => 'أنثى', 'Phone' => '0500000004', 'Stu_position' => 'موقف الكسر',            'wing_id' => 1, 'Division' => 'أ', 'region_id' => 8,  'teacher_id' => 4],
    ['id' => 5,  'Name' => 'فهد عبدالله',  'Grade' => 'الخامس', 'Sex' => 'ذكر',  'Phone' => '0500000005', 'Stu_position' => 'موقف الخشعة',           'wing_id' => 2, 'Division' => 'ب', 'region_id' => 10, 'teacher_id' => 1],
    ['id' => 6,  'Name' => 'نور خالد',    'Grade' => 'السادس', 'Sex' => 'أنثى', 'Phone' => '0500000006', 'Stu_position' => 'موقف البادية',          'wing_id' => 3, 'Division' => 'ج', 'region_id' => 12, 'teacher_id' => 2],
    ['id' => 7,  'Name' => 'عبدالله يوسف', 'Grade' => 'الأول', 'Sex' => 'ذكر',  'Phone' => '0500000007', 'Stu_position' => 'موقف الخبة',            'wing_id' => 1, 'Division' => 'أ', 'region_id' => 14, 'teacher_id' => 3],
    ['id' => 8,  'Name' => 'مها إبراهيم', 'Grade' => 'الثاني', 'Sex' => 'أنثى', 'Phone' => '0500000008', 'Stu_position' => 'موقف بور',              'wing_id' => 2, 'Division' => 'ب', 'region_id' => 16, 'teacher_id' => 4],
    ['id' => 9,  'Name' => 'علي فهد',     'Grade' => 'الثالث', 'Sex' => 'ذكر',  'Phone' => '0500000009', 'Stu_position' => 'موقف هينن',             'wing_id' => 3, 'Division' => 'ج', 'region_id' => 18, 'teacher_id' => 1],
    ['id' => 10, 'Name' => 'ريم ماجد',    'Grade' => 'الرابع', 'Sex' => 'أنثى', 'Phone' => '0500000010', 'Stu_position' => 'موقف العقاد الغربي',    'wing_id' => 1, 'Division' => 'أ', 'region_id' => 20, 'teacher_id' => 2],
    ['id' => 11, 'Name' => 'ناصر سامي',   'Grade' => 'الخامس', 'Sex' => 'ذكر',  'Phone' => '0500000011', 'Stu_position' => 'موقف رسب',              'wing_id' => 2, 'Division' => 'ب', 'region_id' => 22, 'teacher_id' => 3],
    ['id' => 12, 'Name' => 'هناء محمد',   'Grade' => 'السادس', 'Sex' => 'أنثى', 'Phone' => '0500000012', 'Stu_position' => 'موقف حذو',              'wing_id' => 3, 'Division' => 'ج', 'region_id' => 24, 'teacher_id' => 4],
    ['id' => 13, 'Name' => 'إبراهيم راشد','Grade' => 'الأول',  'Sex' => 'ذكر',  'Phone' => '0500000013', 'Stu_position' => 'موقف ساه',              'wing_id' => 1, 'Division' => 'أ', 'region_id' => 26, 'teacher_id' => 1],
    ['id' => 14, 'Name' => 'جميلة خالد',  'Grade' => 'الثاني', 'Sex' => 'أنثى', 'Phone' => '0500000014', 'Stu_position' => 'موقف الخشعة الغربية',   'wing_id' => 2, 'Division' => 'ب', 'region_id' => 28, 'teacher_id' => 2],
    ['id' => 15, 'Name' => 'يوسف سعد',    'Grade' => 'الثالث', 'Sex' => 'ذكر',  'Phone' => '0500000015', 'Stu_position' => 'موقف الغرف',            'wing_id' => 3, 'Division' => 'ج', 'region_id' => 30, 'teacher_id' => 3],
    ['id' => 16, 'Name' => 'جمانة وليد',  'Grade' => 'الرابع', 'Sex' => 'أنثى', 'Phone' => '0500000016', 'Stu_position' => 'موقف السوق الرئيسي',   'wing_id' => 1, 'Division' => 'أ', 'region_id' => 2,  'teacher_id' => 4],
    ['id' => 17, 'Name' => 'سعيد منصور',  'Grade' => 'الخامس', 'Sex' => 'ذكر',  'Phone' => '0500000017', 'Stu_position' => 'موقف العرض',            'wing_id' => 2, 'Division' => 'ب', 'region_id' => 4,  'teacher_id' => 1],
    ['id' => 18, 'Name' => 'هالة أحمد',   'Grade' => 'السادس', 'Sex' => 'أنثى', 'Phone' => '0500000018', 'Stu_position' => 'موقف العقاد',           'wing_id' => 3, 'Division' => 'ج', 'region_id' => 6,  'teacher_id' => 2],
    ['id' => 19, 'Name' => 'طارق يوسف',   'Grade' => 'الأول',  'Sex' => 'ذكر',  'Phone' => '0500000019', 'Stu_position' => 'موقف الخبة',            'wing_id' => 1, 'Division' => 'أ', 'region_id' => 14, 'teacher_id' => 3],
    ['id' => 20, 'Name' => 'مريم خالد',   'Grade' => 'الثاني', 'Sex' => 'أنثى', 'Phone' => '0500000020', 'Stu_position' => 'موقف بور',              'wing_id' => 2, 'Division' => 'ب', 'region_id' => 16, 'teacher_id' => 4],
]);

    }
}