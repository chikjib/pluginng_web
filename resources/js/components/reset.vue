<template>
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <h4>Hello! reset your password here</h4>
                  <h6 class="fw-light">Enter your new password</h6>
                  <form class="pt-3" method="POST" action="javascript:void(0)">
                    <div class="form-group">
                      <input
                        type="password"
                        id="password"
                        class="form-control form-control-lg"
                        v-model="user.password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                      />
                    </div>

                    <div class="form-group">
                      <input
                        type="password"
                        id="password_confirmation"
                        class="form-control form-control-lg"
                        v-model="user.password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="confirm-password"
                        placeholder="Confirm Password"
                      />
                    </div>

                    <div class="mt-3">
                      <input
                        type="submit"
                        :disabled="processing"
                        @click="submitQuery()"
                        class="
                          btn btn-block btn-primary btn-lg
                          font-weight-medium
                          auth-form-btn
                        "
                        value="Reset"
                      />
                    </div>
                    <div v-if="errorflag != ''" class="text-danger">
                      {{ errorflag }}
                    </div>
                    <div v-if="successflag != ''" class="text-success">
                      {{ successflag }}
                    </div>
                    <div
                      class="
                        my-2
                        d-flex
                        justify-content-between
                        align-items-center
                      "
                    ></div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        password: "",
        email: "",
        code: "",
      },
      processing: false,
      errorflag: "",
      successflag: "",
    };
  },

  methods: {
    submitQuery() {
      this.processing = true;
      this.user.email = window.localStorage.getItem("email");
      this.user.code = window.localStorage.getItem("code");
      this.axios
        .post(`/api/auth/password/reset`, this.user)
        .then(({ data }) => {
          this.processing = false;
          this.$toasted.show(data.data.message);

          window.localStorage.removeItem("email");
          window.localStorage.removeItem("code");
          this.$router.push("/login");
        })
        .catch((error) => {
          // this.processing = false;
          // this.$toasted.show(error.response.data.data);
          // console.log(error);
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