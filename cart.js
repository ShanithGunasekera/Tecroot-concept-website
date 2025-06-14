// cart.js - reusable cart functions

export async function getCart() {
    try {
        const response = await fetch('api_cart.php');
        return await response.json();
    } catch (error) {
        console.error('Error fetching cart:', error);
        return { cart: [], totalItems: 0, totalPrice: 0 };
    }
}

export async function addToCart(product) {
    try {
        const response = await fetch('api_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(product)
        });
        return await response.json();
    } catch (error) {
        console.error('Error adding to cart:', error);
        return { error: 'Failed to add to cart' };
    }
}

export async function updateCartItem(productId, quantity) {
    try {
        const response = await fetch('api_cart.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity })
        });
        return await response.json();
    } catch (error) {
        console.error('Error updating cart:', error);
        return { error: 'Failed to update cart' };
    }
}

export async function removeCartItem(productId) {
    try {
        const response = await fetch('api_cart.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        });
        return await response.json();
    } catch (error) {
        console.error('Error removing from cart:', error);
        return { error: 'Failed to remove from cart' };
    }
}

// Update cart count in navbar
export function updateCartUI(totalItems) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => {
        el.textContent = totalItems;
    });
}