let currentOrder = [];

function addToOrder(product) {
  const existingItem = currentOrder.find((item) => item.id === product.id);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    currentOrder.push({ ...product, quantity: 1 });
  }
  renderOrder();
}

function updateQuantity(id, delta) {
  const item = currentOrder.find((item) => item.id === id);
  if (item) {
    item.quantity += delta;
    if (item.quantity <= 0) {
      currentOrder = currentOrder.filter((i) => i.id !== id);
    }
  }
  renderOrder();
}

function renderOrder() {
  const container = document.getElementById('order-items-container');
  const totalEl = document.getElementById('total-display');

  if (currentOrder.length === 0) {
    container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-secondary opacity-40">
            <i data-lucide="shopping-cart" class="size-12 mb-4"></i>
            <p>No items in order</p>
        </div>`;
    totalEl.innerText = '₱0.00';
    lucide.createIcons();
    return;
  }
  let total = 0;
  container.innerHTML = currentOrder
    .map((item) => {
      const price = parseFloat(item.price);
      const itemTotal = price * item.quantity;
      total += itemTotal;
      return `
            <div class="bg-white p-3 rounded-md flex justify-between items-center border-1 border-[#6f4e37]/20 animate-in fade-in slide-in-from-right-4">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-lg overflow-hidden bg-gray-100">
                        <img src="/cafe_404/public/assets/images/uploads/menu/${item.image_path}" 
                            alt="${item.name}" 
                            class="object-cover w-full h-full">
                    </div>
                    <div class="flex flex-col">
                        <h4 class="font-bold text-title text-on-surface text-sm leading-tight mb-1">${item.name}</h4>
                        <span class="text-subtitle font-semibold text-xs">₱${item.price.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 px-2 py-1">
                    <button onclick="updateQuantity(${item.id}, -1)" 
                            class="text-secondary bg-[#F0EDED]/40 hover:bg-[#F0EDED]/70 py-1 px-3 rounded-md hover:text-primary font-bold  transition-colors">−</button>
                    <span class="text-subtitle font-bold text-xs text-on-surface text-center">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)" 
                            class="text-secondary bg-[#F0EDED]/40 hover:bg-[#F0EDED]/70 p-1 px-3 rounded-md hover:text-primary font-bold  transition-colors">+</button>
                </div>
            </div>
        `;
    })
    .join('');

  if (totalEl) {
    totalEl.innerText = total.toLocaleString(undefined, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  lucide.createIcons();
}

function clearOrder() {
  currentOrder = [];
  renderOrder();
}

async function processCheckout() {
  if (currentOrder.length === 0) {
    alert('Wait! The cart is empty.');
    return;
  }

  const cashInput = document.getElementById('cash-amount');
  const amountReceived = parseFloat(cashInput.value) || 0;

  const total = currentOrder.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0,
  );

  if (amountReceived < total) {
    alert(
      `Insufficient cash! You still need ₱${(total - amountReceived).toFixed(2)}`,
    );
    return;
  }

  const formattedItems = currentOrder.map((item) => ({
    id: parseInt(item.id),
    quantity: parseInt(item.quantity),
    price: parseFloat(item.price),
  }));
  const orderData = {
    items: formattedItems,
    total_amount: total,
    amount_received: amountReceived,
  };

  try {
    const response = await fetch('/cafe_404/pos/checkout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(orderData),
    });

    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new TypeError("Oops, the server didn't send back JSON!");
    }

    const result = await response.json();

    if (result.success) {
      alert('✨ Order Placed Successfully!');

      currentOrder = [];
      cashInput.value = '';

      const changeDisplay = document.getElementById('change-display');
      if (changeDisplay) changeDisplay.style.opacity = '0';

      renderOrder();
    } else {
      alert('Error: ' + result.message);
    }
  } catch (error) {
    console.error('Fetch error:', error);
    alert('Server connection failed. Check the console for details.');
  }
}
function calculateChange() {
  const totalText = document
    .getElementById('total-display')
    .innerText.replace('₱', '')
    .replace(',', '');
  const total = parseFloat(totalText);
  const received =
    parseFloat(document.getElementById('cash-amount').value) || 0;
  const changeDisplay = document.getElementById('change-display');

  if (received >= total) {
    const change = received - total;
    changeDisplay.innerText = `Change: ₱${change.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
    changeDisplay.classList.replace('text-rose-400', 'text-[#6f4e37]');
  } else {
    changeDisplay.innerText = `Short: ₱${(total - received).toFixed(2)}`;
    changeDisplay.classList.replace('text-[#6f4e37]', 'text-rose-400');
  }
}
