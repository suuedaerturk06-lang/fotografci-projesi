<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// State (Durum) Tanımlamaları
const photographers = ref([])
const loading = ref(false)
const error = ref(null)

// Filtre Değişkenleri
const selectedCity = ref('')
const selectedStyle = ref('')
const maxPrice = ref('')

// Türkiye'nin 81 İli
const turkeyCities = [
  "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Aksaray", "Amasya", "Ankara", "Antalya", "Ardahan", "Artvin",
  "Aydın", "Balıkesir", "Bartın", "Batman", "Bayburt", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur",
  "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Düzce", "Edirne", "Elazığ", "Erzincan",
  "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Iğdır", "Isparta", "İstanbul",
  "İzmir", "Kahramanmaraş", "Karabük", "Karaman", "Kars", "Kastamonu", "Kayseri", "Kırıkkale", "Kırklareli", "Kırşehir",
  "Kilis", "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", "Mardin", "Mersin", "Muğla", "Muş",
  "Nevşehir", "Niğde", "Ordu", "Osmaniye", "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas",
  "Şanlıurfa", "Şırnak", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Uşak", "Van", "Yalova", "Yozgat", "Zonguldak"
]

// API'den Fotoğrafçıları Çeken Fonksiyon
const fetchPhotographers = async () => {
  loading.value = true
  error.value = null
  try {
    const params = {}
    if (selectedCity.value) params.city = selectedCity.value
    if (selectedStyle.value) params.style = selectedStyle.value
    if (maxPrice.value) params.maxPrice = maxPrice.value

    const response = await axios.get('http://127.0.0.1:8000/api/photographers', { params })
    photographers.value = response.data
  } catch (err) {
    error.value = 'Veriler çekilirken bir hata oluştu. Symfony sunucusunun (8000 portu) açık olduğundan emin olun.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

// Filtreleri Temizleme
const clearFilters = () => {
  selectedCity.value = ''
  selectedStyle.value = ''
  maxPrice.value = ''
  fetchPhotographers()
}

// Sayfa ilk yüklendiğinde verileri çek
onMounted(() => {
  fetchPhotographers()
})
</script>

<template>
  <div class="container">
    <header class="header">
      <h1>📸 Fotoğrafçı Bul</h1>
      <p>İhtiyacınıza en uygun fotoğrafçıyı şehir, tarz ve bütçeye göre filtreleyin.</p>
    </header>

    <!-- Filtreleme Alanı -->
    <section class="filter-card">
      <div class="filter-group">
        <label>Şehir</label>
        <select v-model="selectedCity" @change="fetchPhotographers">
          <option value="">Tüm Şehirler (81 İl)</option>
          <option v-for="city in turkeyCities" :key="city" :value="city">
            {{ city }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <label>Çekim Tarzı</label>
        <select v-model="selectedStyle" @change="fetchPhotographers">
          <option value="">Tüm Tarzlar</option>
          <option value="Düğün">Düğün</option>
          <option value="Portre">Portre</option>
          <option value="Moda">Moda</option>
          <option value="Ürün">Ürün</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Maksimum Bütçe (₺)</label>
        <input 
          type="number" 
          v-model="maxPrice" 
          placeholder="Örn: 15000" 
          @input="fetchPhotographers"
        />
      </div>

      <button class="btn-reset" @click="clearFilters">Filtreleri Sıfırla</button>
    </section>

    <!-- Yükleniyor / Hata Durumları -->
    <div v-if="loading" class="state-msg">Fotoğrafçılar yükleniyor...</div>
    <div v-else-if="error" class="state-msg error">{{ error }}</div>

    <!-- Fotoğrafçı Listesi -->
    <section v-else class="photographer-grid">
      <div v-if="photographers.length === 0" class="no-result">
        Aradığınız kriterlere uygun fotoğrafçı bulunamadı.
      </div>

      <div 
        v-for="p in photographers" 
        :key="p.id" 
        class="card"
      >
        <div class="card-badge">{{ p.style }}</div>
        <h3>{{ p.name }}</h3>
        <p class="city">📍 {{ p.city }}</p>
        <div class="price">{{ p.price.toLocaleString('tr-TR') }} ₺</div>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Düğün.com Renk Paleti ve Tema */
:global(body) {
  background-color: #f7f8fa;
  margin: 0;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2.5rem 1rem;
  font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #2b2b2b;
}

.header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.header h1 {
  font-size: 2.4rem;
  color: #e8006f;
  font-weight: 800;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.header p {
  color: #666;
  font-size: 1.05rem;
}

.filter-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.8rem;
  display: flex;
  gap: 1.2rem;
  flex-wrap: wrap;
  align-items: flex-end;
  box-shadow: 0 10px 25px rgba(232, 0, 111, 0.08);
  border: 1px solid #f0e6eb;
  margin-bottom: 2.5rem;
}

.filter-group {
  flex: 1;
  min-width: 200px;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.85rem;
  font-weight: 700;
  color: #4a4a4a;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

select, input {
  padding: 0.75rem 1rem;
  border: 1.5px solid #e1e4e8;
  border-radius: 10px;
  font-size: 0.95rem;
  outline: none;
  background-color: #fafafa;
  transition: all 0.2s ease;
  color: #333;
}

select:focus, input:focus {
  border-color: #e8006f;
  background-color: #fff;
  box-shadow: 0 0 0 4px rgba(232, 0, 111, 0.15);
}

.btn-reset {
  padding: 0.75rem 1.2rem;
  background: #fff;
  color: #e8006f;
  border: 1.5px solid #e8006f;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 700;
  transition: all 0.2s ease;
}

.btn-reset:hover {
  background: #e8006f;
  color: #fff;
}

.photographer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 1.8rem;
}

.card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.8rem;
  position: relative;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
  border: 1px solid #f0f0f0;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(232, 0, 111, 0.12);
  border-color: #fcddec;
}

.card-badge {
  position: absolute;
  top: 1.2rem;
  right: 1.2rem;
  background: #fde8f2;
  color: #e8006f;
  font-size: 0.8rem;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-weight: 700;
}

.card h3 {
  margin: 0.5rem 0 0.4rem 0;
  font-size: 1.3rem;
  color: #1e293b;
  font-weight: 700;
}

.city {
  color: #718096;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

.price {
  margin-top: auto;
  font-size: 1.4rem;
  font-weight: 800;
  color: #e8006f;
}

.state-msg, .no-result {
  text-align: center;
  padding: 3rem;
  color: #718096;
  font-size: 1.1rem;
  font-weight: 600;
}

.error {
  color: #e53e3e;
}
</style>