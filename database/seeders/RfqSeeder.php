<?php

namespace Database\Seeders;

use App\Models\Rfq;
use App\Models\RfqItem;
use Illuminate\Database\Seeder;

class RfqSeeder extends Seeder
{
    public function run(): void
    {
        $rfqs = [
            [
                'rfq_number'    => 'RFQ-2025-001',
                'agency_id'     => 1,
                'date_received' => '2025-05-15',
                'deadline'      => '2025-05-22',
                'abc'           => 85000,
                'status'        => 'Received',
                'philgeps_ref'  => '9876543',
                'items' => [
                    ['item_description' => 'Amoxicillin 500mg Capsule', 'unit' => 'capsule', 'quantity' => 500,  'unit_price' => 8.50],
                    ['item_description' => 'Paracetamol 500mg Tablet',  'unit' => 'tablet',  'quantity' => 1000, 'unit_price' => 2.75],
                ],
            ],
            [
                'rfq_number'    => 'RFQ-2025-002',
                'agency_id'     => 2,
                'date_received' => '2025-05-10',
                'deadline'      => '2025-05-25',
                'abc'           => 142000,
                'status'        => 'Reviewing',
                'philgeps_ref'  => '9876544',
                'items' => [
                    ['item_description' => 'Metformin 500mg Tablet',  'unit' => 'tablet', 'quantity' => 2000, 'unit_price' => 5.00],
                    ['item_description' => 'Amlodipine 5mg Tablet',   'unit' => 'tablet', 'quantity' => 1000, 'unit_price' => 7.25],
                ],
            ],
            [
                'rfq_number'    => 'RFQ-2025-003',
                'agency_id'     => 3,
                'date_received' => '2025-05-08',
                'deadline'      => '2025-05-20',
                'abc'           => 38500,
                'status'        => 'Quoted',
                'philgeps_ref'  => null,
                'items' => [
                    ['item_description' => 'ORS Sachet',           'unit' => 'sachet', 'quantity' => 5000, 'unit_price' => 4.50],
                    ['item_description' => 'Cetirizine 10mg Tablet', 'unit' => 'tablet', 'quantity' => 500,  'unit_price' => 6.00],
                ],
            ],
            [
                'rfq_number'    => 'RFQ-2025-004',
                'agency_id'     => 4,
                'date_received' => '2025-05-01',
                'deadline'      => '2025-05-28',
                'abc'           => 210000,
                'status'        => 'Awarded',
                'philgeps_ref'  => '9876545',
                'items' => [
                    ['item_description' => 'Cefuroxime 500mg Tablet', 'unit' => 'tablet', 'quantity' => 1000, 'unit_price' => 45.00],
                    ['item_description' => 'Omeprazole 20mg Capsule', 'unit' => 'capsule', 'quantity' => 2000, 'unit_price' => 12.00],
                ],
            ],
            [
                'rfq_number'    => 'RFQ-2025-005',
                'agency_id'     => 5,
                'date_received' => '2025-04-28',
                'deadline'      => '2025-05-18',
                'abc'           => 55000,
                'status'        => 'Lost',
                'philgeps_ref'  => null,
                'items' => [
                    ['item_description' => 'Vitamin C 500mg Tablet',  'unit' => 'tablet', 'quantity' => 3000, 'unit_price' => 3.50],
                    ['item_description' => 'Iron Supplement 325mg',   'unit' => 'tablet', 'quantity' => 1000, 'unit_price' => 8.00],
                ],
            ],
        ];

        foreach ($rfqs as $data) {
            $items = $data['items'];
            unset($data['items']);

            $rfq = Rfq::create($data);

            foreach ($items as $item) {
                $item['total_price'] = $item['unit_price'] * $item['quantity'];
                $rfq->items()->create($item);
            }
        }
    }
}