
import type { Product } from "./product";

export interface CartItem {
  id: number;
  quantity: number;
  subtotal: number |  null;
}