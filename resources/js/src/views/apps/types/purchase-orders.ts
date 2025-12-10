export interface Note {
    user_id: number;
    user_name: string;
    note: string;
    timestamp: string;
}

export interface PurchaseOrder {
    id: number;
    order_number: string;
    supplier_id: number;
    supplier: Supplier;
    order_date: string;
    expected_delivery_date: string | null;
    delivery_date: string | null;
    status: 'draft' | 'pending' | 'approved' | 'rejected' | 'ordered' | 'received' | 'cancelled';
    notes: Note[] | null;
    rejection_reason: string | null;
    created_by: number;
    creator: User;
    approved_by: number | null;
    approver: User | null;
    approved_at: string | null;
    items: PurchaseOrderItem[];
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
    notes: Note[] | null;
}

export interface PurchaseOrderFilters {
    status: string;
    supplier_id: string;
}

export interface PurchaseOrderParams {
    id: number | null;
    supplier_id: number | null;
    expected_delivery_date: string | null;
    new_note: string; // Representa la nueva nota a añadir
    items: PurchaseOrderItemParams[];
}

export interface PurchaseOrderItemParams {
    id?: number;
    product_id: number | null;
    quantity: number;
    new_note: string; // Representa la nueva nota a añadir para el ítem
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