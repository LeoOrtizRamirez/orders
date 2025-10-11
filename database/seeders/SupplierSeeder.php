<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Tecnología Avanzada S.A.',
                'contact_person' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@tecnologia-avanzada.com',
                'phone' => '+57 1 2345678',
                'address' => 'Calle 123 #45-67',
                'city' => 'Bogotá',
                'country' => 'Colombia',
                'tax_id' => '900123456-1',
                'payment_terms' => '30 días',
                'notes' => 'Proveedor oficial de equipos tecnológicos',
                'is_active' => true,
            ],
            [
                'name' => 'Suministros Industriales Ltda.',
                'contact_person' => 'María González',
                'email' => 'maria.gonzalez@suministros.com',
                'phone' => '+57 1 8765432',
                'address' => 'Avenida Principal #89-10',
                'city' => 'Medellín',
                'country' => 'Colombia',
                'tax_id' => '900987654-2',
                'payment_terms' => '15 días',
                'notes' => 'Especialistas en suministros industriales',
                'is_active' => true,
            ],
            [
                'name' => 'Distribuidora Global S.A.S.',
                'contact_person' => 'Andrés Pérez',
                'email' => 'andres.perez@distribuidora-global.com',
                'phone' => '+57 1 5551234',
                'address' => 'Carrera 50 #23-45',
                'city' => 'Cali',
                'country' => 'Colombia',
                'tax_id' => '900555666-3',
                'payment_terms' => '45 días',
                'notes' => 'Distribuidor mayorista de productos generales',
                'is_active' => true,
            ],
            [
                'name' => 'Electro Componentes S.A.',
                'contact_person' => 'Laura Martínez',
                'email' => 'laura.martinez@electro-componentes.com',
                'phone' => '+57 1 4445678',
                'address' => 'Diagonal 70 #12-34',
                'city' => 'Barranquilla',
                'country' => 'Colombia',
                'tax_id' => '900777888-4',
                'payment_terms' => '30 días',
                'notes' => 'Componentes electrónicos y eléctricos',
                'is_active' => true,
            ],
            [
                'name' => 'Materiales de Construcción Hermanos López',
                'contact_person' => 'José López',
                'email' => 'jose.lopez@materialeslopez.com',
                'phone' => '+57 1 3339876',
                'address' => 'Transversal 25 #67-89',
                'city' => 'Cartagena',
                'country' => 'Colombia',
                'tax_id' => '900222333-5',
                'payment_terms' => '60 días',
                'notes' => 'Materiales de construcción y ferretería',
                'is_active' => true,
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}