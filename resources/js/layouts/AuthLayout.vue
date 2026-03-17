<template>
  <v-app>
    <v-main>

      <div class="login-container">
        <div class="background-slider">
          <transition name="fade">
            <div :key="currentSlide" class="slide" :style="{ backgroundImage: `url(${slides[currentSlide].image})` }" />
          </transition>
        </div>
        <div class="left-content d-none d-md-flex">
          <transition name="fade" mode="out-in">
            <div :key="currentSlide">
              <h1 class="title">{{ slides[currentSlide].title }}</h1>
              <p class="my-6 subtitle">{{ slides[currentSlide].text }}</p>
              <v-btn v-if="slides[currentSlide].buton" :href="slides[currentSlide].url" color="primary" rounded
                size="large" type="submit">
                {{ slides[currentSlide].buton }}
              </v-btn>
            </div>
          </transition>
        </div>


        <div class="login-box">
          <v-card class="pa-6 login-card">

            <!-- <h2 class="text-h5 font-weight-bold mb-6 text-center">
              Iniciar sesión
            </h2> -->
            <v-card-title class="text-h5 justify-items-center pb-8">
              <img :src="imagen" width="200px" class="team-img" />
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="submitLogin">
                <v-text-field v-model="username" :rules="emailRules" label="Correo Electrónico / Usuario" class="mt-1 mb-4"
                  required density="comfortable" hide-details="auto" variant="outlined" color="primary" rounded />

                <v-text-field v-model="password" :rules="passwordRules" label="Contraseña" required
                  density="comfortable" variant="outlined" color="primary" hide-details="auto" rounded
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'" @click:append-inner="showPassword = !showPassword"
                  class="mb-4" />

                <v-btn color="primary" block rounded size="large" type="submit">
                  Ingresar
                </v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </div>


      </div>

    </v-main>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from "vue"

const email = ref("")
const emailRules = ref([
  v => !!v || 'Se requiere usuario o correo electrónico',
  //v => (v && v.length <= 10) || 'Password must be less than 10 characters'
]);
const password = ref("")
const showPassword = ref(false)
const passwordRules = ref([
  v => !!v || 'Se requiere contraseña',
  //v => (v && v.length <= 10) || 'Password must be less than 10 characters'
]);

const currentSlide = ref(0)

const slides = [
  {
    title: "Asesoría legal cercana y estratégica",
    text: "Ofrecemos soluciones viables y seguras a través de un servicio integral, personalizado y guiado por nuestros valores: comunicación, compromiso, resolución y colaboración.",
    image: "https://gamarrafirma.com/wp-content/uploads/2025/09/BANNER-1.webp",
    buton: "Conoce más",
    url: "https://gamarrafirma.com/nosotros/"
  },
  {
    title: "Un equipo legal sólido a tu servicio",
    text: "Nuestro equipo trabaja de forma articulada para brindar soluciones legales integrales, ágiles y estratégicas.",
    image: "https://gamarrafirma.com/wp-content/uploads/2025/09/BANNER-3-1.webp",
    buton: "Leer más",
    url: "https://gamarrafirma.com/equipo/"
  },
  {
    title: "Respaldo internacional ",
    text: "Contamos con el respaldo de Lexincorp, una de las firmas más reconocidas en Centroamérica, con presencia en Guatemala, El Salvador, Honduras, Nicaragua y Costa Rica. Esta alianza potencia nuestra capacidad para atender operaciones legales transfronterizas con el mismo estándar de calidad.",
    image: "https://gamarrafirma.com/wp-content/uploads/2025/08/BANNER-LEX.webp"
  }
]

const imagen = ref('https://sisega.gamarrafirma.com/img/login.png')

onMounted(() => {
  setInterval(() => {
    currentSlide.value =
      (currentSlide.value + 1) % slides.length
  }, 6000)
})

function submitLogin() {
  console.log(email.value, password.value)
}
</script>

<style scoped>
/* CONTENEDOR */
.login-container {
  position: relative;
  height: 100vh;
  width: 100%;
  overflow: hidden;
}

/* 🔵 BACKGROUND */
.background-slider {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.slide {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 8s ease;
}

/* overlay oscuro */
.background-slider::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
}

/* 🟣 TEXTO */
.left-content {
  position: absolute;
  left: 8%;
  top: 50%;
  transform: translateY(-50%);
  max-width: 520px;
  /* max-width: 450px; */
  color: white;
  z-index: 2;
}

.title {
  font-size: 45px;
  font-weight: 700;
  line-height: 1.1;
}

.subtitle {
  font-size: 20px;
  opacity: .9;
}

/* ⚪ LOGIN */
.login-box {
  position: absolute;
  right: 8%;
  top: 50%;
  transform: translateY(-50%);
  z-index: 2;
}

.login-card {
  width: 380px;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(10px);
}

/* ✨ ANIMACIONES */
.fade-enter-active,
.fade-leave-active {
  transition: opacity .8s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* 🟡 TABLET */
@media (max-width: 1024px) {
  .left-content {
    left: 5%;
    max-width: 350px;
  }

  .title {
    font-size: 32px;
  }

  .login-box {
    right: 5%;
  }
}

/* 🔵 MÓVIL */
@media (max-width: 768px) {

  .left-content {
    display: none !important;
  }

  .login-box {
    left: 50%;
    right: auto;
    transform: translate(-50%, -50%);
    width: 100%;
    padding: 0 16px;
  }

  .login-card {
    width: 100%;
  }
}
</style>