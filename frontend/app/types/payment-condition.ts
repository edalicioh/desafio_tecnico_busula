export interface PaymentCondition {
  id: number;
  name: string;
  installments: string;
  discount: number;
  payment_method_id:number
}
