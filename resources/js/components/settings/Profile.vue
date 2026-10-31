<template>
  <div>
    <div class="row">
        <div class="col-xl-6 col-lg-6">
           <div class="card mb-6">
            <div class="card-body">
                <h4 class="card-title">Profile Info</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td>Name</td>
                        <td>{{ users.firstname + "  " + users.lastname }}</td>
                    </tr>
                    <tr>
                        <td>Wallet</td>
                        <td>&#8358; {{ users.wallet }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ users.email }}</td>
                    </tr>

                    <tr>
                        <td>Phone</td>
                        <td>{{ users.phone }}</td>
                    </tr>

                    <tr>
                        <td>Join Date</td>
                        <td>{{ users.created_at }}</td>
                    </tr>

                    <tr>
                        <td>Referral Link</td>
                        <td>
                        <input
                            type="text"
                            v-model="reflink"
                            class="form-control"
                            readonly
                        />
                        </td>
                    </tr>

                    <!-- <tr>
                                    <td>API KEY</td>
                                    <td v-if="users.userToken == null">
                                        <a
                                        class="btn btn-primary"
                                        @click.stop="generateApiKey()"
                                        href="#"
                                        >Generate Key</a
                                        >
                                    </td>
                                    <td v-else>
                                        {{ users.userToken }}
                                    </td>
                                    </tr> -->
                    </table>
                </div>
            </div>

          </div>
        </div>
        <div class="col-xl-6 col-lg-6">
             <div class="card mb-6">
                <div class="card-body">
                      <h4 class="card-title">Form of Identification</h4>
          <hr />
          <div v-if="users.bvn == null">
            <p>
              <input
                type="radio"
                id="bvn"
                name="id-select"
                value="bvn"
                v-model="picked"
              />
              Bvn Number
            </p>
            <div v-if="picked == 'bvn'" class="bvn_style">
              <div
                class="notification alert alert-success"
                v-if="bvn_success_flag"
              >
                <span>{{ bvn_success_flag }}</span>
              </div>
              <div
                class="notification alert alert-danger"
                v-if="bvn_errors.length"
              >
                <span v-for="bvn_error in bvn_errors" :key="bvn_error.key">{{
                  bvn_error
                }}</span>
              </div>

              <form @submit.prevent="submitBvn">
                <div class="form-group">
                  <label>BVN number</label>
                  <input
                    type="number"
                    name="bvn"
                    id="bvn"
                    v-model="bvn"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>Full Name (as it appears on your bvn)</label>
                  <input
                    type="text"
                    name="name"
                    id="name"
                    v-model="bvn_name"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <button
                    v-if="isLoading"
                    disabled
                    id="bvn"
                    class="btn btn-primary"
                  >
                    Processing...
                  </button>
                  <button v-else type="submit" id="bvn" class="btn btn-primary">
                    Submit
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div v-else>
            <p class="text-success">BVN Verified!</p>
          </div>

          <div v-if="users.nin == null">
            <p>
              <input
                type="radio"
                id="nin"
                name="id-select"
                value="nin"
                v-model="picked"
              />
              NIN Number
            </p>
            <div v-if="picked == 'nin'" class="nin_style">
              <div
                class="notification alert alert-success"
                v-if="nin_success_flag"
              >
                <span>{{ nin_success_flag }}</span>
              </div>
              <div
                class="notification alert alert-danger"
                v-if="nin_errors.length"
              >
                <span
                  v-for="nin_error in nin_errors"
                  v-bind:key="nin_error.key"
                  >{{ nin_error }}</span
                >
              </div>

              <form @submit.prevent="submitNin">
                <div class="form-group">
                  <label>NIN number</label>
                  <input
                    type="number"
                    name="nin"
                    id="nin"
                    v-model="nin"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <div class="form-group">
                    <button
                      v-if="isLoading"
                      disabled
                      id="bvn"
                      class="btn btn-primary"
                    >
                      Processing...
                    </button>
                    <button
                      v-else
                      type="submit"
                      id="bvn"
                      class="btn btn-primary"
                    >
                      Submit
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div v-else>
            <p class="text-success">Nin Verified!</p>
          </div>
                </div>

          </div>
        </div>

    </div>

    <div class="row m">
        <div class="col-xl-12 col-lg-12">
            <div class="card mb-12">

                <div class="card-body">
                    <h4 class="card-title">Webhook Url</h4>
                    <form @submit.prevent="updateWebhook" class="forms-sample">
                        <div class="form-group">
                        <div class="col-sm-12">
                            <input
                            required
                            type="text"
                            v-model="users.webhook_url"
                            class="form-control"
                            placeholder="Your Webhook Url"
                            />
                        </div>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">
                        Submit
                        </button>
                    </form>
                </div>


            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card mb-6">
                <div class="card-body">
                    <h4 class="card-title">Change Password</h4>
                    <form @submit.prevent="changePassword" class="forms-sample">
                        <div class="form-group">
                        <div class="col-sm-12">
                            <input
                            required
                            type="password"
                            v-model="form.old_password"
                            class="form-control"
                            placeholder="Old Password"
                            />
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="col-sm-12">
                            <input
                            required
                            type="password"
                            v-model="form.new_password"
                            class="form-control"
                            placeholder="New Password"
                            />
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="col-sm-12">
                            <input
                            required
                            type="password"
                            v-model="form.confirm_password"
                            class="form-control"
                            placeholder="Confirm Password"
                            />
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">
                        Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
           <div class="card mb-6">
            <div class="card-body">
                <h4 v-if="users.bank_name != null" class="card-title">
                Update Profile
                </h4>
                <h4 v-else class="card-title">Update Profile</h4>
                <form @submit.prevent="addBank" class="forms-sample">
                    <div class="form-group">
                    <div class="col-sm-12">
                        <input
                        required
                        type="text"
                        v-model="users.phone"
                        class="form-control"
                        placeholder="Change Phone"
                        />
                    </div>
                    </div>

                    <div class="form-group">
                    <div class="col-sm-12">
                        <input
                        required
                        type="text"
                        v-model="users.bank_name"
                        class="form-control"
                        placeholder="Bank Name"
                        />
                    </div>
                    </div>
                    <div class="form-group">
                    <div class="col-sm-12">
                        <input
                        required
                        type="text"
                        v-model="users.account_number"
                        class="form-control"
                        placeholder="Account Number"
                        />
                    </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-2">
                    Submit
                    </button>
                </form>
            </div>


            </div>
        </div>
    </div>
  </div>
