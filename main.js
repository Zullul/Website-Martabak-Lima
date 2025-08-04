// Martabak Lima Website JavaScript

// Global Variables
let cart = [];
let currentOrder = {
    base: null,
    toppings: [],
    total: 0
};

// Menu Data
const menuData = [
    {
        id: 1,
        name: "Martabak Sapi Mozarella Telur Bebek",
        category: "asin",
        price: 31500,
        image: "Martabak_Mozarella.jpg",
        description: "Signature dish dengan daging sapi, keju mozarella, dan telur bebek",
        isSignature: true,
        ingredients: ["Daging Sapi", "Keju Mozarella", "Telur Bebek"]
    },
    {
        id: 2,
        name: "Martabak Ikan Tuna Asap",
        category: "asin",
        price: 25000,
        image: "https://via.placeholder.com/300x200/F8981D/FFFFFF?text=Tuna+Asap",
        description: "Martabak dengan ikan tuna asap yang kaya gizi",
        isSignature: false,
        ingredients: ["Ikan Tuna Asap", "Telur"]
    },
    {
        id: 3,
        name: "Martabak Ayam Klasik",
        category: "asin",
        price: 21000,
        image: "https://via.placeholder.com/300x200/FF6B35/FFFFFF?text=Ayam+Klasik",
        description: "Martabak asin klasik dengan ayam shredded",
        isSignature: false,
        ingredients: ["Ayam Shredded", "Telur", "Daun Bawang"]
    },
    {
        id: 4,
        name: "Martabak Coklat Keju",
        category: "manis",
        price: 17000,
        image: "https://via.placeholder.com/300x200/8B4513/FFFFFF?text=Coklat+Keju",
        description: "Perpaduan sempurna coklat dan keju dalam martabak manis",
        isSignature: false,
        ingredients: ["Coklat", "Keju", "Susu Kental Manis"]
    },
    {
        id: 5,
        name: "Martabak Pandan Susu",
        category: "manis",
        price: 15000,
        image: "https://via.placeholder.com/300x200/90EE90/FFFFFF?text=Pandan+Susu",
        description: "Martabak manis dengan aroma pandan dan susu",
        isSignature: false,
        ingredients: ["Pandan", "Susu Kental Manis"]
    },
    {
        id: 6,
        name: "Martabak Kacang Original",
        category: "manis",
        price: 13500,
        image: "https://via.placeholder.com/300x200/DEB887/FFFFFF?text=Kacang+Original",
        description: "Martabak manis tradisional dengan kacang tanah",
        isSignature: false,
        ingredients: ["Kacang Tanah", "Gula"]
    }
];

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Load menu items
    loadMenuItems();
    
    // Initialize event listeners
    initializeEventListeners();
    
    // Initialize smooth scrolling
    initializeSmoothScrolling();
    
    // Load cart from localStorage
    loadCartFromStorage();
}

function initializeEventListeners() {
    // Menu filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const filter = e.target.dataset.filter;
            filterMenu(filter);
            
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
        });
    });

    // Base martabak selection
    document.querySelectorAll('input[name="base"]').forEach(input => {
        input.addEventListener('change', handleBaseSelection);
    });

    // Topping selections
    document.querySelectorAll('#martabakForm input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateOrderSummary);
    });

    // Add to cart button
    document.getElementById('addToCartBtn').addEventListener('click', addToCart);

    // Cart button in navbar
    document.getElementById('cartBtn').addEventListener('click', showCartModal);

    // Checkout button
    document.getElementById('checkoutBtn').addEventListener('click', checkout);

    // Add quick order buttons for menu items
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('quick-order-btn')) {
            const itemId = parseInt(e.target.dataset.itemId);
            quickOrder(itemId);
        }
    });
}

function loadMenuItems() {
    const container = document.getElementById('menuContainer');
    container.innerHTML = '';

    menuData.forEach(item => {
        const menuCard = createMenuCard(item);
        container.appendChild(menuCard);
    });
}

function createMenuCard(item) {
    const col = document.createElement('div');
    col.className = 'col-lg-4 col-md-6 mb-4';
    col.dataset.category = item.category;

    col.innerHTML = `
        <div class="card menu-card h-100" data-aos="fade-up">
            ${item.isSignature ? '<span class="badge badge-signature">Signature</span>' : ''}
            <div class="position-relative overflow-hidden">
                <img src="${item.image}" class="card-img-top" alt="${item.name}">
            </div>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">${item.name}</h5>
                <p class="card-text text-muted flex-grow-1">${item.description}</p>
                <div class="mb-2">
                    <small class="text-muted">Berisi: ${item.ingredients.join(', ')}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 text-primary mb-0">Rp ${item.price.toLocaleString('id-ID')}</span>
                    <button class="btn btn-outline-primary quick-order-btn" data-item-id="${item.id}">
                        <i class="fas fa-plus"></i> Pesan
                    </button>
                </div>
            </div>
        </div>
    `;

    return col;
}

