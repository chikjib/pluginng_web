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
                    <form @submit.prevent="buydata" class="forms-sample">
                        <div class="row">
                            <img
                                v-if="networkImage != ''"
                                :src="
                                    '/template/images/services/' + networkImage
                                "
                            />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category"
                                        >Select Provider</label
                                    >
                                    <select
                                        name="subcategory_id"
                                        v-model="form.subcategory_id"
                                        v-on:change="showPlans"
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
                                    <label for="category">Select Plan</label>
                                    <select
                                        name="subcategory_id"
                                        v-model="form.plan_id"
                                        v-on:change="showAmount"
                                        form-control-sm
                                        class="form-control"
                                    >
                                        <option>Select Plan</option>
                                        <option
                                            v-for="plan in plans"
                                            v-bind:value="plan.plan"
                                            :key="plan.plan"
                                        >
                                            {{
                                                plan.plan +
                                                " &#8358;" +
                                                getUserLevel(plan)
                                            }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        readonly
                                        v-model="form.amount"
                                    />
                                </div>
                            </div>
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
                                <div class="col-md-12">
                                    <input
                                        type="checkbox"
                                        v-model="form.ported"
                                        value="yes"
                                    />

                                    <label for="phone">Is Ported Number?</label>
                                </div>
                            </div>
                            <div
                                class="col-md-12 text-info"
                                v-if="description2 != ''"
                            >
                                {{ description2 }}
                            </div>
                            <button
                                :disabled="loading"
                                type="submit"
                                class="btn btn-primary me-2"
                            >
                                {{ loading ? "Processing.." : "Process" }}
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
            plans: [],

            category_id: null,
            subcategory_id: null,
            plan_id: null,
            phonenumber: null,
            amount: 0,
            loading: false,
            networkImage: "",
            form: {},
            successflag: "",
            errorflag: "",
            description2: "",
        };
    },

    mounted() {
        this.loadplan();
    },

    methods: {
        formatPhoneNumber() {
            // Remove spaces
            this.form.phonenumber = this.form.phonenumber.replace(/\s+/g, "");

            // Replace starting 234 or +234 with 0
            this.form.phonenumber = this.form.phonenumber.replace(
                /^(234|\+234)/,
                "0"
            );

            // If it doesn't start with 0, prepend 0 (assuming you want the user to see and correct it, otherwise you'd enforce this differently)
            if (
                this.form.phonenumber &&
                this.form.phonenumber.charAt(0) !== "0"
            ) {
                this.form.phonenumber = "0" + this.form.phonenumber;
            }
            // Enforce a max length of 11 characters
            if (this.form.phonenumber.length > 11) {
                this.form.phonenumber = this.form.phonenumber.substring(0, 11);
            }
        },
        refreshlist() {
            if (typeof page === "undefined") {
                page = 1;
            }
            axios
                .get(`/api/users?page=${page}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                })
                .then((response) => {
                    console.log(response.data);
                    this.users = response.data;
                });
        },

        buydata() {
            this.loading = true;
            // Format the phone number before sending
            // this.formatPhoneNumber();
            axios
                .post(`/api/purchase/data`, this.form, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                })
                .then((response) => {
                    //console.log(response.data);
                    this.form.amount == "";
                    this.form.phonenumber = "";
                    this.successflag = response.data.message;
                    this.loading = false;
                    this.$toasted.show(response.data.message);
                })
                .catch((error) => {
                    this.loading = false;
                    this.errorflag = error.response.data.message;
                    this.$toasted.show(error.response.data.message);
                });
        },

        loadplan() {
            axios
                .get(`/api/subcategory?category_id=1`, {
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

        showAmount() {
            var c = this.plans.filter((sub) => sub.plan === this.form.plan_id);

            this.form.amount = this.getUserLevel(c[0]);
        },

        showPlans() {
            this.loading = true;
            this.phonenumber = null;
            this.amount = null;
            var c = this.subcategories.data.filter(
                (sub) => sub.id === this.form.subcategory_id
            );
            this.networkImage = c[0].subcat_image;
            var content = JSON.parse(c[0].products);
            this.description2 = c[0].description2;

            this.loading = false;

            this.plans = content;
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
