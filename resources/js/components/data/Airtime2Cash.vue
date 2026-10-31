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
          <form @submit.prevent="buyairtime" class="forms-sample">
            <div class="row">
              <img
                v-if="networkImage != ''"
                :src="'/template/images/services/' + networkImage"
              />
            </div>

            <div v-if="description != ''" class="col-md-12">
              <p v-html="description"></p>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="category">Select Provider</label>
                  <select
                    name="subcategory_id"
                    v-model="subcategory_id"
                    v-on:change="showPlans"
                    form-control-sm
                    class="form-control"
                  >
                    <option>Select Provider</option>
                    <option
                      v-for="subcategories in subcategories.data"
                      :key="subcategories.id"
                      v-bind:value="subcategories.id"
                    >
                      {{ subcategories.title }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="phone">Phone Number you transferred from</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.tphone"
                    required
                    id="phone"
                    placeholder="phone"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount">Amount Transferred</label>
                  <input
                    type="text"
                    class="form-control"
                    required
                    v-model="tamount"
                  />
                </div>
              </div>

              <input type="hidden" v-model="form.tamount" />

              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount">Amount To Receive</label>
                  <input
                    type="text"
                    class="form-control"
                    readonly
                    v-model="form.amount"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount">Our Percentage</label>
                  <input
                    type="number"
                    class="form-control"
                    readonly
                    v-model="form.percentage"
                  />
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary me-2">Proceed</button>
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
      tamount: 0,
      discount: 0,
      loading: false,
      networkImage: "",
      description: "",
      successflag: "",
      errorflag: "",
    };
  },

  mounted() {
    this.loadplan();
  },

  watch: {
    tamount(after, before) {
      this.showActualAmount();
    },
  },

  methods: {
    buyairtime() {
      this.form.subcategory_id = this.subcategory_id;
      axios
        .post(`/api/purchase/a2cash`, this.form, {
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
        .get(`/api/subcategory?category_id=5`, {
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
      this.amount = null;
      var c = this.subcategories.data.filter(
        (sub) => sub.id === this.subcategory_id
      );
      this.networkImage = c[0].subcat_image;
      this.description = c[0].description;

      var content = JSON.parse(c[0].products);

      this.form.percentage = this.getUserLevel(content);
      // axios
      //   .get(`/api/subcategory?subcategory_id=${this.subcategory_id}`, {
      //     headers: {
      //       Authorization: `Bearer ${token}`,
      //       "Content-Type": "application/json",
      //     },
      //   })
      //   .then((response) => {
      //     console.log(response.data);
      //     this.loading = false;
      //     this.plans = response.data;
      //   });
    },

    showActualAmount() {
      this.form.tamount = this.tamount;

      this.form.amount =
        this.tamount - this.tamount * (this.form.percentage / 100);
    },

    showAmount() {
      this.form.amount = "";

      this.form.amount =
        this.form.tamount - this.form.tamount * (this.form.percentage / 100);
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
#code {
  font-size: 8pt;
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