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
          <form @submit.prevent="sendsms" class="forms-sample">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="senderID">Sender ID</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.senderID"
                  />
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <textarea
                    cols="4"
                    rows="5"
                    placeholder="Phone number separated by coma"
                    class="form-control"
                    v-model="form.phone"
                  >
                  </textarea>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="phone">Message</label>
                  <textarea
                    cols="4"
                    rows="5"
                    placeholder="Enter sms message"
                    class="form-control"
                    v-model="message"
                  >
                  </textarea>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="amount">SMS Rate</label>
                  <input
                    type="text"
                    readonly
                    class="form-control"
                    v-model="form.amount"
                  />
                </div>
                <span class="message-counter"
                  >Page: {{ page }} ({{ message.length }} / 160)</span
                >
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
    };
  },

  mounted() {
    this.loadplan();
  },

  watch: {
    message(after, before) {
      this.page = Math.ceil(this.message.length / 160);
    },
  },

  methods: {
    sendsms() {
      this.form.message = this.message;
      axios
        .post(`/api/send/sms`, this.form, {
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
        .get(`/api/subcategory?category_id=11`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          // console.log(response.data);
          this.subcategories = response.data;

          this.showPlans();
        });
    },

    showPlans() {
      this.loading = true;

      var c = this.subcategories.data.filter((sub) => sub.title === "BULKSMS");

      var content = JSON.parse(c[0].products);
      console.log(content);

      this.form.amount = this.getUserLevel(content);
      console.log(this.form.amount);
      this.loading = false;
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