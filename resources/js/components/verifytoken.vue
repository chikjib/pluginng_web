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
                  <h4>Hello! Verify your token?</h4>
                  <h6 class="fw-light">Enter the token sent to your email</h6>
                  <form class="pt-3" method="POST" action="javascript:void(0)">
                    <div v-if="successflag != ''" class="text-success">
                      {{ successflag }}
                    </div>
                    <div class="form-group">
                      <input
                        type="text"
                        class="form-control form-control-lg"
                        v-model="user.token"
                        required
                        autofocus
                        placeholder="Enter Code"
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
                        value="Submit"
                      />
                    </div>
                    <div v-if="errorflag != null" class="text-danger">
                      {{ errorflag }}
                    </div>
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
        token: "",
      },
      processing: false,
      errorflag: "",
      successflag: "",
    };
  },

  methods: {
    submitQuery() {
      this.processing = true;

      if (this.user.token == "") {
        this.processing = false;
        this.errorflag = "Token cannot be empty";
        return;
      }

      this.axios
        .post(`/api/auth/token/verify`, this.user)
        .then(({ data }) => {
          this.processing = false;
          this.successflag = data.data.message;
          window.localStorage.setItem("code", this.user.token);
          this.$toasted.show(data.data.message);

          this.$router.push("/reset");
        })
        .catch((error) => {
          this.processing = false;
          if (error.response.status != 200) {
            this.errorflag = error.response.data.message;
          }
          this.$toasted.show(error.response.data.message);
          //console.log(error);
        });
    },
  },
};
</script>