// BASE DE DATOS EDITORIAL
const products = [
    {
        id: 1,
        title: "KRONOS ARCHIVE HOODIE 'V-01'",
        category: "hoodies",
        price: 48900,
        image: "img/1.jpg",
        tag: "SERIES A",
        sizes: ["S", "M", "L", "XL"]
    },
    {
        id: 2,
        title: "HEAVY TACTICAL PANTS 'RAW'",
        category: "cargo",
        price: 54000,
        image: "img/2.jpg",
        tag: "SERIES B",
        sizes: ["S", "M", "L", "XL"]
    },
    {
        id: 3,
        title: "BOXIER GRAPHIC TEE '01'",
        category: "tees",
        price: 24500,
        image: "img/3.jpg",
        tag: "LIMITED",
        sizes: ["M", "L", "XL"]
    },
    {
        id: 4,
        title: "ZIP ARCHIVE JACKET",
        category: "hoodies",
        price: 51000,
        image: "img/4.jpg",
        tag: "SERIES A",
        sizes: ["S", "M", "L", "XL"]
    },
    {
        id: 5,
        title: "PARACHUTE VOLUME TROUSERS",
        category: "cargo",
        price: 56000,
        image: "img/5.jpg",
        tag: "SERIES B",
        sizes: ["M", "L", "XL"]
    },
    {
        id: 6,
        title: "LONGSLEEVE STRUCTURAL",
        category: "tees",
        price: 28900,
        image: "img/6.jpg",
        tag: "LIMITED",
        sizes: ["S", "M", "L"]
    },
    {
        id: 7,
        title: "ARCHIVAL UTILITY BAG",
        category: "cargo",
        price: 21000,
        image: "img/7.jpg",
        tag: "ACCESSORY",
        sizes: ["U"]
    },
    {
        id: 8,
        title: "HEAVYWEIGHT WASHED TEE",
        category: "tees",
        price: 23000,
        image: "img/8.jpg",
        tag: "ESSENTIAL",
        sizes: ["S", "M", "L", "XL"]
    }
];

let cart = [];
let selectedSizes = {};

const productGrid = document.getElementById('productGrid');
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
    renderProducts(products);
    setupFilters();
    setupCart();
});

function renderProducts(items) {
    productGrid.innerHTML = '';
    
    if (items.length === 0) {
        productGrid.innerHTML = `<p style="padding: 40px; font-family: 'Space Mono', monospace;">NO ITEMS FOUND IN THIS SELECTION.</p>`;
        return;
    }

    items.forEach(product => {
        if (!selectedSizes[product.id]) {
            selectedSizes[product.id] = product.sizes[0];
        }

        const card = document.createElement('div');
        card.classList.add('brutal-card');
        
        card.innerHTML = `
            <div class="card-img-wrap">
                <span class="brutal-badge">${product.tag}</span>
                <img src="${product.image}" alt="${product.title}">
            </div>
            <div class="card-body">
                <div class="card-meta">
                    <h3 class="card-title">${product.title}</h3>
                    <div class="card-price">$${product.price.toLocaleString()}</div>
                </div>
                <div class="card-actions">
                    <div class="brutal-sizes" id="sizes-${product.id}">
                        ${product.sizes.map(size => `
                            <button class="s-btn ${selectedSizes[product.id] === size ? 'active' : ''}" 
                                    onclick="selectSize(${product.id}, '${size}')">${size}</button>
                        `).join('')}
                    </div>
                    <button class="brutal-add-btn" onclick="addToCart(${product.id})">ADD TO BAG</button>
                </div>
            </div>
        `;
        productGrid.appendChild(card);
    });
}

window.selectSize = function(productId, size) {
    selectedSizes[productId] = size;
    const container = document.getElementById(`sizes-${productId}`);
    container.querySelectorAll('.s-btn').forEach(btn => {
        if (btn.textContent === size) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
};

function setupFilters() {
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            if (filter === 'all') {
                renderProducts(products);
            } else {
                renderProducts(products.filter(p => p.category === filter));
            }
        });
    });
}

window.addToCart = function(productId) {
    const product = products.find(p => p.id === productId);
    const size = selectedSizes[productId];
    const existing = cart.find(item => item.id === productId && item.size === size);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ ...product, size, quantity: 1 });
    }

    updateCartUI();
    openDrawer();
};

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
            <img src="${item.image}" alt="">
            <div class="item-info">
                <h4>${item.title}</h4>
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