<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="custom-header">
        <div class="header-flex">
          <div class="brand-group">
            <div class="logo-box">R</div>
            <ion-title class="brand-name">RestoKu</ion-title>
          </div>
        </div>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="ion-padding-top main-bg">
      <div class="welcome-section ion-padding-horizontal">
        <h1>Halo, pelanggan! 👋</h1>
        <p>Mau makan enak apa hari ini?</p>
      </div>

      <div class="search-container ion-padding-horizontal">
        <div class="search-inner">
          <ion-icon :icon="searchOutline" class="search-icon"></ion-icon>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Cari menu favoritmu..." 
            class="minimal-input"
          />
        </div>
      </div>

      <div class="category-wrapper">
        <div class="chip-scroll">
          <button 
            v-for="cat in ['Semua', 'Makanan', 'Minuman', 'Snack']" 
            :key="cat"
            @click="activeCategory = cat"
            :class="['chip', activeCategory === cat ? 'chip-active' : '']"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <div v-if="loading" class="loader-container">
        <ion-spinner name="crescent" color="orange"></ion-spinner>
      </div>

      <ion-grid v-else class="ion-no-padding">
        <ion-row class="ion-padding-horizontal">
          <ion-col size="6" v-for="item in filteredMenus" :key="item.id" class="ion-padding-tiny">
            <div class="new-menu-card">
              <div class="img-box">
                <img :src="item.foto_url" @error="handleImgError" />
                <div v-if="item.stok_porsi <= 0" class="out-of-stock">Habis</div>
              </div>
              <div class="card-content">
                <div class="category-tag">{{ item.nama_kategori }}</div>
                <h2 class="menu-title">{{ item.nama_menu }}</h2>
                <div class="card-footer">
                  <span class="price-tag">Rp{{ Number(item.harga).toLocaleString('id-ID') }}</span>
                  <button class="add-circle-btn" @click="addToCart(item)" :disabled="item.stok_porsi <= 0">
                    <ion-icon :icon="addOutline"></ion-icon>
                  </button>
                </div>
              </div>
            </div>
          </ion-col>
        </ion-row>
      </ion-grid>

      <ion-modal :is-open="isModalOpen" @didDismiss="isModalOpen = false" :initial-breakpoint="0.8" :breakpoints="[0, 0.8, 0.95]">
        <div class="ion-padding checkout-form">
          <h2 class="form-title">Konfirmasi Pesanan</h2>
          
          <div class="nota-section mb-4">
            <label class="section-label">Nota Pesanan</label>
            <div class="nota-card">
              <div v-for="(item, index) in cart" :key="index" class="nota-item">
                <div class="nota-info">
                  <span class="nota-qty">1x</span>
                  <span class="nota-name">{{ item.nama_menu }}</span>
                </div>
                <div class="nota-price-group">
                  <span class="nota-price">Rp{{ Number(item.harga).toLocaleString('id-ID') }}</span>
                  <button class="btn-remove-nota" @click="removeFromCart(index)">×</button>
                </div>
              </div>
            </div>
          </div>

          <div class="order-type-toggle mb-4">
            <button @click="formOrder.jenis_pesanan = 'dinein'" :class="formOrder.jenis_pesanan === 'dinein' ? 'active' : ''">🍽️ Dine In</button>
            <button @click="formOrder.jenis_pesanan = 'takeaway'" :class="formOrder.jenis_pesanan === 'takeaway' ? 'active' : ''">🛍️ Take Away</button>
          </div>

          <div class="input-group">
            <label>Nama Pemesan</label>
            <input type="text" v-model="formOrder.nama" placeholder="Masukkan nama Anda" class="form-input">
          </div>

          <div v-if="formOrder.jenis_pesanan === 'dinein'" class="input-group mt-3">
            <label>Pilih Nomor Meja</label>
            <select v-model="formOrder.meja" class="form-input">
            <option value="">-- Pilih Meja --</option>
            <option v-for="m in mejas" :key="m.id" :value="m.id">
      Meja {{ m.nama_meja }}
    </option>
  </select>
