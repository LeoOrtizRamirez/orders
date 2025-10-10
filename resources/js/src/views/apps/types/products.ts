// types/products.ts
export interface Product {
    id: number;
    name: string;
    sku: string;
    description: string;
    price: number;
    cost: number;
    stock: number;
    min_stock: number;
    reorder_point: number;
    unit: string;
    category: string;
    brand: string;
    supplier: string;
    is_active: boolean;
    order: number;
    purchase_order_items_count: number;
    created_at: string;
    updated_at: string;
}

export interface ProductFilters {
    category: string;
    stock_status: string;
}

export interface ProductParams {
    id: number | null;
    name: string;
    sku: string;
    description: string;
    price: number;
    cost: number;
    stock: number;
    min_stock: number;
    reorder_point: number;
    unit: string;
    category: string;
    brand: string;
    supplier: string;
    is_active: boolean;
    order: number;
}