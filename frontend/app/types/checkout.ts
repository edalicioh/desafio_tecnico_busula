import type { Cart } from "./cart";

export interface BillingAddress {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  zipCode: string;
  country: string;
}

export interface PaymentMethod {
  id: number;
  name: string;
}

export interface PaymentCondition {
  id: number;
  name: string;
  installments: string;
  discount: number;
  payment_method_id:number
}

export interface Order {
  id: number | null;
  cartId: number;
  paymentMethodId: number;
  total: number;
  status: 'PENDING' | 'PAID' | 'CANCELED';
  createdAt?: string;
  updatedAt?: string;
}

export interface CheckoutState {
  billingAddress: BillingAddress | null;
  paymentMethod: PaymentMethod | null;
  paymentCondition: PaymentCondition | null;
  order: Order | null;
  loading: boolean;
  error: string | null;
}