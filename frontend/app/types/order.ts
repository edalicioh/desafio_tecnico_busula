
export interface Order {
  id: number | null;
  cartId: number;
  paymentMethodId: number;
  total: number;
  status: 'PENDING' | 'PAID' | 'CANCELED';
}