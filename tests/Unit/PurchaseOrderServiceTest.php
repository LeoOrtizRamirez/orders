<?php

namespace Tests\Unit;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Supplier;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\ProductRepository;
use App\Services\PurchaseOrderManagementService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PurchaseOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $purchaseOrderRepository;
    protected $productRepository;
    protected $purchaseOrderManagementService;
    protected $user;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchaseOrderRepository = Mockery::mock(PurchaseOrderRepository::class);
        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->purchaseOrderManagementService = new PurchaseOrderManagementService(
            $this->purchaseOrderRepository,
            $this->productRepository
        );

        $this->user = User::factory()->create(['name' => 'Test Customer']);
        $this->supplier = Supplier::factory()->create(['name' => 'Test Supplier']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_kanban_purchase_orders()
    {
        $order1 = PurchaseOrder::factory()->make([
            'id' => 1,
            'order_number' => 'PO-001',
            'status' => 'pending',
            'notes' => json_encode([['note' => 'First order note']]),
            'total' => 100.00,
            'created_at' => now()->subDays(5),
            'supplier_id' => $this->supplier->id,
            'created_by' => $this->user->id,
        ]);
        $order1->setRelation('creator', $this->user);
        $order1->setRelation('supplier', $this->supplier);

        $order2 = PurchaseOrder::factory()->make([
            'id' => 2,
            'order_number' => 'PO-002',
            'status' => 'approved',
            'notes' => json_encode([['note' => 'Second order note']]),
            'total' => 200.00,
            'created_at' => now()->subDays(2),
            'supplier_id' => $this->supplier->id,
            'created_by' => $this->user->id,
        ]);
        $order2->setRelation('creator', $this->user);
        $order2->setRelation('supplier', $this->supplier);

        $mockedOrders = collect([$order1, $order2]);

        $this->purchaseOrderRepository->shouldReceive('getKanbanOrders')
                                      ->once()
                                      ->andReturn($mockedOrders->map(function ($order) {
                                          return [
                                              'id' => $order->id,
                                              'order_number' => $order->order_number,
                                              'customer_name' => $order->creator->name,
                                              'supplier_name' => $order->supplier->name,
                                              'status' => $order->status,
                                              'description' => $order->notes ? json_decode($order->notes, true)[0]['note'] : '',
                                              'total' => $order->total,
                                              'created_at' => $order->created_at->toDateTimeString(),
                                          ];
                                      }));

        $orders = $this->purchaseOrderManagementService->getKanbanPurchaseOrders();

        $this->assertIsIterable($orders);
        $this->assertCount(2, $orders);
        $this->assertEquals('PO-001', $orders[0]['order_number']);
        $this->assertEquals('Test Customer', $orders[0]['customer_name']);
        $this->assertEquals('pending', $orders[0]['status']);
        $this->assertEquals(100.00, $orders[0]['total']);
    }
}
