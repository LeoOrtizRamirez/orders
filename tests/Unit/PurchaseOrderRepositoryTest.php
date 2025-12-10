<?php

namespace Tests\Unit;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Supplier;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $purchaseOrderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purchaseOrderRepository = new PurchaseOrderRepository(new PurchaseOrder());
    }

    /** @test */
    public function it_can_get_kanban_orders_with_correct_structure()
    {
        // Create a user and supplier
        $user = User::factory()->create(['name' => 'John Doe']);
        $supplier = Supplier::factory()->create(['name' => 'Acme Inc.']);

        // Create some purchase orders
        $order1 = PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $supplier->id,
            'created_by' => $user->id,
            'notes' => json_encode([['note' => 'Test note for order 1']]),
            'total' => 150.75,
        ]);
        $order2 = PurchaseOrder::factory()->create([
            'status' => 'approved',
            'supplier_id' => $supplier->id,
            'created_by' => $user->id,
            'notes' => null, // No notes for this order
            'total' => 200.00,
        ]);

        $kanbanOrders = $this->purchaseOrderRepository->getKanbanOrders();

        // Assert that the collection is not empty
        $this->assertCount(2, $kanbanOrders);

        // Assert structure and values for the first order
        $this->assertEquals($order1->id, $kanbanOrders[0]['id']);
        $this->assertEquals($order1->order_number, $kanbanOrders[0]['order_number']);
        $this->assertEquals('John Doe', $kanbanOrders[0]['customer_name']);
        $this->assertEquals('Acme Inc.', $kanbanOrders[0]['supplier_name']);
        $this->assertEquals('pending', $kanbanOrders[0]['status']);
        $this->assertEquals('Test note for order 1', $kanbanOrders[0]['description']);
        $this->assertEquals(150.75, $kanbanOrders[0]['total']);
        $this->assertNotNull($kanbanOrders[0]['created_at']);

        // Assert structure and values for the second order
        $this->assertEquals($order2->id, $kanbanOrders[1]['id']);
        $this->assertEquals($order2->order_number, $kanbanOrders[1]['order_number']);
        $this->assertEquals('John Doe', $kanbanOrders[1]['customer_name']);
        $this->assertEquals('Acme Inc.', $kanbanOrders[1]['supplier_name']);
        $this->assertEquals('approved', $kanbanOrders[1]['status']);
        $this->assertEquals('', $kanbanOrders[1]['description']); // Should be empty if no notes
        $this->assertEquals(200.00, $kanbanOrders[1]['total']);
        $this->assertNotNull($kanbanOrders[1]['created_at']);
    }

    /** @test */
    public function it_returns_empty_collection_if_no_orders()
    {
        $kanbanOrders = $this->purchaseOrderRepository->getKanbanOrders();
        $this->assertEmpty($kanbanOrders);
    }
}
