# Store Documentation

## Overview
This directory contains the Pinia stores used in the application for state management.

## Stores

### Cart Store (`cartStore.ts`)
Manages the shopping cart state, including:
- Cart items
- Cart totals
- Cart operations (add/remove items, update quantities)

### Checkout Store (`checkoutStore.ts`)
Manages the checkout process state, including:
- Billing address information
- Payment method selection
- Order creation
- Validation and error handling

## Usage

### Importing Stores
```typescript
import { useCartStore } from '~/store/cartStore'
import { useCheckoutStore } from '~/store/checkoutStore'
```

### Using Stores in Components
```typescript
<script setup lang="ts">
import { useCartStore } from '~/store/cartStore'
import { useCheckoutStore } from '~/store/checkoutStore'

const cartStore = useCartStore()
const checkoutStore = useCheckoutStore()

// Access state
const cartItems = cartStore.cartItems
const billingAddress = checkoutStore.billingAddress

// Call actions
cartStore.addItemToCart(product)
checkoutStore.setBillingAddress(address)
</script>
```

## Types
Type definitions for the stores are located in the `~/types` directory:
- `cart.ts` - Cart and cart item types
- `checkout.ts` - Checkout-related types (billing address, payment methods, etc.)
- `product.ts` - Product types
- `category.ts` - Category types

## Best Practices
1. Always use the store composition functions (`useCartStore`, `useCheckoutStore`) to access store state
2. Prefer actions over direct state manipulation
3. Handle loading and error states appropriately
4. Use getters for computed properties
5. Reset store state when appropriate (e.g., after completing checkout)