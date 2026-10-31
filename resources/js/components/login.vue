<template>
  <!-- Login Content -->
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div
                    v-if="this.$route.query.result != ''"
                    class="text-success"
                  >
                    {{ this.$route.query.result }}
                  </div>
                  <h4>Hello! login to Proceed</h4>
                  <form class="user" @submit.prevent="login">
                    <div class="form-group">
                      <input
                        type="email"
                        class="form-control"
                        v-model="user.email"
                        required
                        id="exampleInputEmail"
                        aria-describedby="emailHelp"
                        placeholder="Email Address"
                      />
                    </div>


                    <div class="input-group mb-3">
                      <input
                        v-if="showPassword"
                        type="text"
                        class="form-control"
                        v-model="user.password"
                        placeholder="Password"
                      />
                      <input
                        v-else
                        type="password"
                        class="form-control"
                        v-model="user.password"
                        placeholder="Password"
                      />

                      <div class="input-group-append">
                        <button type="button" class="btn" @click="toggleShow">
                          <i
                            class="fas"
                            :class="{
                              'fa-eye-slash': showPassword,
                              'fa-eye': !showPassword,
                            }"
                          ></i>
                        </button>
                      </div>
                    </div>


                    <!-- <div class="form-group">
                      <input
                        type="password"
                        v-model="user.password"
                        required
                        class="form-control"
                        id="exampleInputPassword"
                        placeholder="Password"
                      />
                    </div> -->

                        <div class="checkbox">
                        <div class="mycheckbox"><input
                            type="checkbox"
                            v-model="user.remember_me"
                            true-value="true"
                            false-value="false"
                            aria-describedby="emailHelp"
                          /></div>
                        <div class="rem">Remember Me</div>
                        </div>


                    <div v-if="errorflag != ''" class="text-danger">
                      {{ errorflag }}
                    </div>

                    <div class="form-group">
                      <input
                        type="submit"
                        :disabled="processing"
                        class="btn btn-primary btn-block"
                        value="Login"
                      />
                    </div>
                  </form>
                  <div class="text-center">
                    <a class="font-weight-bold small" href="register"
                      >Create an Account!</a
                    >
                  </div>
                  <div class="text-center">
                    <router-link
                      :to="{ name: 'forgotpassword' }"
                      class="font-weight-bold small"
                      >Forgot Your Password?</router-link
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Login Content -->
</template>

<script>
import Auth from "../Auth.js";

export default {
  data() {
    return {
      user: {
        email: "",
        password: "",
        remember_me: false,
      },
      processing: false,
      errorflag: "",
      showPassword: false,
    };
  },

  computed: {
    buttonLabel() {
      return this.showPassword ? "Hide" : "Show";
    },
  },

  methods: {
    toggleShow() {
      this.showPassword = !this.showPassword;
    },
    login() {
      this.processing = true;
    //   console.log(user.remember_me)

      if (this.user.email == "") {
        this.processing = false;
        this.errorflag = "Email cannot be empty";
        return;
      }
      if (this.user.password == "") {
        this.processing = false;
        this.errorflag = "Password cannot be empty";
        return;
      }

      this.axios
        .post(`/api/login`, this.user)
        .then(({ data }) => {
          Auth.login(data.data.token, data.data); //set local storage
          this.processing = false;
          window.location.href = "/dashboard";
          // this.$router.go("/dashboard");
        })
        .catch((error) => {
          this.processing = false;

          // if (error.response.status === 404) {
          this.errorflag = error.response.data.message;
          //}

          // console.log(error.response.status);
        });
      // });
    },
  },
};
</script>
<style scoped>
.mycheckbox {
    padding-right: 10px;
}

.checkbox {
    display: flex;
    flex-direction: row;
    justify-items: flex-start;
    justify-content: flex-start;
}
.rem {
    padding-top: 8px;
}

</style>
