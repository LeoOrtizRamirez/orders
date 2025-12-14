export interface UserNote {
    id: number;
    note: string;
    is_important: boolean;
    created_at: string;
    author: {
        id: number;
        name: string;
    };
    notable_id?: number;
    notable_type?: string;
}

export interface PurchaseOrder {
    id: number;
    order_number: string;
    supplier_id: number;
    supplier: Supplier;
    order_date: string;
    expected_delivery_date: string | null;
    delivery_date: string | null;
    status: 'draft' | 'pending' | 'approved' | 'rejected' | 'ordered' | 'received' | 'cancelled' | 'nuevo pedido' | 'disponibilidad' | 'preparar pedido' | 'en preparación' | 'facturación' | 'en despacho' | 'en ruta' | 'entregado';
    notes: UserNote[] | null;
    rejection_reason: string | null;
    created_by: number;
    creator: User;
    approved_by: number | null;
    approver: User | null;
    approved_at: string | null;
    items: PurchaseOrderItem[];
    parent_id: number | null;
    parent?: PurchaseOrder;
    sub_orders?: PurchaseOrder[];
    can_be_edited: boolean;
    can_be_approved: boolean;
    can_be_received: boolean;
    created_at: string;
    updated_at: string;
}

export interface PurchaseOrderItem {
    id: number;
    purchase_order_id: number;
    product_id: number;
    product: Product;
    quantity: number;
    received_quantity: number;
    item_notes: UserNote[] | null; // Changed to match API snake_case
}

export interface PurchaseOrderFilters {
    status: string;
    supplier_id: string;
}

export interface PurchaseOrderParams {
    id: number | null;
    supplier_id: number | null;
    expected_delivery_date: string | null;
    items: PurchaseOrderItemParams[];
}

export interface PurchaseOrderItemParams {
    id?: number;
    product_id: number | null;
    quantity: number;
    itemNotes?: UserNote[]; // Add itemNotes to params
}

export interface Supplier {
    id: number;
    name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
}

export interface User {
    id: number;
    name: string;
    email: string;
}