</div>

          <div class="input-group mt-3">
            <label>Metode Pembayaran</label>
            <div class="payment-grid">
              <button @click="formOrder.pembayaran = 'cash'" :class="formOrder.pembayaran === 'cash' ? 'active' : ''">💵 Tunai</button>
              <button @click="formOrder.pembayaran = 'qris'" :class="formOrder.pembayaran === 'qris' ? 'active' : ''">📱 QRIS</button>
              <button @click="formOrder.pembayaran = 'debit'" :class="formOrder.pembayaran === 'debit' ? 'active' : ''">💳 Debit</button>
            </div>
          </div>

          <div class="input-group mt-3">
            <label>Catatan (Opsional)</label>
            <textarea v-model="formOrder.catatan" placeholder="Contoh: Tidak pakai sambal..." class="form-input" rows="2"></textarea>
          </div>

          <div class="summary-box mt-4">
            <div class="summary-item font-bold">
              <span class="text-dark">Total Bayar</span>
              <span class="text-orange">Rp {{ totalHarga.toLocaleString('id-ID') }}</span>
            </div>
          </div>

          <button class="btn-checkout-final mt-4" @click="processCheckout" :disabled="isSubmitting">
            {{ isSubmitting ? 'Memproses...' : 'Kirim Pesanan' }}
          </button>
        </div>
      </ion-modal>

      <div class="spacer-bottom"></div>
    </ion-content>

    <div v-if="cart.length > 0" class="cart-pill-container">
      <div class="cart-pill" @click="isModalOpen = true">
        <div class="cart-left">
          <div class="badge">{{ cart.length }}</div>
          <span class="cart-total">Rp {{ totalHarga.toLocaleString('id-ID') }}</span>
        </div>
        <div class="cart-right">
          <span>Checkout</span>
          <ion-icon :icon="chevronForwardOutline"></ion-icon>
        </div>
      </div>
    </div>
  </ion-page>
</template>

<script setup lang="ts">
import { 
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, 
  IonButtons, IonButton, IonIcon, IonGrid, IonRow, IonCol,
  IonSpinner, IonModal, onIonViewWillEnter, alertController 
} from '@ionic/vue';
import { 
  personOutline, searchOutline, addOutline, 
  chevronForwardOutline 
} from 'ionicons/icons';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const activeCategory = ref('Semua');
const searchQuery = ref('');
const menus = ref<any[]>([]);
const mejas = ref<any[]>([]); // Data meja dari database
const cart = ref<any[]>([]);
const loading = ref(false);

const isModalOpen = ref(false);
const isSubmitting = ref(false);
const formOrder = ref({ 
  nama: '', 
  meja: '', 
  jenis_pesanan: 'dinein', 
  pembayaran: 'cash',
  catatan: ''
});

const BASE_API = "http://localhost/api-restoran/";

const fetchData = async () => {
  loading.value = true;
  try {
    const [resMenu, resMeja] = await Promise.all([
      fetch(BASE_API + 'read_menu.php'),
      fetch(BASE_API + 'read_meja.php') // Mengambil data meja
    ]);
    const menuResult = await resMenu.json();
    const mejaResult = await resMeja.json();
    if (menuResult.status === 'success') menus.value = menuResult.data;
    if (mejaResult.status === 'success') mejas.value = mejaResult.data;
  } catch (error) {
    console.error('API Error:', error);
  } finally {
    loading.value = false;
  }
};

onIonViewWillEnter(fetchData);

const filteredMenus = computed(() => {
  return menus.value.filter(item => {
    const matchCat = activeCategory.value === 'Semua' || 
                     (item.nama_kategori && item.nama_kategori.toLowerCase() === activeCategory.value.toLowerCase());
    const matchSearch = item.nama_menu.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchCat && matchSearch;
  });
});

