// Los productos ya NO están escritos acá: los genera PHP desde la base de datos
// y este script los lee de los atributos data-* de cada carta (.brutal-card).

let cart = [];
let products = [];

const productGrid = document.getElementById('productGrid');
const noItems = document.getElementById('noItems');
const filterBtns = document.querySelectorAll('.f-btn');
const cartToggle = document.getElementById('cartToggle');
const closeCart = document.getElementById('closeCart');
const cartDrawer = document.getElementById('cartDrawer');
const cartOverlay = document.getElementById('cartOverlay');
const cartItemsContainer = document.getElementById('cartItems');
const cartCount = document.getElementById('cartCount');
const cartTotalPrice = document.getElementById('cartTotalPrice');
const checkoutBtn = document.getElementById('checkoutBtn');

document.addEventListener('DOMContentLoaded', () => {
    loadProductsFromDOM();
    setupGrid();
    setupFilters();
    setupCart();
});

// Arma la lista de productos leyendo las cartas que dibujó PHP
function loadProductsFromDOM() {
    products = Array.from(document.querySelectorAll('.brutal-card')).map(card => ({
        id: Number(card.dataset.id),
        title: card.dataset.title,
        category: card.dataset.category,
        price: Number(card.dataset.price),
        image: card.dataset.image
    }));
}

// Talles y botón "ADD TO BAG" (un solo listener para toda la grilla)
function setupGrid() {
    productGrid.addEventListener('click', (e) => {
        const sizeBtn = e.target.closest('.s-btn');
        if (sizeBtn) {
            sizeBtn.parentElement.querySelectorAll('.s-btn').forEach(b => b.classList.remove('active'));
            sizeBtn.classList.add('active');
            return;
        }

        const addBtn = e.target.closest('.brutal-add-btn');
        if (addBtn && !addBtn.disabled) {
            const card = addBtn.closest('.brutal-card');
            const activeSize = card.querySelector('.s-btn.active');
            addToCart(Number(card.dataset.id), activeSize ? activeSize.textContent : '');
        }
    });
}

// Los filtros ahora muestran/ocultan las cartas existentes
function setupFilters() {
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');
            let visible = 0;

            document.querySelectorAll('.brutal-card').forEach(card => {
                const show = filter === 'all' || card.dataset.category === filter;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noItems) {
                noItems.style.display = visible === 0 ? 'block' : 'none';
            }
        });
    });
}

// Los nombres vienen de la base de datos: se escapan antes de meterlos en el HTML del carrito
function escapeHTML(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/"/g, '&quot;');
}

function addToCart(productId, size) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existing = cart.find(item => item.id === productId && item.size === size);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ ...product, size, quantity: 1 });
    }

    updateCartUI();
    openDrawer();
}

window.removeFromCart = function(index) {
    cart.splice(index, 1);
    updateCartUI();
};

function updateCartUI() {
    const totalItems = cart.reduce((sum, i) => sum + i.quantity, 0);
    cartCount.textContent = totalItems;

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `<p class="empty-text">BAG IS EMPTY.</p>`;
        cartTotalPrice.textContent = "$0";
        return;
    }

    cartItemsContainer.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        let sub = item.price * item.quantity;
        total += sub;
        
        const el = document.createElement('div');
        el.classList.add('drawer-item');
        el.innerHTML = `
            <img src="${escapeHTML(item.image)}" alt="">
            <div class="item-info">
                <h4>${escapeHTML(item.title)}</h4>
                <p>SIZE: ${item.size} // QTY: ${item.quantity}</p>
                <span class="item-price">$${sub.toLocaleString()}</span>
            </div>
            <button class="remove-btn" onclick="removeFromCart(${index})"><i class="fa-solid fa-xmark"></i></button>
        `;
        cartItemsContainer.appendChild(el);
    });

    cartTotalPrice.textContent = `$${total.toLocaleString()}`;
}

function openDrawer() {
    cartDrawer.classList.add('open');
    cartOverlay.classList.add('open');
}

function closeDrawer() {
    cartDrawer.classList.remove('open');
    cartOverlay.classList.remove('open');
}

function setupCart() {
    cartToggle.addEventListener('click', openDrawer);
    closeCart.addEventListener('click', closeDrawer);
    cartOverlay.addEventListener('click', closeDrawer);

    checkoutBtn.addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Your bag is empty.');
            return;
        }

        let msg = "KRONOS ARCHIVE // ORDER REQUEST:\n\n";
        let total = 0;

        cart.forEach(item => {
            let sub = item.price * item.quantity;
            total += sub;
            msg += `- ${item.title} [Size: ${item.size}] x${item.quantity} = $${sub.toLocaleString()}\n`;
        });

        msg += `\nTOTAL: $${total.toLocaleString()}`;
        window.open(`https://wa.me/5491100000000?text=${encodeURIComponent(msg)}`, '_blank');
    });
}