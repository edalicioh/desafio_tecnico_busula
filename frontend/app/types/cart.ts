import type { CartItem } from "./cart-item";

export interface Cart {
  id: number | null;
  userId: number | string | null;
  sessionId: string | null;
  status: 'OPEN' | 'CHECKOUT' | 'CANCELED' ;
  total: number | null
  items: CartItem[] | [];
}