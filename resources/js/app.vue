<template>
  <router-view v-if="canRenderRoute"> </router-view>
</template>

<script>
import Auth from "./Auth.js";
export default {
  data() {
    return {
      loggedUser: this.auth.user,
    };
  },
  computed: {
    canRenderRoute() {
      const protectedRoute = this.$route.matched.some(record => record.meta.requiresAuth || record.meta.requiresAdmin);
      return !protectedRoute || Boolean(this.auth.user);
    },
  },
  methods: {
    logout() {
      this.axios
        .post("/api/logout")
        .then(({ data }) => {
          Auth.logout(); //reset local storage
          this.$router.push("/login");
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
