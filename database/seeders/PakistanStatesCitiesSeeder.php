<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class PakistanStatesCitiesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Punjab', 'slug' => 'punjab', 'sort' => 1, 'cities' => [
                'Lahore', 'Faisalabad', 'Rawalpindi', 'Multan', 'Gujranwala', 'Sialkot', 'Bahawalpur', 'Sargodha',
                'Sheikhupura', 'Rahim Yar Khan', 'Jhang', 'Dera Ghazi Khan', 'Gujrat', 'Sahiwal', 'Wah Cantonment',
                'Kasur', 'Okara', 'Mandi Bahauddin', 'Chiniot', 'Khanewal', 'Hafizabad', 'Muzaffargarh', 'Khanpur',
                'Gojra', 'Bahawalnagar', 'Muridke', 'Pakpattan', 'Jhelum', 'Chishtian', 'Attock', 'Mianwali',
                'Kamoke', 'Vihari', 'Kamalia', 'Ahmedpur East', 'Kot Addu', 'Wazirabad', 'Layyah', 'Taxila',
                'Khushab', 'Mian Channu', 'Burewala', 'Chakwal', 'Toba Tek Singh', 'Jaranwala', 'Haroonabad',
                'Narowal', 'Bhalwal', 'Hasilpur', 'Mailsi', 'Daska', 'Pattoki', 'Renala Khurd', 'Nankana Sahib',
            ]],
            ['name' => 'Sindh', 'slug' => 'sindh', 'sort' => 2, 'cities' => [
                'Karachi', 'Hyderabad', 'Sukkur', 'Larkana', 'Nawabshah', 'Mirpurkhas', 'Jacobabad', 'Shikarpur',
                'Khairpur', 'Dadu', 'Tando Allahyar', 'Tando Adam', 'Badin', 'Sanghar', 'Thatta', 'Naushahro Feroze',
                'Umerkot', 'Ghotki', 'Matiari', 'Jamshoro', 'Kamber', 'Kashmore', 'Tharparkar', 'Sujawal',
            ]],
            ['name' => 'Khyber Pakhtunkhwa', 'slug' => 'khyber-pakhtunkhwa', 'sort' => 3, 'cities' => [
                'Peshawar', 'Mardan', 'Mingora', 'Kohat', 'Abbottabad', 'Bannu', 'Dera Ismail Khan', 'Swabi',
                'Nowshera', 'Charsadda', 'Mansehra', 'Swat', 'Haripur', 'Malakand', 'Karak', 'Hangu',
                'Tank', 'Lakki Marwat', 'Dera Ismail Khan', 'Battagram', 'Upper Dir', 'Lower Dir', 'Buner',
                'Shangla', 'Kohistan', 'Torghar', 'Chitral',
            ]],
            ['name' => 'Balochistan', 'slug' => 'balochistan', 'sort' => 4, 'cities' => [
                'Quetta', 'Turbat', 'Khuzdar', 'Chaman', 'Hub', 'Sibi', 'Loralai', 'Zhob', 'Gwadar', 'Dera Bugti',
                'Dera Murad Jamali', 'Usta Muhammad', 'Surab', 'Mastung', 'Nushki', 'Kalat', 'Panjgur', 'Kech',
                'Kharan', 'Washuk', 'Awaran', 'Barkhan', 'Musakhel', 'Sherani', 'Kohlu', 'Duki', 'Ziarat', 'Lehri',
            ]],
            ['name' => 'Islamabad', 'slug' => 'islamabad', 'sort' => 5, 'cities' => [
                'Islamabad', 'Rawalpindi',
            ]],
            ['name' => 'Gilgit-Baltistan', 'slug' => 'gilgit-baltistan', 'sort' => 6, 'cities' => [
                'Gilgit', 'Skardu', 'Chilas', 'Astore', 'Hunza', 'Nagar', 'Ghanche', 'Shigar', 'Kharmang', 'Diamer',
            ]],
            ['name' => 'Azad Jammu and Kashmir', 'slug' => 'ajk', 'sort' => 7, 'cities' => [
                'Muzaffarabad', 'Mirpur', 'Rawalakot', 'Kotli', 'Bhimber', 'Bagh', 'Neelum', 'Hattian', 'Haveli', 'Sudhnati',
            ]],
        ];

        foreach ($data as $item) {
            $state = State::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'sort_order' => $item['sort'],
            ]);
            foreach ($item['cities'] as $i => $cityName) {
                City::create([
                    'state_id' => $state->id,
                    'name' => $cityName,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