function filterMenu(category) {
    const menuItems = document.querySelectorAll('#menuContainer > div');
    
    menuItems.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
            item.classList.add('fade-in');
        } else {
            item.style.display = 'none';
        }
    });
}

function handleBaseSelection(e) {
    const baseType = e.target.value;
    currentOrder.base = {
        type: baseType,
        price: parseInt(e.target.dataset.price)
    };

    // Show/hide relevant sections
    const proteinSection = document.getElementById('proteinSection');
    const extrasSection = document.getElementById('extrasSection');
    const sweetSection = document.getElementById('sweetSection');

    if (baseType === 'asin') {
        proteinSection.style.display = 'block';
        extrasSection.style.display = 'block';
        sweetSection.style.display = 'none';
        // Clear sweet toppings
        document.querySelectorAll('#sweetSection input[type="checkbox"]').forEach(cb => cb.checked = false);
    } else {
        proteinSection.style.display = 'none';
        extrasSection.style.display = 'none';
        sweetSection.style.display = 'block';
        // Clear savory toppings
        document.querySelectorAll('#proteinSection input[type="checkbox"], #extrasSection input[type="checkbox"]').forEach(cb => cb.checked = false);
    }

    document.getElementById('addToCartBtn').disabled = false;
    updateOrderSummary();
}

function updateOrderSummary() {
    if (!currentOrder.base) return;

    const summaryDiv = document.getElementById('orderSummary');
    const totalPriceSpan = document.getElementById('totalPrice');
    
    let total = currentOrder.base.price;
    let summaryHTML = `
        <div class="mb-2">
            <strong>Base:</strong> Martabak ${currentOrder.base.type.charAt(0).toUpperCase() + currentOrder.base.type.slice(1)}
            <span class="float-end">Rp ${currentOrder.base.price.toLocaleString('id-ID')}</span>
        </div>
    `;

    // Get selected toppings
    const selectedToppings = [];
    document.querySelectorAll('#martabakForm input[type="checkbox"]:checked').forEach(checkbox => {
        const price = parseInt(checkbox.dataset.price);
        const label = checkbox.nextElementSibling.querySelector('span').textContent;
        selectedToppings.push({ name: label, price: price });
        total += price;
    });

    if (selectedToppings.length > 0) {
        summaryHTML += '<div class="mt-2"><strong>Topping:</strong></div>';
        selectedToppings.forEach(topping => {
            summaryHTML += `
                <div class="ms-3">
                    ${topping.name}
                    <span class="float-end">+Rp ${topping.price.toLocaleString('id-ID')}</span>
                </div>
            `;
        });
    }

    summaryDiv.innerHTML = summaryHTML;
    totalPriceSpan.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    currentOrder.total = total;
    currentOrder.toppings = selectedToppings;
}

