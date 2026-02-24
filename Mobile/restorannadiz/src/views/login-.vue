<template>
  <ion-page>
    <ion-content :fullscreen="true" class="login-content">
      
      <div class="login-container">
        
        <div class="header-section ion-text-center ion-margin-bottom">
          <div class="logo-circle">
            <ion-icon :icon="restaurant" size="large" color="light"></ion-icon>
          </div>
          <h1 class="ion-text-bold">Resto App</h1>
          <p class="ion-text-medium">Silakan masuk untuk melanjutkan</p>
        </div>

        <div class="form-section">
          
          <ion-input
            v-model="username"
            label="Username"
            label-placement="floating"
            fill="outline"
            placeholder="Masukkan username"
            class="custom-input ion-margin-bottom"
          >
            <ion-icon slot="start" :icon="personOutline" aria-hidden="true"></ion-icon>
          </ion-input>

          <ion-input
            v-model="password"
            label="Password"
            label-placement="floating"
            fill="outline"
            type="password"
            placeholder="Masukkan password"
            class="custom-input ion-margin-bottom"
            @keyup.enter="login" 
          >
            <ion-icon slot="start" :icon="lockClosedOutline" aria-hidden="true"></ion-icon>
          </ion-input>

          <div v-if="error" class="error-box ion-padding-vertical">
            <ion-text color="danger">
              <small>
                <ion-icon :icon="alertCircleOutline" style="vertical-align: middle"></ion-icon>
                Username atau password salah
              </small>
            </ion-text>
          </div>

          <ion-button
            expand="block"
            size="large"
            class="login-btn"
            :disabled="loading"
            @click="login"
          >
            <span v-if="!loading">Masuk</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>
          
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import {
  IonPage,
  IonContent,
  IonInput,
  IonButton,
  IonText,
  IonIcon,
  IonSpinner
} from '@ionic/vue'
import { 
  personOutline, 
  lockClosedOutline, 
  restaurant, 
  alertCircleOutline 
} from 'ionicons/icons'

import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(false)

const login = async () => {
  if(!username.value || !password.value) {
    error.value = true;
    return;
  }

  error.value = false
  loading.value = true

  try {
    const res = await fetch('http://localhost/api-restoran/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username: username.value,
        password: password.value
      })
    })

    const result = await res.json()

    if (result.status === 'success') {
      localStorage.setItem('user', JSON.stringify(result.data))
      router.replace('/home-login')
    } else {
      error.value = true
    }
  } catch (e) {
    console.error(e)
    error.value = true
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-content {
  --background: #f4f5f8; 
  --ion-text-color: #333333;
  --color: #333333;
}

.login-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 100%;
  padding: 30px;
  max-width: 500px;
  margin: 0 auto;
}

.logo-circle {
  width: 80px;
  height: 80px;
  background: var(--ion-color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 4px 10px rgba(var(--ion-color-primary-rgb), 0.3);
}

.header-section h1 {
  font-size: 28px;
  margin-bottom: 5px;
  font-weight: 700;
  color: #222222;
}

.header-section p {
  color: #666666;
  margin-top: 0;
}

.custom-input {
  --border-radius: 12px;
  --background: #ffffff;
  --color: #000000;
  --placeholder-color: #888888;
  --border-color: #e0e0e0;
}

.login-btn {
  margin-top: 30px;
  --border-radius: 12px;
  --box-shadow: 0 4px 12px rgba(var(--ion-color-primary-rgb), 0.4);
  font-weight: 600;
}

.error-box {
  text-align: center;
  animation: shake 0.5s;
}

.error-box ion-text {
  color: var(--ion-color-danger, #eb445a) !important;
}

@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
</style>