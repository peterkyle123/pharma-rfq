<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencies = [
            [
                'name'           => 'Quezon City General Hospital',
                'type'           => 'Government Hospital',
                'region'         => 'NCR',
                'contact_person' => 'Maria Santos',
                'contact_email'  => 'procurement@qcgh.gov.ph',
                'contact_phone'  => '02-8123-4567',
            ],
            [
                'name'           => 'DOH-NCRO',
                'type'           => 'National Agency',
                'region'         => 'NCR',
                'contact_person' => 'Juan Dela Cruz',
                'contact_email'  => 'procurement@doh.gov.ph',
                'contact_phone'  => '02-8651-7800',
            ],
            [
                'name'           => 'Marikina City Health Office',
                'type'           => 'LGU',
                'region'         => 'NCR',
                'contact_person' => 'Ana Reyes',
                'contact_email'  => 'cho@marikina.gov.ph',
                'contact_phone'  => '02-8646-2045',
            ],
            [
                'name'           => 'Philippine General Hospital',
                'type'           => 'Government Hospital',
                'region'         => 'NCR',
                'contact_person' => 'Pedro Lim',
                'contact_email'  => 'bac@pgh.gov.ph',
                'contact_phone'  => '02-8554-8400',
            ],
            [
                'name'           => 'Pasig City RHU',
                'type'           => 'LGU',
                'region'         => 'NCR',
                'contact_person' => 'Rosa Garcia',
                'contact_email'  => 'rhu@pasigcity.gov.ph',
                'contact_phone'  => '02-8641-9999',
            ],
        ];

        foreach ($agencies as $agency) {
            Agency::create($agency);
        }
    }
}