</template>

<script>
const token = window.localStorage.getItem("token");

export default {
  data() {
    return {
      users: {},
      picked: "",
      bvn_errors: [],
      bvn_success_flag: "",
      nin_errors: [],
      nin_success_flag: "",
      bvn: "",
      bvn_name: "",
      nin: "",
      isLoading: false,
      form: {
        old_password: null,
        new_password: null,
        confirm_password: null,
        bank_name: null,
        phone: "",
        account_number: null,
        webhook_url: null,
      },

      upload: {
        user_image: null,
      },

      reflink: this.$appUrl + "/register?ref=" + this.auth.user.id,

      userdata: this.auth.user,

      activeModal: 0,
    };
  },

  mounted() {
    this.getusers();
    // this.getProd();
    this.get_avatar();
  },

  methods: {
    showModal: function (id) {
      return this.activeModal === id;
    },
    toggleModal: function (id) {
      if (this.activeModal !== 0) {
        this.activeModal = 0;
        return false;
      }
      this.activeModal = id;
    },

    get_avatar() {
      let photo =
        this.users.user_image == null
          ? "/template/images/faces/noimage.png"
          : this.users.user_image;
      return photo;
    },

    getusers() {
      axios
        .get(`/api/users/${this.userdata.id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data.data);
          this.users = response.data.data;
        });
    },

    generateApiKey() {
      axios
        .get(`/api/generate/api/key`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          // console.log(response.data.data);
          // this.form.token = response.data.data.userToken;
          window.location.reload();
        });
    },

    onChange(e) {
      this.upload.user_image = e.target.files[0];
    },
    formSubmit(e) {
      e.preventDefault();
      let existingObj = this;
      const config = {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      };
      let data = new FormData();
      data.append("user_image", this.upload.user_image);
      // console.log(this.upload.user_image);
      axios
        .post(`/api/users/uploadimage/${this.userdata.id}`, data, config)
        .then(function (res) {
          // existingObj.success = res.data.success;
          // console.log(res.data.message);
          //this.$toasted.show(res.data.message);
          //this.$router.push({ name: "users" });
          window.location.reload();
        })
        .catch(function (err) {
          //existingObj.output = err;
          this.$toasted.show(error.response.data.data);
        });
    },

    onChange22(e) {
      this.upload.user_image = e.target.files[0];
      axios
        .put(`/api/users/${this.userdata.id}`, this.upload, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data);
          // this.close();
          //  this.users.push(this.user);
          this.$toasted.show(response.data.data.message);
          //this.$router.push({ name: "users" });
          window.location.reload();
        })
        .catch((error) => {
          console.log(error);
          this.$toasted.show(error.response.data.data);
        });
    },

    changePassword() {
      axios
        .post(`/api/change-password/`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data);
          // this.close();
          //  this.users.push(this.user);
          this.$toasted.show(response.data.data.message);
          //this.$router.push({ name: "users" });
          window.location.reload();
        })
        .catch((error) => {
          console.log(error);
          this.$toasted.show(error.response.data.data);
        });
    },

    addBank() {
      this.form.phone = this.users.phone;
      this.form.bank_name = this.users.bank_name;
      this.form.account_number = this.users.account_number;

      axios
        .put(`/api/users/${this.userdata.id}`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          //console.log(response.data);
          // this.close();
          //  this.users.push(this.user);
          this.$toasted.show(response.data.data.message);
          //this.$router.push({ name: "users" });
          window.location.reload();
        })
        .catch((error) => {
          console.log(error);
          this.$toasted.show(error.response.data.data);
        });
    },

    updateWebhook() {
      this.form.webhook_url = this.users.webhook_url;


      axios
        .put(`/api/update-webhook`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response);
          // this.close();
          //  this.users.push(this.user);
          this.$toasted.show(response.data.data.message);
          alert("Webhook url updated successfully");
          //this.$router.push({ name: "users" });
          window.location.reload();
        })
        .catch((error) => {
          console.log(error);
          this.$toasted.show(error.response.data.data);
        });
    },

    submitBvn() {
      this.bvn_errors = [];
      this.bvn_success_flag = "";
      const new_bvn_dob = "3-Jun-1994";
      if (this.bvn == "") {
        this.bvn_errors.push("BVN Number is required");
      } else if (this.bvn.length < 11 || this.bvn.length > 11) {
        this.bvn_errors.push("BVN Number must be exactly 11 digits");
      } else if (this.bvn_name == "") {
        this.bvn_errors.push("Name is required");
      } else {
        this.isLoading = true;
        const formData = {
          bvn: this.bvn,
          name: this.bvn_name,
          dob: new_bvn_dob,
          phone: this.users.phone,
        };

        axios
          .post(`/api/update/profile/bvn/`, formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          })
          .then((response) => {
            console.log(response.data.data.message);
            // this.close();
            //  this.users.push(this.user);
            this.bvn_success_flag = response.data.message;
            this.$toasted.show(response.data.message);
            this.isLoading = false;
            alert("Bvn verified successfully");

            //this.$router.push({ name: "users" });
            window.location.reload();
          })
          .catch((error) => {
            console.log(error);
            this.bvn_errors = error.response.data.data;
            this.$toasted.show(error.response.data.data);
            this.isLoading = false;
          });

        console.log(formData);
      }
    },
    submitNin() {
      this.nin_errors = [];
      if (this.nin == "") {
        this.nin_errors.push("NIN Number is required");
      } else if (this.nin.length < 11 || this.nin.length > 11) {
        this.nin_errors.push("NIN Number must be exactly 11 digits");
      } else {
        this.isLoading = true;
        const formData = {
          nin: this.nin,
        };
        axios
          .post(`/api/update/profile/nin/`, formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          })
          .then((response) => {
            console.log(response.data.data.message);
            // this.close();
            //  this.users.push(this.user);
            this.nin_success_flag = response.data.message;
            this.$toasted.show(response.data.message);
            this.isLoading = false;
            //this.$router.push({ name: "users" });
            // window.location.reload();
            alert("Nin verified successfully");

            //this.$router.push({ name: "users" });
            window.location.reload();
          })
          .catch((error) => {
            console.log(error);
            this.nin_errors = error.response.data.data;
            this.$toasted.show(error.response.data.data);
            this.isLoading = false;
          });

        console.log(formData);
      }
    },
  },
};
</script>
<style scoped>
.m {
    margin-top: 20px;
    margin-bottom: 20px;
  }
</style>
