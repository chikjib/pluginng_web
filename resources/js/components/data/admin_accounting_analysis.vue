<template>
    <div v-if="isloading">
        <vcl-table :row="5" :column="8"></vcl-table>
    </div>

    <div v-else class="col-xl-12 col-lg-12">
        <div class="row">
            <div class="col-md-12 mb-2">
                <form @submit.prevent="getResults" class="form-horizontal">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Select Status</label>
                                    <select class="form-control" v-model="form.status">
                                        <option value="">Status</option>
                                        <option value="1">Confirmed</option>
                                        <option value="3">Cancelled</option>
                                        <option value="4">Failed</option>
                                        <option value="2">Reversed</option>
                                        <option value="0">Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="provider">Data Type</label>
                                    <select name="subcategory_id" v-model="form.subcategory_id" class="form-control">
                                        <option value="">Select Type</option>
                                        <option v-for="subcategories in subcategories.data" :key="subcategories.id"
                                            v-bind:value="subcategories.id">
                                            {{ subcategories.title }} -
                                            {{ subcategories.category.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">API Route</label>
                                    <select class="form-control" v-model="form.api_route">
                                        <option value="">Select API Route</option>
                                        <option>MTNDIRECT</option>
                                        <option>SMEPLUG</option>
                                        <option>AIRTELEDUSITE</option>
                                        <option>AIRTIMENIGERIA</option>
                                        <option>VTPASS</option>
                                        <option>RINGO</option>
                                        <option>AUTOPILOT</option>
                                        <option>SUBARENA</option>
                                        <option>TBCHPORTAL</option>
                                        <option>OGADAM</option>
                                        <option>GONGOZ</option>
                                        <option>AYINLAK</option>
                                        <option>JONET</option>
                                        <option>GONGOZ</option>
                                        <option>AUTOPILOTAWUF</option>
                                        <option>AUTOSYNCAWUF</option>
                                        <option>TBCHPORTAL</option>
                                        <option>TOPUPACCESS</option>
                                        <option>ZOEPORTAL</option>
                                        <option>KORAPORTAL</option>
                                        <option>ROYALPORTAL</option>
                                        <option>SUBPORTAL</option>
                                        <option>DATAMALL</option>
                                        <option>GTECHPORTAL</option>
                                        <option>WISPER</option>
                                        <option>INTEGRITYPORTAL</option>
                                        <option>DRRCOMM</option>
                                        <option>VTUPLUGPORTAL</option>
                                        <option>NAIJASUBPORTAL</option>
                                        <option>COOLSUBPORTAL</option>
                                        <option>AMAKASUBPORTAL</option>
                                        <option>AMAKASUBPORTAL</option>
                                        <option>ZOEDATAPORTAL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category"> Start Date</label>
                                    <input type="datetime-local" v-model="form.start_date" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category"> End Date</label>
                                    <input type="datetime-local" v-model="form.end_date" class="form-control" />
                                </div>
                            </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Load</button>
                                </div>
                            </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" v-if="transactions && transactions.length">
                <thead class="thead-light">
                    <tr>
                        <th>Total Count</th>
                        <th v-if="hasDataSize">Total Amount (*by Data Size)</th>
                        <th v-if="hasCostPrice">Total Cost Price</th>
                        <th v-if="hasSumAmount">Total Amount Bought</th>
                        <th v-if="hasAmountPaid">Total Amount Users Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="transaction in transactions" :key="transaction.id">
                        <td>{{ $filters.formatNumber(transaction.countPlan) }}</td>
                        <td v-if="transaction.dataSize !== null && transaction.dataSize !== undefined">
                            ₦{{ $filters.formatNumber(transaction.dataSize) }}
                        </td>
                        <td v-if="transaction.costPrice !== null && transaction.costPrice !== undefined">
                            ₦{{ $filters.formatNumber(transaction.costPrice) }}
                        </td>

                        <td v-if="transaction.sumAmount !== null && transaction.sumAmount !== undefined">₦{{ $filters.formatNumber(transaction.sumAmount) }}</td>

                        <td v-if="transaction.amountPaid !== null && transaction.amountPaid !== undefined">
                            ₦{{ $filters.formatNumber(transaction.amountPaid) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Optional fallback if there’s no data -->
            <div v-else class="text-center p-3">
                No data available
            </div>
        </div>

        <br />
        <br />
        <br />
        <!-- Load user wallet -->
        <button class="btn btn-primary" @click="show_wallet">Show Total Users' wallet</button>
        <div v-if="total_wallet_show" style="margin-top: 30px;">
            <h4>Total User's Wallet balance : <strong>&#8358;{{ $filters.formatNumber(total_wallet['data']) }}</strong></h4>
        </div>
    </div>
</template>

<script>
import { VclTable } from "../ui/LoadingSkeletons.js";

const token = window.localStorage.getItem("token");

export default {
    props: ["limit"],
    components: { VclTable },

    data() {
        return {
            transactions: {},
            keyword: null,
            field: "Search By",
            form: {
                status: "",
                start_date: new Date().toISOString().slice(0, 16),
                end_date: new Date().toISOString().slice(0, 16),
            },
            subcategories: {},

            searching: false,
            isloading: false,

            category_id: null,
            subcategory_id: null,
            plan_id: null,

            showMoreDes: false,
            showMorePhone: false,

            total_wallet: 0,
            total_wallet_show: false,
        };
    },

    mounted() {
        this.loadplan();
    },

    // watch: {
    //   keyword(after, before) {
    //     this.getResults();
    //   },
    // },

    computed: {
        hasDataSize() {
            return (
                this.transactions &&
                Array.isArray(this.transactions) &&
                this.transactions.some(t => t.dataSize != null) // checks both null & undefined
            );
        },

        hasCostPrice() {
            return (
                this.transactions &&
                Array.isArray(this.transactions) &&
                this.transactions.some(t => t.costPrice != null)
            );
        },

        hasSumAmount() {
            return (
                this.transactions &&
                Array.isArray(this.transactions) &&
                this.transactions.some(t => t.sumAmount != null) // checks both null & undefined
            );
        },

        hasAmountPaid() {
            return (
                this.transactions &&
                Array.isArray(this.transactions) &&
                this.transactions.some(t => t.amountPaid != null) // checks both null & undefined
            );
        },

    },

    methods: {
        loadplan() {
            axios
                .get(`/api/accounting`, {
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

        getResults(page) {
            if (typeof page === "undefined") {
                page = 1;
            }

            this.isloading = true;
            this.searching = true;
            this.errorMessage = "";
            this.transactions = null; // reset old data

            axios
                .post(`/api/accounting/reports`, this.form, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                })
                .then((response) => {
                    console.log(response.data);

                    // ✅ Laravel sendResponse() case
                    if (response.data.success === true) {
                        this.transactions = response.data.data.map(t => ({
                            countPlan: t.countPlan ?? 0,
                            dataSize: t.dataSize ?? null,
                            costPrice: t.costPrice ?? null,
                            amountPaid: t.amountPaid ?? null,
                            sumAmount: t.sumAmount ?? null
                        })); // your report data
                        this.errorMessage = "";
                    }
                    // ✅ Laravel sendError() case
                    else if (response.data.success === false) {
                        this.errorMessage =
                            response.data.message || "No data found for the selected filters.";
                        this.transactions = { data: [] }; // ensure table doesn’t crash
                    }
                })
                .catch((error) => {
                    console.error("API Error:", error);

                    // ✅ Network/server errors
                    if (error.response && error.response.data) {
                        this.errorMessage =
                            error.response.data.message ||
                            "An error occurred while fetching reports.";
                    } else {
                        this.errorMessage = "Unable to connect to server. Please try again.";
                    }

                    this.transactions = { data: [] };
                })
                .finally(() => {
                    this.isloading = false;
                    this.searching = false;
                });
        },

        show_wallet() {
            axios
                .get(`/api/total-wallet`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                })
                .then((response) => {
                    console.log(response.data);
                    this.total_wallet = response.data;
                    this.total_wallet_show = true;
                });
        }
    },
};
</script>

<style scoped>
.pagination {
    margin-bottom: 0;
    margin-top: 5;
}

.box {
    inline-size: 5px;
    overflow-wrap: break-word;
}
</style>
