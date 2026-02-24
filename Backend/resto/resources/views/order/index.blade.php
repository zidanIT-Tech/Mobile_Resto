@extends('layouts.main')

@section('title', 'Order Menu')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Menu</h2>
            <p class="text-sm text-gray-500">Silakan pilih menu favorit Anda</p>
        </div>
        <input type="text" id="search-input" onkeyup="searchMenu()" placeholder="Cari menu..." class="border rounded-full px-4 py-2 text-sm w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white shadow-sm transition">
    </div>

    <div class="flex space-x-2 mb-8 overflow-x-auto no-scrollbar pb-2 sticky top-0 z-20 bg-gray-50/90 backdrop-blur-sm py-2">
        <button onclick="filterMenu('all')" class="category-btn px-6 py-2 rounded-full bg-orange-500 text-white shadow-md transition font-semibold text-sm whitespace-nowrap active:scale-95" id="btn-all">Semua</button>
        @php
            $kategoriUnik = $menus->pluck('kategori.nama_kategori')->unique();
        @endphp
        @foreach($kategoriUnik as $katName)
            <button onclick="filterMenu('{{ Str::slug($katName) }}')" class="category-btn px-6 py-2 rounded-full bg-white text-gray-600 border hover:bg-orange-50 transition font-semibold text-sm whitespace-nowrap active:scale-95" id="btn-{{ Str::slug($katName) }}">{{ $katName }}</button>
        @endforeach
    </div>

    <div id="menu-container" class="pb-24 space-y-10">
        @php
            // Mengelompokkan menu berdasarkan nama kategori
            $groupedMenus = $menus->groupBy(function($item) {
                return $item->kategori->nama_kategori ?? 'Lainnya';
            });
        @endphp

        @foreach($groupedMenus as $kategoriNama => $items)
        <div class="menu-section" id="section-{{ Str::slug($kategoriNama) }}" data-category="{{ Str::slug($kategoriNama) }}">
            
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800 border-l-4 border-orange-500 pl-3">{{ $kategoriNama }}</h3>
                <span class="text-xs text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full">{{ $items->count() }} Item</span>
                <div class="h-px bg-gray-200 flex-grow"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $menu)
                <div class="menu-item bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                    
                    <div class="h-40 bg-gray-100 relative overflow-hidden">
                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <span class="absolute top-2 right-2 bg-black/60 text-white text-[10px] font-bold px-2 py-1 rounded-md backdrop-blur-sm">
                            Stok: {{ $menu->stok_porsi }}
                        </span>
                    </div>
                    
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="menu-name font-bold text-gray-800 text-lg mb-1 truncate leading-tight">{{ $menu->nama_menu }}</h3>
                        <p class="text-gray-400 text-xs mb-4 line-clamp-2 min-h-[2.5em]">{{ $menu->deskripsi }}</p>
                        
                        <div class="mt-auto flex justify-between items-center">
                            <span class="text-orange-600 font-bold text-lg">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            <button onclick="addToCart({{ $menu->id }}, '{{ $menu->nama_menu }}', {{ $menu->harga }})" 
                                    class="bg-orange-100 text-orange-600 hover:bg-orange-500 hover:text-white p-2.5 rounded-xl transition shadow-sm active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div id="floating-cart" class="fixed bottom-6 left-4 right-4 md:left-auto md:right-8 md:w-96 bg-gray-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center cursor-pointer hidden hover:bg-black transition z-50 transform hover:-translate-y-1" onclick="showModal()">
        <div class="flex flex-col">
            <span class="text-xs text-gray-400">Total Pesanan</span>
            <div class="font-bold text-lg">
                <span id="cart-total-qty" class="text-orange-400">0</span> Item | Rp <span id="cart-total-price">0</span>
            </div>
        </div>
        <div class="flex items-center font-bold bg-white text-gray-900 px-4 py-2 rounded-xl text-sm">
            Lanjut Bayar
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
        </div>
    </div>

    <div id="checkout-modal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col animate-fade-in-up">
            
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center sticky top-0 z-10">
                <h2 class="text-lg font-bold text-gray-800">Konfirmasi Pesanan</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition text-2xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200">&times;</button>
            </div>

            <div class="p-5 overflow-y-auto space-y-6">
                <div class="flex bg-gray-100 p-1 rounded-xl">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="dinein" class="peer hidden" checked onchange="toggleOrderType()">
                        <div class="py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white peer-checked:text-orange-600 peer-checked:shadow-sm transition">🍽️ Dine In</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="takeaway" class="peer hidden" onchange="toggleOrderType()">
                        <div class="py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white peer-checked:text-orange-600 peer-checked:shadow-sm transition">🛍️ Take Away</div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pemesan <span class="text-red-500">*</span></label>
                    <input type="text" id="input-nama" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 focus:bg-white" placeholder="Masukkan nama Anda..." required>
                </div>

                <div id="section-meja">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Meja <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($mejas as $meja)
                        <label class="group relative border rounded-xl p-2 text-center cursor-pointer transition {{ $meja->status != 'available' ? 'bg-gray-100 opacity-50 cursor-not-allowed' : 'hover:border-orange-500 hover:bg-orange-50' }}">
                            <input type="radio" name="pilih_meja" value="{{ $meja->id }}" class="peer hidden" {{ $meja->status != 'available' ? 'disabled' : '' }}>
                            <div class="absolute inset-0 border-2 border-orange-500 bg-orange-100 rounded-xl opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                            <div class="relative z-10">
                                <div class="font-bold text-gray-800 text-lg">{{ $meja->nomor_meja }}</div>
                                <div class="text-[10px] text-gray-500">{{ $meja->kapasitas }} Org</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer hidden" checked>
                            <div class="border rounded-xl p-2 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:bg-gray-50 transition">
                                <span class="block text-lg">💵</span>
                                <span class="text-xs font-bold">Tunai</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="qris" class="peer hidden">
                            <div class="border rounded-xl p-2 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:bg-gray-50 transition">
                                <span class="block text-lg">📱</span>
                                <span class="text-xs font-bold">QRIS</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="debit" class="peer hidden">
                            <div class="border rounded-xl p-2 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:bg-gray-50 transition">
                                <span class="block text-lg">💳</span>
                                <span class="text-xs font-bold">Debit</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea id="input-catatan" rows="2" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none bg-gray-50 focus:bg-white resize-none" placeholder="Contoh: Jangan terlalu pedas..."></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
                    <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-4">Rincian Menu</h3>
                    <div id="modal-cart-list" class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar"></div>
                    <div class="flex justify-between font-bold mt-4 pt-4 border-t border-gray-200 text-lg">
                        <span>Total Bayar</span>
                        <span id="modal-total-price" class="text-orange-600">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t bg-white flex gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <button onclick="closeModal()" class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">Batal</button>
                <button onclick="processCheckout()" class="flex-1 py-3 rounded-xl bg-orange-600 text-white font-bold hover:bg-orange-700 shadow-lg shadow-orange-200 transition transform active:scale-95">Pesan Sekarang</button>
            </div>
        </div>
    </div>

    <div id="success-modal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-8 text-center animate-fade-in-up transform scale-100 transition-all">
            <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mb-3">Pesanan Berhasil!</h2>
            <p id="success-message" class="text-gray-500 text-sm mb-8 leading-relaxed"></p>
            
            <button onclick="window.location.reload()" class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-200 transform active:scale-95">
                Kembali ke Menu
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // --- 0. FUNGSI PENCARIAN (SEARCH) BARU ---
    function searchMenu() {
        let input = document.getElementById('search-input').value.toLowerCase();
        let items = document.querySelectorAll('.menu-item');
        let sections = document.querySelectorAll('.menu-section'); // Ambil semua section

        // Jika sedang mencari, tampilkan semua section dulu
        if (input.length > 0) {
            sections.forEach(sec => sec.style.display = 'block');
            
            // Reset style tombol filter
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-gray-600', 'border');
            });
            let btnAll = document.getElementById('btn-all');
            if(btnAll) {
                 btnAll.classList.remove('bg-white', 'text-gray-600', 'border');
                 btnAll.classList.add('bg-orange-500', 'text-white', 'shadow-md');
            }
        }

        items.forEach(item => {
            let name = item.querySelector('.menu-name').innerText.toLowerCase();
            if (name.includes(input)) {
                item.style.display = 'flex';
                // Pastikan parent section-nya tampil juga
                item.closest('.menu-section').style.display = 'block'; 
            } else {
                item.style.display = 'none';
            }
        });

        // (Opsional) Sembunyikan Section yang kosong jika hasil pencarian tidak ada di section tsb
        // Tapi untuk simplifikasi, kita biarkan section tampil, hanya itemnya yang hilang.
    }

    // --- 0. TOGGLE DINE IN / TAKE AWAY ---
    function toggleOrderType() {
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const sectionMeja = document.getElementById('section-meja');
        
        if (orderType === 'takeaway') {
            sectionMeja.classList.add('hidden');
            const checkedMeja = document.querySelector('input[name="pilih_meja"]:checked');
            if (checkedMeja) checkedMeja.checked = false;
        } else {
            sectionMeja.classList.remove('hidden');
        }
    }

    // --- 1. FILTER KATEGORI (UPDATED UNTUK SECTION) ---
    function filterMenu(categorySlug) {
        // Kosongkan search bar
        document.getElementById('search-input').value = '';

        // Update Button Style
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('bg-orange-500', 'text-white', 'shadow-md');
            btn.classList.add('bg-white', 'text-gray-600', 'border');
        });
        let activeBtn = document.getElementById('btn-' + categorySlug);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-gray-600', 'border');
            activeBtn.classList.add('bg-orange-500', 'text-white', 'shadow-md');
        }

        // Logic Show/Hide Section
        let sections = document.querySelectorAll('.menu-section');
        sections.forEach(section => {
            // Pastikan semua item di dalam section ditampilkan kembali (reset dari pencarian)
            let items = section.querySelectorAll('.menu-item');
            items.forEach(i => i.style.display = 'flex');

            if (categorySlug === 'all') {
                section.style.display = 'block';
            } else {
                if (section.getAttribute('data-category') === categorySlug) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            }
        });
    }

    // --- 2. LOGIC KERANJANG (CART) ---
    let cart = [];

    function addToCart(id, nama, harga) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
        } else {
            cart.push({ id: id, nama: nama, harga: harga, qty: 1 });
        }
        updateFloatingCart();
        let cartBtn = document.getElementById('floating-cart');
        cartBtn.classList.add('scale-105');
        setTimeout(() => cartBtn.classList.remove('scale-105'), 200);
    }

    function changeQty(id, change) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
        }
        updateFloatingCart();
        showModal();
    }

    function updateFloatingCart() {
        let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        let totalPrice = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);

        document.getElementById('cart-total-qty').innerText = totalQty;
        document.getElementById('cart-total-price').innerText = totalPrice.toLocaleString('id-ID');

        let floatingCart = document.getElementById('floating-cart');
        if (totalQty > 0) {
            floatingCart.classList.remove('hidden');
            floatingCart.classList.add('flex');
        } else {
            floatingCart.classList.add('hidden');
            floatingCart.classList.remove('flex');
            closeModal();
        }
    }

    // --- 3. LOGIC MODAL CHECKOUT ---
    function showModal() {
        let listContainer = document.getElementById('modal-cart-list');
        listContainer.innerHTML = ''; 
        let totalPrice = 0;

        if(cart.length === 0) return;

        cart.forEach(item => {
            let subtotal = item.harga * item.qty;
            totalPrice += subtotal;

            listContainer.innerHTML += `
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex-1">
                        <div class="font-bold text-gray-800">${item.nama}</div>
                        <div class="text-xs text-gray-500">@ Rp ${item.harga.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button onclick="changeQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow text-orange-600 font-bold hover:bg-orange-50 active:scale-90 transition">-</button>
                            <span class="w-8 text-center font-bold text-sm text-gray-700">${item.qty}</span>
                            <button onclick="changeQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow text-green-600 font-bold hover:bg-green-50 active:scale-90 transition">+</button>
                        </div>
                        <div class="font-bold text-gray-700 text-sm w-20 text-right">Rp ${subtotal.toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
        });

        document.getElementById('modal-total-price').innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
        document.getElementById('checkout-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('checkout-modal').classList.add('hidden');
    }

    // --- 4. KIRIM DATA ---
    function processCheckout() {
        let nama = document.getElementById('input-nama').value;
        let mejaInput = document.querySelector('input[name="pilih_meja"]:checked');
        let orderType = document.querySelector('input[name="order_type"]:checked').value;
        let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        let catatan = document.getElementById('input-catatan').value;

        if (!nama) {
            alert("Mohon isi Nama Pemesan!");
            document.getElementById('input-nama').focus();
            return;
        }

        let mejaValue = null;
        if (orderType === 'dinein') {
            if (!mejaInput) {
                alert("Mohon Pilih Meja untuk Dine In!");
                return;
            }
            mejaValue = mejaInput.value;
        } 

        let payload = {
            nama_konsumen: nama,
            id_meja: mejaValue,
            jenis_pesanan: orderType,
            metode_pembayaran: paymentMethod,
            catatan: catatan,
            items: cart.map(item => ({
                id_menu: item.id,
                jumlah: item.qty
            }))
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if(!csrfToken) {
            alert("Error: CSRF Token tidak ditemukan.");
            return;
        }

        let btn = document.querySelector('button[onclick="processCheckout()"]');
        let originalText = btn.innerText;
        btn.innerText = "Memproses...";
        btn.disabled = true;

        fetch('/order/simpan', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                closeModal();
                let successMsg = "";
                if (orderType === 'dinein') {
                    successMsg = "Pesanan Anda sedang diproses. Silakan menunggu di meja yang telah dipilih.";
                } else {
                    successMsg = "Pesanan Anda sedang diproses. Silakan menunggu pesanan hingga nama anda dipanggil.";
                }
                document.getElementById('success-message').innerText = successMsg;
                document.getElementById('success-modal').classList.remove('hidden');
            } else {
                alert('Gagal: ' + data.message);
                btn.innerText = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan sistem.');
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }
</script>
@endpush