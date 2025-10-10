<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Asesoría Financiera',
                'slug' => 'asesoria-financiera',
                'description' => 'Servicios de asesoramiento financiero personalizado',
                'price' => 99,
                'duration_minutes' => 60,
                'order' => 1,
                'icon' => '💰',
                'color' => '#10B981'
            ],
            [
                'name' => 'Asesoría Psicológica',
                'slug' => 'asesoria-psicologica',
                'description' => 'Apoyo psicológico profesional',
                'price' => 79,
                'duration_minutes' => 50,
                'order' => 2,
                'icon' => '🧠',
                'color' => '#3B82F6'
            ],
            [
                'name' => 'Asesoría Legal',
                'slug' => 'asesoria-legal',
                'description' => 'Asesoramiento legal especializado',
                'price' => 129,
                'duration_minutes' => 45,
                'order' => 3,
                'icon' => '⚖️',
                'color' => '#EF4444'
            ],
            [
                'name' => 'Nutrición',
                'slug' => 'nutricion',
                'description' => 'Planes de alimentación y nutrición',
                'price' => 69,
                'duration_minutes' => 40,
                'order' => 4,
                'icon' => '🥗',
                'color' => '#8B5CF6'
            ],
            [
                'name' => 'Bienestar',
                'slug' => 'bienestar',
                'description' => 'Servicios integrales de bienestar',
                'price' => 89,
                'duration_minutes' => 55,
                'order' => 5,
                'icon' => '🌿',
                'color' => '#F59E0B'
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}