function addToCart() {
    if (!currentOrder.base) {
        Swal.fire({
            title: 'Pilih Base Martabak',
            text: 'Silakan pilih base martabak terlebih dahulu',
            icon: 'warning',
            confirmButtonColor: '#FF6B35'
        });
        return;
    }

    // Create cart item
    const cartItem = {
        id: Date.now(),
        base: currentOrder.base,
        toppings: [...currentOrder.toppings],
        total: currentOrder.total,
        quantity: 1
    };

    cart.push(cartItem);
    updateCartDisplay();
    saveCartToStorage();

    // Reset form
    resetOrderForm();

    // Show success message
    Swal.fire({
        title: 'Berhasil!',
        text: 'Item berhasil ditambahkan ke keranjang',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');
    const cartCountSpan = document.getElementById('cartCount');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="text-muted text-center">Keranjang kosong</p>';
        cartTotalSpan.textContent = 'Rp 0';
        cartCountSpan.textContent = '0';
        checkoutBtn.disabled = true;
        return;
    }

    let cartHTML = '';
    let grandTotal = 0;

    cart.forEach((item, index) => {
        grandTotal += item.total;
        cartHTML += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="cart-item-title">Martabak ${item.base.type.charAt(0).toUpperCase() + item.base.type.slice(1)}</div>
                        <div class="small text-muted">
                            ${item.toppings.map(t => t.name).join(', ') || 'Tanpa topping tambahan'}
                        </div>
                        <div class="cart-item-price mt-1">Rp ${item.total.toLocaleString('id-ID')}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    cartItemsDiv.innerHTML = cartHTML;
    cartTotalSpan.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
    cartCountSpan.textContent = cart.length.toString();
    checkoutBtn.disabled = false;
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
    saveCartToStorage();
    
    Swal.fire({
        title: 'Item Dihapus',
        text: 'Item berhasil dihapus dari keranjang',
        icon: 'info',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function quickOrder(itemId) {
    const item = menuData.find(m => m.id === itemId);
    if (!item) return;

    // Add directly to cart
    const cartItem = {
        id: Date.now(),
        base: { type: item.category, price: item.price },
        toppings: [],
        total: item.price,
        quantity: 1,
        isQuickOrder: true,
        name: item.name
    };

    cart.push(cartItem);
    updateCartDisplay();
    saveCartToStorage();

    Swal.fire({
        title: 'Ditambahkan!',
        text: `${item.name} berhasil ditambahkan ke keranjang`,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function resetOrderForm() {
    document.getElementById('martabakForm').reset();
    document.getElementById('proteinSection').style.display = 'none';
    document.getElementById('extrasSection').style.display = 'none';
    document.getElementById('sweetSection').style.display = 'none';
    document.getElementById('orderSummary').innerHTML = '<p class="text-muted text-center">Silakan pilih base martabak terlebih dahulu</p>';
    document.getElementById('totalPrice').textContent = 'Rp 0';
    document.getElementById('addToCartBtn').disabled = true;
    
    currentOrder = {
        base: null,
        toppings: [],
        total: 0
    };
}

function checkout() {
    if (cart.length === 0) return;

    // Create WhatsApp message
    let message = "Halo Martabak Lima! Saya ingin memesan:\n\n";
    let grandTotal = 0;

    cart.forEach((item, index) => {
        grandTotal += item.total;
        if (item.isQuickOrder) {
            message += `${index + 1}. ${item.name}\n`;
        } else {
            message += `${index + 1}. Martabak ${item.base.type.charAt(0).toUpperCase() + item.base.type.slice(1)}\n`;
            if (item.toppings.length > 0) {
                message += `   Topping: ${item.toppings.map(t => t.name).join(', ')}\n`;
            }
        }
        message += `   Harga: Rp ${item.total.toLocaleString('id-ID')}\n\n`;
    });

    message += `Total: Rp ${grandTotal.toLocaleString('id-ID')}\n\n`;
    message += "Terima kasih!";

    // Encode message for WhatsApp URL
    const encodedMessage = encodeURIComponent(message);
    const whatsappURL = `https://wa.me/6281234567890?text=${encodedMessage}`;

    // Open WhatsApp
    window.open(whatsappURL, '_blank');
}

function saveCartToStorage() {
    localStorage.setItem('martabakLimaCart', JSON.stringify(cart));
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem('martabakLimaCart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartDisplay();
    }
}

function showCartModal() {
    // This function can be expanded to show a modal instead of the sidebar cart
    Swal.fire({
        title: 'Keranjang Belanja',
        html: generateCartModalHTML(),
        showConfirmButton: false,
        showCloseButton: true,
        width: '600px',
        customClass: {
            container: 'cart-modal'
        }
    });
}

function generateCartModalHTML() {
    if (cart.length === 0) {
        return '<p class="text-center text-muted">Keranjang kosong</p>';
    }

    let html = '<div class="cart-modal-content">';
    let grandTotal = 0;

    cart.forEach((item, index) => {
        grandTotal += item.total;
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="background: #f8f9fa; border-radius: 8px;">
                <div>
                    <strong>${item.isQuickOrder ? item.name : `Martabak ${item.base.type}`}</strong>
                    ${!item.isQuickOrder && item.toppings.length > 0 ? `<br><small class="text-muted">${item.toppings.map(t => t.name).join(', ')}</small>` : ''}
                    <br><span class="text-primary">Rp ${item.total.toLocaleString('id-ID')}</span>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index}); Swal.close();">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });

    html += `
        <hr>
        <div class="d-flex justify-content-between">
            <strong>Total: Rp ${grandTotal.toLocaleString('id-ID')}</strong>
            <button class="btn btn-success" onclick="checkout(); Swal.close();">
                <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
            </button>
        </div>
    </div>`;

    return html;
}

function initializeSmoothScrolling() {
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('Error occurred:', e.error);
    Swal.fire({
        title: 'Oops!',
        text: 'Terjadi kesalahan. Silakan refresh halaman atau hubungi kami.',
        icon: 'error',
        confirmButtonColor: '#FF6B35'
    });
});

// Service Worker registration for PWA capabilities (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
    });
}