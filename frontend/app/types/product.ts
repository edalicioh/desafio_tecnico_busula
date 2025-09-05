import type { Category } from "./category";

export interface Product {
  id: number;
  name: string;
  category: Category;
  price: string;
  image: string;
  rating?: number;
}