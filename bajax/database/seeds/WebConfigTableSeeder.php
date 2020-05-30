<?php

use Illuminate\Database\Seeder;
use App\WebConfig;
class WebConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		WebConfig::create(['name' => 'update_point','value' => 0]);
		WebConfig::create(['name' => 'jumlah_uang','value' => 0]);
    }
}