const totalHarga = computed(() => cart.value.reduce((sum, item) => sum + Number(item.harga), 0));
const addToCart = (item: any) => cart.value.push(item);
const removeFromCart = (index: number) => { 
  cart.value.splice(index, 1); 
  if (cart.value.length === 0) isModalOpen.value = false; 
};
const goToLogin = () => router.push('/login');
const handleImgError = (ev: any) => ev.target.src = 'https://via.placeholder.com/300x200?text=Food';

const processCheckout = async () => {
  if (!formOrder.value.nama) {
    const alert = await alertController.create({ header: 'Peringatan', message: 'Isi nama pemesan!', buttons: ['OK'] });
    await alert.present();
    return;
  }
  if (formOrder.value.jenis_pesanan === 'dinein' && !formOrder.value.meja) {
    const alert = await alertController.create({ header: 'Peringatan', message: 'Pilih nomor meja!', buttons: ['OK'] });
    await alert.present();
    return;
  }

  isSubmitting.value = true;
  const payload = {
    nama_konsumen: formOrder.value.nama,
    id_meja: formOrder.value.jenis_pesanan === 'dinein' ? formOrder.value.meja : null,
    total_bayar: totalHarga.value,
    metode_pembayaran: formOrder.value.pembayaran,
    jenis_pesanan: formOrder.value.jenis_pesanan,
    items: cart.value.map(item => ({
      id_menu: item.id,
      jumlah: 1,
      subtotal: item.harga,
      catatan: formOrder.value.catatan
    }))
  };

  try {
    const res = await fetch(BASE_API + 'simpan_pesanan.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    if (result.status === 'success') {
      const alert = await alertController.create({ header: 'Sukses', message: 'Pesanan terkirim!', buttons: ['Mantap'] });
      await alert.present();
      cart.value = [];
      formOrder.value = { nama: '', meja: '', jenis_pesanan: 'dinein', pembayaran: 'cash', catatan: '' };
      isModalOpen.value = false;
      fetchData(); // Refresh data menu & stok
    }
  } catch (error) {
    console.error("Checkout Error:", error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* GLOBAL & FONTS */
.main-bg { --background: #ffffff; font-family: 'Inter', sans-serif; }
.custom-header { --background: #ffffff; padding: 10px 5px; }
.header-flex { display: flex; justify-content: space-between; align-items: center; width: 100%; }
.brand-group { display: flex; align-items: center; gap: 10px; }
.logo-box { background: #ea580c; color: white; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; }
.brand-name { font-weight: 800; color: #111827; font-size: 18px; }
.profile-btn { --color: #6b7280; background: #f3f4f6; border-radius: 50%; width: 40px; height: 40px; }
.welcome-section h1 { font-size: 22px; font-weight: 800; color: #111827; margin: 15px 0 5px; }
.welcome-section p { color: #6b7280; font-size: 14px; margin: 0; }
.search-container { margin-top: 20px; }
.search-inner { background: #f3f4f6; border-radius: 16px; display: flex; align-items: center; padding: 12px 16px; gap: 12px; }
.search-icon { color: #9ca3af; font-size: 20px; }
.minimal-input { border: none; background: transparent; width: 100%; outline: none; font-size: 14px; color: #374151; }
.chip-scroll { display: flex; overflow-x: auto; padding: 0 20px; gap: 10px; scrollbar-width: none; }
.chip-scroll::-webkit-scrollbar { display: none; }
.chip { padding: 10px 24px; border-radius: 50px; background: #f3f4f6; border: none; color: #6b7280; font-size: 13px; font-weight: 600; white-space: nowrap; transition: 0.3s; }
.chip-active { background: #ea580c; color: white; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2); }
.ion-padding-tiny { padding: 8px; }
.new-menu-card { background: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #f3f4f6; height: 100%; transition: 0.2s; }
.img-box { position: relative; height: 110px; width: 100%; }
.img-box img { width: 100%; height: 100%; object-fit: cover; }
.out-of-stock { position: absolute; inset: 0; background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; color: #dc2626; font-weight: 800; font-size: 12px; text-transform: uppercase; }
.card-content { padding: 12px; }
.category-tag { color: #ea580c; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.menu-title { font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 10px; line-clamp: 1; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
.price-tag { font-weight: 800; color: #111827; font-size: 14px; }
.add-circle-btn { background: #111827; color: white; border: none; width: 30px; height: 30px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.add-circle-btn:disabled { background: #e5e7eb; color: #9ca3af; }

/* MODAL FIX TEKS HITAM */
.checkout-form { padding-top: 10px; overflow-y: auto; background: #ffffff; }
.form-title { font-size: 20px; font-weight: 800; color: #111827 !important; margin-bottom: 20px; }

/* NOTA PESANAN SECTION */
.nota-section { margin-bottom: 20px; }
.section-label { display: block; font-size: 11px; font-weight: 700; color: #6b7280 !important; margin-bottom: 8px; text-transform: uppercase; }
.nota-card { background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 12px; padding: 12px; }
.nota-item { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
.nota-item:last-child { border-bottom: none; }
.nota-info { display: flex; gap: 8px; align-items: center; }
.nota-qty { font-weight: 800; color: #ea580c; font-size: 13px; }
.nota-name { font-size: 13px; font-weight: 600; color: #111827 !important; }
.nota-price-group { display: flex; align-items: center; gap: 10px; }
.nota-price { font-size: 13px; font-weight: 700; color: #374151 !important; }
.btn-remove-nota { background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 14px; }

.order-type-toggle { background: #f3f4f6; padding: 5px; border-radius: 12px; display: flex; gap: 5px; }
.order-type-toggle button { flex: 1; padding: 10px; border-radius: 10px; border: none; font-weight: 700; font-size: 13px; transition: 0.3s; color: #6b7280; }
.order-type-toggle button.active { background: white; color: #ea580c; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

.input-group label { display: block; font-size: 12px; font-weight: 700; color: #4b5563 !important; margin-bottom: 8px; text-transform: uppercase; }
.form-input { 
  width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #e5e7eb; 
  background: #ffffff; color: #111827 !important; font-size: 14px; outline: none; transition: 0.3s; 
}
.form-input::placeholder { color: #9ca3af !important; }
.payment-grid { display: flex; gap: 8px; }
.payment-grid button { 
  flex: 1; padding: 10px; border-radius: 12px; border: 1px solid #e5e7eb; 
  background: white; font-size: 11px; font-weight: 700; color: #4b5563 !important; transition: 0.3s; 
}
.payment-grid button.active { border-color: #ea580c; background: #fff7ed; color: #ea580c !important; }

.summary-box { background: #f9fafb; border-radius: 16px; padding: 16px; border: 1px solid #f3f4f6; }
.summary-item { display: flex; justify-content: space-between; font-size: 14px; color: #374151 !important; }
.text-dark { color: #111827 !important; }
.text-orange { color: #ea580c !important; font-weight: 800; }
.btn-checkout-final { width: 100%; padding: 16px; border-radius: 16px; background: #ea580c; color: #ffffff !important; font-weight: 800; border: none; }
.btn-checkout-final:disabled { background: #d1d5db; color: #9ca3af !important; }

.cart-pill-container { position: fixed; bottom: 25px; width: 100%; display: flex; justify-content: center; z-index: 1000; padding: 0 20px; }
.cart-pill { background: #111827; color: white; padding: 12px 20px; border-radius: 50px; display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.badge { background: #ea580c; color: white; min-width: 24px; height: 24px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; }
.cart-total { font-weight: 700; font-size: 14px; color: white; }
.cart-right { display: flex; align-items: center; gap: 5px; font-weight: 800; font-size: 13px; color: #ea580c; }
.spacer-bottom { height: 120px; }
</style>