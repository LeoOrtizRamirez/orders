export interface Supplier {
    id: number;
    name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;
    country: string | null;
    tax_id: string | null;
    payment_terms: string | null;
    notes: string | null;
    is_active: boolean;
    purchase_orders_count: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}