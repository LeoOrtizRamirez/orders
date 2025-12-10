<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class PurchaseOrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions if they don't exist
        Permission::findOrCreate('view purchase_orders', 'web');
        Permission::findOrCreate('edit purchase_orders', 'web');
        Permission::findOrCreate('create purchase_orders', 'web');

        // Create a user with 'view purchase_orders' and 'edit purchase_orders' permission
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['view purchase_orders', 'edit purchase_orders']);

        // Create an admin user with all permissions
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin'); // Assuming 'admin' role has all permissions
    }

    /** @test */
    public function it_can_fetch_all_purchase_orders_for_kanban_board()
    {
        $this->withoutExceptionHandling();

        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        // Create some purchase orders with different statuses
        PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);
        PurchaseOrder::factory()->create([
            'status' => 'approved',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);
        PurchaseOrder::factory()->create([
            'status' => 'ordered',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/purchase-orders/kanban');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'order_number',
                             'customer_name',
                             'supplier_name',
                             'status',
                             'description',
                             'total',
                             'created_at',
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_purchase_order_status_via_kanban()
    {
        $this->withoutExceptionHandling();

        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        // Test transition from 'pending' to 'approved'
        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'approved'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Orden aprobada correctamente.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'approved',
        ]);

        // Test transition from 'approved' to 'ordered'
        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'ordered'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Orden marcada como enviada al proveedor.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'ordered',
        ]);
    }

    /** @test */
    public function it_cannot_update_purchase_order_status_to_rejected_directly()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'rejected'
        ]);

        $response->assertStatus(422) // Unprocessable Entity due to validation exception
                 ->assertJson([
                     'success' => false,
                     'message' => 'Para rechazar una orden, use la acción específica que requiere una razón de rechazo.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'pending', // Status should not have changed
        ]);
    }

    /** @test */
    public function it_cannot_update_purchase_order_status_to_received_directly()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'ordered',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'received'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Para marcar una orden como "recibida", use la acción específica que requiere detalles de los ítems.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'ordered',
        ]);
    }

    /** @test */
    public function it_can_transition_from_rejected_to_pending()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'rejected',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'pending'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Orden reabierta y puesta en estado pendiente correctamente.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_can_transition_from_pending_to_draft()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'draft'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Orden actualizada a borrador correctamente.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function it_can_transition_from_draft_to_pending()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create([
            'status' => 'draft',
            'supplier_id' => $supplier->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/purchase-orders/{$order->id}/status", [
            'status' => 'pending'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Orden enviada a aprobación correctamente.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }
}
