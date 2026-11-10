'use strict';

// Sidebar responsif untuk layar kecil.
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');

function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('show');
}

menuToggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    overlay?.classList.toggle('show');
});

overlay?.addEventListener('click', closeSidebar);

// Konfirmasi sebelum penghapusan data.
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.getAttribute('data-confirm') || 'Lanjutkan tindakan ini?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

// Tutup notifikasi setelah tombol X ditekan.
document.querySelectorAll('.alert-close').forEach((button) => {
    button.addEventListener('click', () => button.closest('.alert')?.remove());
});

// Preview total transaksi. Perhitungan final tetap dilakukan oleh Stored Function/Procedure di MariaDB.
const productSelect = document.getElementById('productSelect');
const quantityInput = document.getElementById('quantityInput');
const discountInput = document.getElementById('discountInput');
const unitPriceOutput = document.getElementById('unitPrice');
const subtotalOutput = document.getElementById('subtotal');
const discountOutput = document.getElementById('discountValue');
const grandTotalOutput = document.getElementById('grandTotal');
const stockHint = document.getElementById('stockHint');

const formatRupiah = (value) => new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
}).format(Number.isFinite(value) ? value : 0);

function updateTransactionPreview() {
    if (!productSelect || !quantityInput || !discountInput) return;

    const option = productSelect.selectedOptions[0];
    const price = Number(option?.dataset.price || 0);
    const stock = Number(option?.dataset.stock || 0);
    const quantity = Math.max(0, Number(quantityInput.value || 0));
    const discountRate = Math.min(100, Math.max(0, Number(discountInput.value || 0)));
    const subtotal = price * quantity;
    const discount = subtotal * discountRate / 100;
    const total = subtotal - discount;

    if (unitPriceOutput) unitPriceOutput.textContent = formatRupiah(price);
    if (subtotalOutput) subtotalOutput.textContent = formatRupiah(subtotal);
    if (discountOutput) discountOutput.textContent = formatRupiah(discount);
    if (grandTotalOutput) grandTotalOutput.textContent = formatRupiah(total);

    if (stockHint) {
        if (!option?.value) {
            stockHint.textContent = 'Pilih produk untuk melihat stok.';
        } else if (quantity > stock) {
            stockHint.textContent = `Stok tersedia ${stock}. Jumlah melebihi stok; transaksi akan ditolak.`;
        } else {
            stockHint.textContent = `Stok tersedia: ${stock} unit.`;
        }
    }
}

productSelect?.addEventListener('change', updateTransactionPreview);
quantityInput?.addEventListener('input', updateTransactionPreview);
discountInput?.addEventListener('input', updateTransactionPreview);
updateTransactionPreview();
