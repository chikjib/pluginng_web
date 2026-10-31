<template>
  <div v-if="isloading">
    <vcl-table :row="5" :column="8"></vcl-table>
  </div>

  <div v-else class="col-xl-12 col-lg-12">
    <h4 class="card-title">
      {{ limit == 5 ? "Recent Transaction" : "Transactions" }}
    </h4>
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-9 mb-2">
        <form @submit.prevent="getResults" class="form-inline">
          <div class="col-md-2">
            <select class="form-control" v-model="field">
              <option>Search By</option>
              <option value="phone">phone</option>

              <option value="description">Description</option>
              <option value="ref">ref</option>
              <option value="iuc">iuc</option>
              <option value="meter">meter</option>
              <option value="firstname">firstname</option>
              <option value="email">email</option>
            </select>
          </div>
          <div class="col-md-3">
            <input
              type="text"
              class="form-control"
              v-model="keyword"
              placeholder="Enter Keyword"
            />
          </div>
          <div class="col-md-3">
            <select class="form-control" v-model="status">
              <option>Select Status</option>
              <option value="1">Confirmed</option>
              <option value="0">Pending</option>
              <option value="2">Reversed</option>
              <option value="3">Ignored</option>
              <option value="4">Failed</option>
            </select>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>

            <button type="button" @click="list" class="btn btn-warning">
              Reset
            </button>
          </div>
        </form>
      </div>
      <div class="space-up"></div>
      <div class="col-md-12 dw">
        <form action="/export-transactions" method="GET" class="form-inline">
          <div class="col-md-2">
            <label>Start From Date: </label>
          </div>
          <div class="col-md-2">
            <input type="date" v-model="start_date" name="start_date" required class="form-control" />
          </div>
          <div class="col-md-2">
            <label>End Date: </label>
          </div>
          <div class="col-md-2">
            <input type="date" v-model="end_date" name="end_date" required class="form-control" />
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary">
              Download Transactions
            </button>
          </div>
        </form>
        <!-- <a href="/export-transactions" class="btn btn-primary"
          >Download Transactions</a
        > -->
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Description</th>

            <th>Amount</th>
            <th>Phone</th>
            <th>IUC/Meter</th>
            <th>Bal</th>
            <th>Prev Bal</th>
            <th>Response</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions.data" :key="transaction.id">
            <td>
              <div style="width: 60px" class="box">
                {{ transaction.ref }}
              </div>
            </td>
            <td>{{ transaction.category.title }}</td>
            <td>
              <div v-if="transaction.description.length < 40">
                {{ transaction.description }}
              </div>
              <div v-else>
                <div v-if="showMoreDes == false">
                  {{ transaction.description.substring(0, 40) + ".." }}
                </div>
                <div v-else>
                  {{ transaction.description }}
                </div>
                <button
                  @click="showMoreDes = !showMoreDes"
                  class="btn btn-info"
                >
                  <i v-if="showMoreDes == false" class="fas fa-eye"></i>
                  <i v-else class="fas fa-eye-slash"></i>
                </button>
              </div>
            </td>
            <td>&#8358; {{ $filters.formatNumber(transaction.subtotal) }}</td>
            <td>
              <div v-if="transaction.phone == null">
                {{ transaction.phone }}
              </div>
              <div v-else-if="transaction.phone.length < 13">
                {{ transaction.phone }}
              </div>
              <div v-else>
                <div v-if="showMorePhone == false">
                  {{ transaction.phone.substring(0, 13) + ".." }}
                </div>
                <div v-else>
                  {{ transaction.phone }}
                </div>
                <button
                  @click="showMorePhone = !showMorePhone"
                  class="btn btn-info"
                >
                  <i v-if="showMorePhone == false" class="fas fa-eye"></i>
                  <i v-else class="fas fa-eye-slash"></i>
                </button>
              </div>
            </td>

            <td>{{ transaction.iuc }}{{ transaction.meter }}</td>
            <td>&#8358; {{ $filters.formatNumber(transaction.bal) }}</td>
            <td>&#8358; {{ $filters.formatNumber(transaction.prev_bal) }}</td>
            <td>
              <div v-if="transaction.response == null">
                {{ transaction.response }}
              </div>
              <div v-else-if="transaction.response.length < 40">
                {{ transaction.response }}
              </div>
              <div v-else>
                <div v-if="showMoreDes == false">
                  {{ transaction.response.substring(0, 40) + ".." }}
                </div>
                <div v-else>
                  {{ transaction.response }}
                </div>
                <button
                  @click="showMoreDes = !showMoreDes"
                  class="btn btn-info"
                >
                  <i v-if="showMoreDes == false" class="fas fa-eye"></i>
                  <i v-else class="fas fa-eye-slash"></i>
                </button>
              </div>
            </td>

            <td>
              <label
                class="badge badge-primary"
                v-if="transaction.status == '1'"
                >Confirmed</label
              >

              <label
                class="badge badge-info"
                v-else-if="transaction.status == '2'"
                >Reversed</label
              >

              <label
                class="badge badge-danger"
                v-else-if="transaction.status == '3'"
                >Ignored</label
              >
              <label
                class="badge badge-danger"
                v-else-if="transaction.status == '4'"
                >Failed</label
              >

              <label class="badge badge-warning" v-else>Pending</label>
            </td>
            <td>{{ transaction.created_at }}</td>
            <td>
              <router-link
                v-if="transaction.status == '1'"
                :to="{
                  name: 'receipt',
                  params: { id: transaction.id },
                }"
                class="btn btn-primary"
                >Receipt
              </router-link>

              <a
                    v-if="transaction.status == 0 && transaction.category.id == 4"
                    class="btn btn-primary"
                    href="#"
                    @click.stop="
                      requeryTransaction(transaction.id)
                    "
                    >Requery</a
                  >
                <a
                    v-if="transaction.status == 0 && transaction.category.id == 3"
                    class="btn btn-primary"
                    href="#"
                    @click.stop="
                      requeryTransaction(transaction.id)
                    "
                    >Requery</a
                  >
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <pagination
      v-if="searching == false"
      align="center"
      :limit="5"
      :data="transactions"
      @pagination-change-page="list"
    ></pagination>

    <pagination
      v-if="searching == true"
      align="center"
      :limit="5"
      :data="transactions"
      @pagination-change-page="getResults"
    ></pagination>
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
      status: "Select Status",
      searching: false,
      isloading: false,
      showMoreDes: false,
      showMorePhone: false,
      start_date: null,
      end_date: null,
    };
  },

  mounted() {
    this.list();
  },

  // watch: {
  //   keyword(after, before) {
  //     this.getResults();
  //   },
  // },
  methods: {
    list(page) {
      if (typeof page === "undefined") {
        page = 1;
      }

      // var limited = this.limit == null ? 0 : this.limit;
      this.isloading = true;
      axios
        .get(`/api/orders?page=${page}&user=${this.auth.user.id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then(({ data }) => {
          this.transactions = data;
          this.isloading = false;
        })
        .catch((error) => {
          const response = { ...error.response, data: error.response?.data || {} };
          this.$toasted.show(error.response?.data?.message || error.response?.data?.data || 'Request failed. Please try again.');
          this.isloading = false;
        });
    },

    requeryTransaction: function (id) {
      this.loading = true;

        axios
          .get(`/api/requery/order/${id}`, {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          })
          .then((response) => {
            this.loading = false;

            this.$toasted.show(response.data.message);
            window.location.reload();
          })
          .catch((error) => {
            this.loading = false;
            this.errorflag = error.response.data.message;
            this.$toasted.show(error.response.data.message);
          });

    },

    getResults(page) {
      if (typeof page === "undefined") {
        page = 1;
      }
      this.isloading = true;
      this.searching = true;
      axios
        .get(
          `/api/orders?search_user=${this.keyword}&field=${this.field}&status=${this.status}&user_id=${this.auth.user.id}&page=${page}`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          console.log(response.data);
          this.transactions = response.data;
          this.isloading = false;
        });
    },

    downloadTransactions() {
      if (this.start_date == null) {
        this.$toasted.show("Start Date is required");
      } else if (this.end_date == null) {
        this.$toasted.show("End Date is required");
      } else {
        axios.get(
          `/export-transactions?start_date=${this.start_date}&end_date=${this.end_date}`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          }
        );
      }
    },
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
.space-up {
  margin-bottom: 70px;
}
.dw {
  margin-bottom: 20px;
}
</style>
