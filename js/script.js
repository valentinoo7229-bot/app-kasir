document.addEventListener('DOMContentLoaded', function() {
    const cart = [];
    const cartBody = document.getElementById('cart-body');
    const totalHargaElement = document.getElementById('total-harga');
    const uangBayarInput = document.getElementById('uang-bayar');
    const uangKembaliInput = document.getElementById('uang-kembali');
    const formPembayaran = document.getElementById('form-pembayaran');
    const btnBatal = document.getElementById('btn-batal');
    const produkCards = document.querySelectorAll('.produk-card');
    const pelangganSelect = document.getElementById('pelanggan');
    const pelangganIdInput = document.getElementById('pelanggan_id');
    const totalBayarInput = document.getElementById('total_bayar');
    const cartDataInput = document.getElementById('cart_data');

    // Fungsi untuk render keranjang
    function renderCart() {
        cartBody.innerHTML = '';
        let totalHarga = 0;

        if (cart.length === 0) {
            cartBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Keranjang kosong</td></tr>';
            totalHargaElement.textContent = 'Rp. 0';
            uangKembaliInput.value = 'Rp. 0';
            totalBayarInput.value = 0;
            cartDataInput.value = JSON.stringify([]);
            return;
        }

        cart.forEach((item, index) => {
            const subtotal = item.harga * item.qty;
            totalHarga += subtotal;

            const row = document.createElement('tr');
            // PERUBAHAN ADA DI BAGIAN INI
            row.innerHTML = `
                <td>${item.nama}</td>
                <td>Rp. ${item.harga.toFixed(2)}</td>
                <td>
                    <div class="qty-control">
                        <button class="btn-qty btn-kurang" data-index="${index}">-</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="btn-qty btn-tambah" data-index="${index}">+</button>
                    </div>
                </td>
                <td>Rp. ${subtotal.toFixed(2)}</td>
                <td><button class="btn-hapus" data-index="${index}">Hapus</button></td>
            `;
            cartBody.appendChild(row);
        });

        totalHargaElement.textContent = `Rp. ${totalHarga.toFixed(2)}`;
        totalBayarInput.value = totalHarga.toFixed(2);
        cartDataInput.value = JSON.stringify(cart.map(item => ({
            id: item.id,
            nama: item.nama,
            harga: item.harga,
            qty: item.qty,
            subtotal: (item.harga * item.qty).toFixed(2)
        })));
        calculateChange();
    }

    // Fungsi untuk menghitung kembalian
    function calculateChange() {
        const totalHarga = parseFloat(totalBayarInput.value) || 0;
        const uangBayar = parseFloat(uangBayarInput.value) || 0;
        const uangKembali = uangBayar - totalHarga;
        uangKembaliInput.value = `Rp. ${uangKembali.toFixed(2)}`;
    }

    // Event listener untuk produk card
    produkCards.forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const harga = parseFloat(this.dataset.harga);

            const existingItem = cart.find(item => item.id == id);
            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({ id, nama, harga, qty: 1 });
            }
            renderCart();
        });
    });

    // Event listener untuk aksi di keranjang (tambah, kurang, hapus)
    cartBody.addEventListener('click', function(e) {
        const target = e.target;
        const index = target.dataset.index;

        if (target.classList.contains('btn-hapus')) {
            cart.splice(index, 1);
            renderCart();
        } else if (target.classList.contains('btn-tambah')) {
            cart[index].qty++;
            renderCart();
        } else if (target.classList.contains('btn-kurang')) {
            // Jika qty lebih dari 1, kurangi. Jika 1, hapus item.
            if (cart[index].qty > 1) {
                cart[index].qty--;
            } else {
                cart.splice(index, 1);
            }
            renderCart();
        }
    });

    // Event listener untuk input uang bayar
    uangBayarInput.addEventListener('input', calculateChange);

    // Event listener untuk select pelanggan
    pelangganSelect.addEventListener('change', function() {
        pelangganIdInput.value = this.value;
    });

    // Event listener untuk tombol batal
    btnBatal.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin membatalkan transaksi?')) {
            cart.length = 0; // Kosongkan keranjang
            renderCart();
            pelangganSelect.value = '';
            pelangganIdInput.value = '';
            uangBayarInput.value = '';
        }
    });

    // Inisialisasi tampilan keranjang
    renderCart();
});