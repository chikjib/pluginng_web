<template>
  <div class="col-xl-8 col-lg-7">
    <div class="card mb-4">
      <div
        class="
          card-header
          py-3
          d-flex
          flex-row
          align-items-center
          justify-content-between
        "
      >
        <div class="col-md-12">
          <div
            v-if="successflag != ''"
            class="alert alert-success alert-dismissible"
            role="alert"
          >
            <button
              type="button"
              class="close"
              data-dismiss="alert"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            {{ successflag }}
          </div>
          <div
            v-if="errorflag != ''"
            class="alert alert-danger alert-dismissible"
            role="alert"
          >
            <button
              type="button"
              class="close"
              data-dismiss="alert"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            {{ errorflag }}
          </div>
          <form @submit.prevent="buyElectricity" class="forms-sample">
            <div class="row">
              <img
                v-if="networkImage != ''"
                :src="'/template/images/services/' + networkImage"
              />
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="category">Select Provider</label>
                  <select
                    name="subcategory_id"
                    v-model="subcategory_id"
                    v-on:change="showPlans"
                    class="form-control"
                  >
                    <option>Select Provider</option>
                    <option
                      v-for="subcategories in subcategories.data"
                      :key="subcategories.title"
                      v-bind:value="subcategories.title"
                    >
                      {{ subcategories.title }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="package">Choose Package</label>
                  <select class="form-control" v-model="form.type">
                    <option value="prepaid">Prepaid</option>
                    <option value="postpaid">Postpaid</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount">Amount</label>
                  <input type="number" class="form-control" v-model="amount" />
                </div>
              </div>

              <input type="hidden" v-model="form.amount" />

              <div class="col-md-6">
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.phonenumber"
                    id="phone"
                    required
                    placeholder="phone"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="meter">Meter Number</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.cardno"
                    id="Meter"
                    required
                    placeholder="Meter Number"
                  />
                  <br />

                  <button
                    type="button"
                    :disabled="verifiying"
                    class="btn btn-info"
                    @click="verifyCard"
                  >
                    Verify
                  </button>
                  <div v-if="cardowner != ''" class="text-info">
                    {{ cardowner }}
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="amount">Charge</label>
                  <input
                    type="text"
                    class="form-control"
                    readonly
                    v-model="form.discount"
                  />
                </div>
              </div>
            </div>
            <button
              type="submit"
              :disabled="!successCode || loading"
              class="btn btn-primary me-2"
            >
              {{ loading ? "Processing.." : "Process" }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const token = window.localStorage.getItem("token");
export default {
  // props: ["user"],
  data() {
    return {
      user: this.auth.user,
      subcategories: {},
      plans: {},
      form: {},

      category_id: null,
      subcategory_id: null,
      plan_id: null,
      phonenumber: null,
      amount: null,
      discount: 0,
      loading: false,
      networkImage: "",
      cardowner: "",
      successCode: false,
      verifiying: false,

      successflag: "",
      errorflag: "",
    };
  },

  mounted() {
    this.loadplan();
  },

  watch: {
    amount(after, before) {
      this.showActualAmount();
    },
  },

  methods: {
    buyElectricity() {
      this.loading = true;

      this.form.variation_code = this.form.type;
      this.form.serviceID = this.subcategory_id;
      var plan1 = this.subcategory_id.toString().split("-");
      var plan2 = plan1[1].trim().replace(" ", "-");
      this.form.plan = plan2.toString().toLowerCase();

      if (this.amount < 500 || this.form.amount < 500) {
        this.errorflag = "Amount cannot be less than 500";
        this.$toasted.show("Amount cannot be less than 500");
        this.loading = false;
      } else {
        axios
          .post(`/api/purchase/electricity`, this.form, {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          })
          .then((response) => {
            this.successflag = response.data.message;
            this.loading = false;
            this.form.amount = "";
            this.form.cardno = "";
            this.form.phonenumber = "";
            this.$toasted.show(response.data.message);
          })
          .catch((error) => {
            this.loading = false;
            this.errorflag = error.response.data.message;
            this.$toasted.show(error.response.data.message);
          });
      }
    },

    loadplan() {
      axios
        .get(`/api/subcategory?category_id=4`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data);
          this.subcategories = response.data;
        });
    },

    showPlans() {
      this.loading = true;
      this.phonenumber = null;
      //this.amount = 0;
      var c = this.subcategories.data.filter(
        (sub) => sub.title === this.subcategory_id
      );
      this.networkImage = c[0].subcat_image;

      var content = JSON.parse(c[0].products);

      this.form.discount = this.getUserLevel(content) + "% Discount";
      this.discount = this.getUserLevel(content);

      this.loading = false;
    },

    verifyCard() {
      this.successCode = false;
      this.verifiying = true;

      if (this.subcategory_id == "") {
        return this.$toasted.show("No provider has veen selected");
      }
      //this.form.plan = this.subcategory_id;

      var plan1 = this.subcategory_id.toString().split("-");

      // console.log(plan1[1]);

      var plan2 = plan1[1].trim().replace(" ", "-");

      this.form.plan = plan2.toString().toLowerCase();

      //console.log(this.form.plan);

      axios
        .post(`/api/verify/card`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          //  this.successCodee=
          //console.log(response.data);
          if (response.data.data.content.error == null) {
            this.cardowner =
              "Customer:" + response.data.data.content.Customer_Name;
            this.successCode = true;
            this.verifiying = false;
          } else {
            this.cardowner = "Meter Number Info not found";
            this.successCode = false;
            this.verifiying = false;
          }
        })
        .catch((error) => {
          console.log(error);
          this.verifiying = false;

          this.$toasted.show(error);
        });
    },

    showActualAmount() {
      this.form.amount = this.amount;

      this.form.discount = this.amount - this.amount * (this.discount / 100);
    },

    getUserLevel(content) {
      if (this.user.userlevel == 0) {
        return content.amount1;
      } else if (this.user.userlevel == 1) {
        return content.amount2;
      } else if (this.user.userlevel == 2) {
        return content.amount3;
      } else if (this.user.userlevel == 3) {
        return content.amount4;
      }
    },
  },
};
</script>

<style scoped>
img {
  width: 9vw;
  height: 9vw;
  padding: 5px;
}

input[type="radio"] {
  display: none;
}

img:hover {
  opacity: 0.6;
  cursor: pointer;
}

img:active {
  opacity: 0.4;
  cursor: pointer;
}

input[type="radio"]:checked + label > img {
  border: 2px solid #6777ef;
}

ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

li {
  float: left;
  border: 2px solid #ccc;
  margin: 5px;
  border-radius: 10px;
}

li a {
  display: block;
  color: white;
  text-align: center;
  padding: 16px;
  text-decoration: none;
}

.centered {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>