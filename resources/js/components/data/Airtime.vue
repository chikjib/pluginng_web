<template>
  <div class="col-xl-8 col-lg-7">
    <div class="card mb-4">
      <div
        class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
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
          <form @submit.prevent="buyairtime" class="forms-sample">
            <div class="row">
              <ul>
                <li
                  v-for="subcategories in subcategories.data"
                  :key="subcategories.id"
                >
                  <div class="row col-md-3 col-sm-6 col-xs-12 col-lg-4">
                    <input
                      type="radio"
                      name="choice"
                      :disabled="loading"
                      v-model="form.subcategory_id"
                      :id="subcategories.title"
                      v-on:change="showPlans"
                      :value="subcategories.id"
                    />
                    <label :for="subcategories.title">
                      <img
                        :src="
                          '/template/images/services/' +
                          subcategories.subcat_image
                        "
                      />
                    </label>
                  </div>
                </li>
              </ul>
            </div>

            <hr />
            <div class="row">
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
                    required
                    id="phone"
                    placeholder="phone"
                  />
                </div>

                <div class="col-md-8">
                  <input type="checkbox" v-model="form.ported" value="yes" />

                  <label for="phone">Is Ported Number?</label>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="amount">Charge</label>
                  <input
                    type="text"
                    readonly
                    class="form-control"
                    v-model="form.discount"
                  />
                </div>
              </div>

              <div class="col-md-12 text-info" v-if="description2 != ''">
                {{ description2 }}
              </div>

              <button
                type="submit"
                :disabled="loading"
                class="btn btn-primary me-2"
              >
                Proceed
              </button>
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
      description2: "",
      amount: 0,
      discount: 0,
      loading: false,
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
    buyairtime() {
      this.loading = true;

      axios
        .post(`/api/purchase/airtime`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.loading = false;

          console.log(response);
          this.successflag = response.data.message;
          this.$toasted.show(response.data.message);
        })
        .catch((error) => {
          // console.log(error);
          this.loading = false;

          this.errorflag = error.response.data.message;
          this.$toasted.show(error.response.data.message);
        });
    },

    loadplan() {
      axios
        .get(`/api/subcategory?category_id=2`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          // console.log(response.data);
          this.subcategories = response.data;
        });
    },

    showPlans() {
      this.loading = true;
      this.phonenumber = "";
      this.amount = 0;
      var c = this.subcategories.data.filter(
        (sub) => sub.id === this.form.subcategory_id
      );

      var content = JSON.parse(c[0].products);

      this.form.discount = this.getUserLevel(content) + "% Discount";
      this.discount = this.getUserLevel(content);

      this.description2 = c[0].description2;
      this.loading = false;
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
