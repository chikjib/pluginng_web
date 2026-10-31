<template>
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <h4>Hello! let's get started</h4>
                  <h6 class="fw-light">Start by creating an account.</h6>
                  <form class="pt-3" method="POST" action="javascript:void(0)">
                    <div v-if="successflag != ''" class="text-success">
                      {{ successflag }}
                    </div>
                    <div class="form-group">
                      <input
                        type="text"
                        id="firstname"
                        class="form-control form-control-lg"
                        v-model="user.firstname"
                        name="name"
                        required
                        autocomplete="firstname"
                        autofocus
                        placeholder="First Name"
                      />
                    </div>
                    <div class="form-group">
                      <input
                        type="text"
                        id="name"
                        class="form-control form-control-lg"
                        v-model="user.lastname"
                        name="lastname"
                        required
                        autocomplete="lastname"
                        autofocus
                        placeholder="Last Name"
                      />
                    </div>
                    <div class="form-group">
                      <input
                        type="email"
                        id="email"
                        class="form-control form-control-lg"
                        v-model="user.email"
                        name="email"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="Email Address"
                      />
                    </div>

                    <div class="form-group">
                      <input
                        type="text"
                        id="phone"
                        class="form-control form-control-lg"
                        v-model="user.phone"
                        name="phone"
                        required
                        autocomplete="phone"
                        autofocus
                        placeholder="Phone Number"
                      />
                    </div>
                    <div class="form-group">
                      <input
                        type="password"
                        class="form-control form-control-lg"
                        v-model="user.password"
                        required
                        placeholder="Password"
                      />
                    </div>

                    <div class="form-group">
                      <input
                        type="password"
                        class="form-control form-control-lg"
                        v-model="user.c_password"
                        required
                        placeholder="Confirm Password"
                      />
                    </div>

                    <div class="form-group">
                      <label>How did you hear about us?</label>
                      <select v-model="user.hear_about_us" class="form-control">
                        <option value="" selected disabled>
                          How did you hear about us?
                        </option>
                        <option value="Facebook">Facebook</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Twitter">Twitter</option>
                        <option value="Google">Google</option>
                        <option value="From a friend">From a friend</option>
                      </select>
                    </div>

                    <p>
                      By clicking the registration button, you agree to our
                      <a href="/terms"> Terms and Conditions </a> and
                      <a href="/privacy"> Privacy Policy </a>
                    </p>

                    <div class="mt-3">
                      <input
                        type="submit"
                        :disabled="processing"
                        @click="register()"
                        class="
                          btn btn-block btn-primary btn-lg
                          font-weight-medium
                          auth-form-btn
                        "
                        value="SIGN UP"
                      />
                    </div>
                    <div v-if="errorflag != ''" class="text-danger">
                      {{ errorflag }}
                    </div>

                    <div
                      class="
                        my-2
                        d-flex
                        justify-content-between
                        align-items-center
                      "
                    >
                      <router-link
                        :to="{ name: 'Login' }"
                        class="font-weight-bold small"
                        >Already have an Account?</router-link
                      >
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        name: "",
        email: "",
        password: "",
        c_password: "",
        phone: "",
        //role: 0,
        hear_about_us: "Facebook",
        referral_id: 0,
      },
      processing: false,
      errorflag: "",
      successflag: "",
    };
  },

  methods: {
    register() {
      this.processing = true;

      this.user.referral_id =
        this.$route.query.ref > 0 ? this.$route.query.ref : 0;
      this.axios
        .post(`/api/register`, this.user)
        .then(({ data }) => {
          this.processing = false;

          this.successflag =
            "Registration successful. A verification link has been sent to your email";
          this.$toasted.show(
            "Registration successful. A verification link has been sent to your email"
          );

          this.user.firstname = "";
          this.user.email = "";
          this.user.phone = "";
          this.user.password = "";
          this.user.c_password = "";

          this.$router.push(
            "/login?result=Registration successful. A verification link has been sent to your email"
          );
        })
        .catch((error) => {
          this.processing = false;
          if (error.response.status != 200) {
            this.errorflag = error.response.data.message;
          }
          this.$toasted.show(error.response.data.message);
        });
    },
  },
};
</script>