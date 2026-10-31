<template>
  <div id="page-top">
    <div id="wrapper">
      <Sidebar></Sidebar>

      <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
          <Header></Header>

          <!-- Container Fluid-->
          <div class="container-fluid" id="container-wrapper">
            <div
              class="d-sm-flex align-items-center justify-content-between mb-4"
            >
              <h1 class="h3 mb-0 text-gray-800">
                {{ this.$route.meta.title }}
              </h1>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                  {{ this.$route.meta.title }}
                </li>
              </ol>
            </div>

            <router-view v-slot="{ Component }">
              <Transition name="slide-fade" mode="out-in">
                <component :is="Component" />
              </Transition>
            </router-view>
            <v-idle
              @idle="onidle"
              style="color: white"
              :wait="5"
              :duration="300"
            />
          </div>
          <!---Container Fluid-->
        </div>
        <Footer></Footer>
      </div>
    </div>
  </div>
</template>



<script>
import Header from "./header.vue";
import { initializeDashboardTheme } from "../../dashboardTheme.js";
import Footer from "./footer.vue";
import Sidebar from "./sidebar.vue";
export default {
  components: {
    Header,
    Footer,
    Sidebar,
  },
  data() {
    return {
      events: ["click", "mousemove", "mousedown", "scroll", "keypress", "load"],
      // warningTimer: null,
      logoutTimer: null,
      cleanupTheme: null,
      // warningZone: false,
    };
  },
  mounted() {
    this.cleanupTheme = initializeDashboardTheme();
    this.events.forEach(function (event) {
      window.addEventListener(event, this.resetTimer);
    }, this);
    this.setTimers();
  },
  unmounted() {
    this.cleanupTheme?.();
    this.events.forEach(function (event) {
      window.removeEventListener(event, this.resetTimer);
    }, this);
    clearTimeout(this.logoutTimer);
  },

  //   created() {
  //     this.autoLogout();
  //   },

  methods: {
    setTimers() {
      var remember_token = window.localStorage.getItem("remember_token");
      //   console.log(remember_token);
      // console.log(remember_token == "null");
      // this.warningTimer = setTimeout(this.warningMessage, import.meta.env.VITE_SESSION_LIFETIME || 120 -1 * 60 * 1000); // 14 minutes - 14 * 60 * 1000

      if (remember_token != "null") {
        // console.log("it is here");
        this.logoutTimer = setTimeout(
          this.logout,
          (Number(import.meta.env.VITE_SESSION_LIFETIME) || 120) * 60 * 1000
        ); // 15 minutes - 15 * 60 * 1000
      } else {
        // console.log("it is here 2");
        this.logoutTimer = setTimeout(this.logout, 400 * 60 * 1000); // 15 minutes - 15 * 60 * 1000
      }
    },
    resetTimer() {
    //   clearTimeout(this.warningTimer);
      clearTimeout(this.logoutTimer);
      this.setTimers();
    },
    logout() {
      this.axios
        .post(`/api/logout`)
        .then(({ data }) => {
          this.auth.logout(); //reset local storage
          // this.$router.push("/login");
          window.location.href = "/login";
        })
        .catch((error) => {
          console.log(error);
        });
    },

    onidle() {
      this.logout();
    },
    onremind(time) {
      // alert seconds remaining to 00:00
      alert("You will be logged out in " + time + " seconds");
    },
  },
};
</script>

<style>
.sidebar .nav-item .router-link-exact-active {
  background: #fff;
  position: relative;
  font-weight: bold;
  font-size: 13px;
}

/*
  Enter and leave animations can use different
  durations and timing functions.
*/
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>
