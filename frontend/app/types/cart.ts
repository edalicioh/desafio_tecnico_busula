import type { CartItem } from "./cart-item";

export interface Cart {
  id: number | null;
  userId: number | string | null;
  status: 'OPEN' | 'CHECKOUT' | 'CANCELED' ;
  totol: number | null 
  items: CartItem[] | [];
}