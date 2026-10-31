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

          <div class="col-md-12 text-info" v-if="description2 != ''">
            {{ description2 }}
          </div>

          <form @submit.prevent="submitManual" class="forms-sample">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="senderID">Payment Name</label>
                  <input
                    type="text"
                    class="form-control"
                    required
                    v-model="form.paymentname"
                  />
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="amount">Amount</label>
                  <input
                    type="number"
                    class="form-control"
                    required
                    v-model="form.amount"
                  />
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="bank">Bank Name</label>
                  <select class="form-control" v-model="form.bank">
                    <option
                      v-for="plan in plans"
                      :key="plan.account"
                      v-bind:value="plan.account"
                    >
                      {{ plan.bank + " " + plan.account }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="senderID">Payment Type</label>
                  <input
                    type="text"
                    placeholder="USSD, Mobile Transfer, POS"
                    class="form-control"
                    required
                    v-model="form.type"
                  />
                </div>
              </div>

              <button type="submit" class="btn btn-primary me-2">Send</button>
            </div>
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
      amount: 0,
      loading: false,
      successflag: "",
      errorflag: "",
      page: 1,
      message: "",
      description2: "",
    };
  },

  mounted() {
    this.loadplan();
  },

  methods: {
    submitManual() {
      axios
        .post(`/api/payment/manual/initiate`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          //console.log(response.data);
          this.successflag = response.data.message;
          this.$toasted.show(response.data.message);
        })
        .catch((error) => {
          // console.log(error);
          this.errorflag = error.response.data.message;
          this.$toasted.show(error.response.data.message);
        });
    },

    loadplan() {
      axios
        .get(`/api/subcategory?category_id=7`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.subcategories = response.data;

          this.showPlans();
        });
    },

    showPlans() {
      this.loading = true;

      var c = this.subcategories.data.filter((sub) => sub.title === "Manual");

      var content = JSON.parse(c[0].products);
      this.description2 = c[0].description2;

      console.log(content);

      this.plans = content;

      this.loading = false;
    },
  },
};
</script>

<style scoped>
img {
  width: 10vw;
  height: 10vw;
  padding: 10px;
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