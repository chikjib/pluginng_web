<template>
  <div v-if="isloading">
    <vcl-twitch></vcl-twitch>
  </div>

  <div v-else class="row mb-3">
    <InfoCaller :identity="home" />
    <div class="col-md-12">
      <div
        v-if="home.notice != ''"
        class="alert alert-info alert-dismissible"
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
        <i class="fa fa-info"></i>
        {{ home.notice }}
      </div>
    </div>

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

    <div class="col-md-12">
      <div class="row mb-3">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1">
                    Balance
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    &#8358;{{ $filters.formatNumber(home.balance) }}
                  </div>
                  <div class="mt-2 mb-0 mt-2 text-muted text-xs"></div>
                </div>
                <div class="col-auto">
                  <a
                    class="btn btn-primary"
                    href="javascript:void(0);"
                    data-toggle="modal"
                    data-target="#TopupModal"
                    >Fund Wallet</a
                  >
                </div>
              </div>

              <div
                class="modal fade"
                id="TopupModal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabelTopup"
                aria-hidden="true"
              >
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabelTopup">
                        Topup Balance
                      </h5>
                      <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                      >
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <form @submit.prevent="makePayment" class="forms-sample">
                      <div class="modal-body">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            value="cardpay"
                            v-model="mode"
                          />
                          <label class="form-check-label">
                            Pay with ATM Card
                          </label>
                        </div>
                        <div v-if="mode == 'cardpay'" class="form-group">
                          <div v-if="charge != ''" class="tex-info">
                            You get (Card):
                            {{ amount }} - 50 =
                            {{ Math.round(charge) }}
                          </div>
                          <input
                            type="number"
                            class="form-control"
                            v-model="amount"
                            id="amount"
                            placeholder="Amount"
                          />
                        </div>

                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            value="vaccount"
                            v-model="mode"
                          />
                          <label class="form-check-label">
                            Personal Bank Transfer
                          </label>
                        </div>

                        <div
                          v-if="mode == 'vaccount'"
                          class="col-xl-12 col-md-12 mb-2"
                        >
                          <div class="card h-100">
                            <div class="card-body">
                              <div class="row no-gutters align-items-center">
                                <div class="line">
                                  <p>
                                    <b
                                      >PAY INTO ANY OF THESE ACCOUNTS BELOW, YOU
                                      CAN SAVE THIS AND TRANSFER TO IT
                                      ANYTIME</b
                                    >
                                  </p>
                                </div>

                                <!-- <div class="col mr-2">
                                  <div
                                    class="text-xs font-weight-bold text-uppercase mb-1"
                                  >
                                    {{ home.vaccount_desc }}
                                  </div>

                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName }}</h6>
                                    {{ home.accountNumber }}
                                  </div>
                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName1 }}</h6>
                                    {{ home.accountNumber1 }}
                                  </div>
                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName2 }}</h6>
                                    {{ home.accountNumber2 }}
                                  </div>

                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName3 }}</h6>
                                    {{ home.accountNumber3 }}
                                  </div>
                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName4 }}</h6>
                                    {{ home.accountNumber4 }}
                                  </div>
                                  <div class="mt-2 mb-0 text-muted text-xs">
                                    <h6>{{ home.bankName5 }}</h6>
                                    {{ home.accountNumber5 }}
                                  </div>
                                </div> -->

                                <div class="col-auto">
                                  <div class="bank">
                                    <h5><strong>PalmPay</strong></h5>
                                    <p>
                                      Charges - 0.4% per deposit, Eg Transfer
                                      1000 and get funded 996, or transfer 100k
                                      and get funded 99,700
                                    </p>
                                    <div
                                      v-if="home.palmpay_reserved_acct != null"
                                    >
                                      <p>
                                        <strong>Account Name: </strong
                                        >{{
                                          home.palmpay_reserved_acct.accountName
                                        }}
                                      </p>
                                      <p>
                                        <strong>Account Number: </strong
                                        >{{
                                          home.palmpay_reserved_acct
                                            .accountNumber
                                        }}
                                        <br />
                                      </p>
                                    </div>
                                    <div v-else>
                                      <p>
                                        <b>Account Name:</b>
                                      </p>
                                      <p><b>Account Number:</b> <br /></p>
                                      <button
                                        v-if="
                                          isCreatingReserve_palmpay == false
                                        "
                                        @click="reserve_account('palmpay')"
                                        class="btn btn-primary"
                                        type="button"
                                      >
                                        Create Account
                                      </button>

                                      <button
                                        v-else
                                        class="btn btn-warning"
                                        disabled
                                      >
                                        Please wait! We are creating your
                                        account...
                                      </button>
                                    </div>
                                    <div
                                      class="notification alert alert-warning"
                                      v-if="gra_errors_palmpay"
                                    >
                                      <span
                                        >Please
                                        <a
                                          href="/dashboard/identity-verification"
                                          class="btn-primary"
                                          >Click Here</a
                                        >
                                        to validate your account for funding
                                        with either BVN or NIN. <b>A verified BVN or NIN is required to create a PalmPay reserved account</b>. <br />After
                                        that come back here to generate this
                                        account for funding. Thanks</span
                                      >
                                    </div>
                                  </div>

                                  <div class="bank">
                                    <h5><strong>GT Bank</strong></h5>

                                    <a
                                      href="/dashboard/identity-verification"
                                      class="btn-primary"
                                      v-if="home.bvn == null"
                                      >(Update Bvn)</a
                                    >
                                    <p>
                                      Charges - 0.2% of every deposit e.g
                                      Transfer 1000 to this account and get
                                      funded 998 (it delays sometimes)
                                    </p>
                                    <div
                                      v-if="home.gtbank_reserved_acct != null"
                                    >
                                      <p>
                                        <strong>Account Name: </strong
                                        >{{
                                          home.gtbank_reserved_acct.accountName
                                        }}
                                      </p>
                                      <p>
                                        <strong>Account Number: </strong
                                        >{{
                                          home.gtbank_reserved_acct
                                            .accountNumber
                                        }}
                                        <br />
                                      </p>
                                    </div>
                                    <div v-else>
                                      <p>
                                        <b>Account Name:</b>
                                      </p>
                                      <p><b>Account Number:</b> <br /></p>
                                      <button
                                        v-if="isCreatingReserve_gtbank == false"
                                        @click="reserve_account('gtbank')"
                                        class="btn btn-primary"
                                        type="button"
                                      >
                                        Create Account
                                      </button>

                                      <button
                                        v-else
                                        class="btn btn-warning"
                                        disabled
                                      >
                                        Please wait! We are creating your
                                        account...
                                      </button>
                                    </div>
                                    <div
                                      class="notification alert alert-warning"
                                      v-if="gra_errors_gtbank"
                                    >
                                      <span
                                        >Please
                                        <a
                                          href="/dashboard/identity-verification"
                                          class="btn-primary"
                                          >Click Here</a
                                        >
                                        to validate your account for funding
                                        with NIN or BVN as directed by CBN
                                        (Central Bank of Nigeria). <br />After
                                        that come back here to generate this
                                        account for funding. Thanks</span
                                      >
                                    </div>
                                  </div>

                                  <div class="bank">
                                    <h5><strong>Wema Bank</strong></h5>
                                    <a
                                      href="/dashboard/identity-verification"
                                      class="btn-primary"
                                      v-if="
                                        home.bvn == null && home.nin == null
                                      "
                                      >(Update bvn or nin)</a
                                    >
                                    <p>Wema Bank is 50 naira for any funding</p>
                                    <div v-if="home.wema_reserved_acct != null">
                                      <p>
                                        <strong>Account Name: </strong
                                        >{{
                                          home.wema_reserved_acct.accountName
                                        }}
                                      </p>

                                      <p>
                                        <strong>Account Number: </strong
                                        >{{
                                          home.wema_reserved_acct.accountNumber
                                        }}
                                      </p>
                                    </div>
                                    <div v-else>
                                      <p>
                                        <b>Account Name: </b>
                                      </p>
                                      <p><b>Account Number: </b> <br /></p>
                                      <button
                                        v-if="isCreatingReserve_wema == false"
                                        @click="reserve_account('wema')"
                                        class="btn btn-primary"
                                        type="button"
                                      >
                                        Create Account
                                      </button>

                                      <button
                                        v-else
                                        class="btn btn-warning"
                                        disabled
                                      >
                                        Please wait! We are creating your
                                        account...
                                      </button>
                                    </div>
                                    <div
                                      class="notification alert alert-warning"
                                      v-if="gra_errors_wema"
                                    >
                                      <span
                                        >Please
                                        <a
                                          href="/dashboard/identity-verification"
                                          class="btn-primary"
                                          >Click Here</a
                                        >
                                        to validate your account for funding
                                        with NIN or BVN as directed by CBN
                                        (Central Bank of Nigeria). <br />After
                                        that come back here to generate this
                                        account for funding. Thanks</span
                                      >
                                    </div>
                                  </div>

                                  <div class="bank">
                                    <h5><strong>Moniepoint Bank</strong></h5>
                                    <a
                                      href="/dashboard/identity-verification"
                                      class="btn-primary"
                                      v-if="
                                        home.bvn == null && home.nin == null
                                      "
                                      >(Update bvn or nin)</a
                                    >
                                    <p>
                                      Moniepoint Microfinance Bank is 50 naira
                                      for any funding
                                    </p>
                                    <div
                                      v-if="
                                        home.moniepoint_reserved_acct != null
                                      "
                                    >
                                      <p>
                                        <strong>Account Name: </strong
                                        >{{
                                          home.moniepoint_reserved_acct
                                            .accountName
                                        }}
                                      </p>
                                      <p>
                                        <strong>Account Number: </strong
                                        >{{
                                          home.moniepoint_reserved_acct
                                            .accountNumber
                                        }}
                                        <br />
                                      </p>
                                    </div>
                                    <div v-else>
                                      <p>
                                        <b>Account Name:</b>
                                      </p>
                                      <p>
                                        <b>Account Number:</b>
                                      </p>
                                      <button
                                        v-if="
                                          isCreatingReserve_moniepoint == false
                                        "
                                        @click="reserve_account('moniepoint')"
                                        class="btn btn-primary"
                                        type="button"
                                      >
                                        Create Account
                                      </button>

                                      <button
                                        v-else
                                        class="btn btn-warning"
                                        disabled
                                      >
                                        Please wait! We are creating your
                                        account...
                                      </button>
                                    </div>

                                    <div
                                      class="notification alert alert-warning"
                                      v-if="gra_errors_moniepoint"
                                    >
                                      <span
                                        >Please
                                        <a
                                          href="/dashboard/identity-verification"
                                          class="btn-primary"
                                          >Click Here</a
                                        >
                                        to validate your account for funding
                                        with NIN or BVN as directed by CBN
                                        (Central Bank of Nigeria). <br />After
                                        that come back here to generate this
                                        account for funding. Thanks</span
                                      >
                                    </div>
                                  </div>

                                  <!-- <div class="bank">
                                    <h5><strong>Rehoboth Bank</strong></h5>
                                    <p>
                                      Rehoboth Microfinance Bank is free for
                                      less than 5,000 and 35 naira for 5,000 and
                                      above. (it delays sometimes)
                                    </p>
                                    <div
                                      v-if="home.rehoboth_reserved_acct != null"
                                    >
                                      <p>
                                        <strong>Account Name: </strong
                                        >{{
                                          home.rehoboth_reserved_acct
                                            .accountName
                                        }}
                                      </p>
                                      <p>
                                        <strong>Account Number: </strong
                                        >{{
                                          home.rehoboth_reserved_acct
                                            .accountNumber
                                        }}
                                        <br />
                                      </p>
                                    </div>
                                    <div v-else>
                                      <p>
                                        <b>Account Name:</b>
                                      </p>
                                      <p><b>Account Number:</b> <br /></p>
                                      <button
                                        v-if="
                                          isCreatingReserve_rehoboth == false
                                        "
                                        @click="reserve_account('rehoboth')"
                                        class="btn btn-primary"
                                        type="button"
                                      >
                                        Create Account
                                      </button>

                                      <button
                                        v-else
                                        class="btn btn-warning"
                                        disabled
                                      >
                                        Please wait! We are creating your
                                        account...
                                      </button>
                                    </div>
                                  </div> -->
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            value="manual"
                            v-model="mode"
                          />
                          <label class="form-check-label">
                            Manual Transfer
                          </label>
                        </div>

                        <div
                          v-if="mode == 'manual'"
                          class="col-xl-12 col-md-12 mb-2"
                        >
                          <a href="/dashboard/manual">
                            <div class="card h-100">
                              <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                  <div class="col mr-2">
                                    <div
                                      class="text-xs font-weight-bold text-uppercase mb-1"
                                    >
                                      Manual Funding
                                    </div>
                                    <div
                                      class="h5 mb-0 font-weight-bold text-gray-800"
                                    >
                                      Fund Wallet Manually
                                    </div>
                                    <!-- <div class="mt-2 mb-0 text-muted text-xs">
                </div> -->
                                  </div>
                                  <div class="col-auto">
                                    <i class="fas fa-th fa-2x text-primary"></i>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </a>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button
                          type="button"
                          class="btn btn-outline-primary"
                          data-dismiss="modal"
                        >
                          Cancel
                        </button>

                        <button
                          :disabled="form.amount == ''"
                          type="submit"
                          class="btn btn-primary"
                        >
                          Proceed
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- New User Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1">
                    User Level
                  </div>
                  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                    {{ translateLevel(home.userlevel) }}
                  </div>
                  <div class="mt-2 mb-0 text-muted text-xs"></div>
                </div>
                <div class="col-auto">
                  <!-- <i class="fas fa-users fa-2x text-info"></i> -->

                  <a
                    v-if="home.userlevel < 3"
                    href="javascript:void(0);"
                    data-toggle="modal"
                    data-target="#UpgradeModal"
                    class="btn btn-warning"
                    >Upgrade</a
                  >
                  <a
                    v-else
                    href="javascript:void(0);"
                    data-toggle="modal"
                    data-target="#UpgradeModal"
                    class="btn btn-warning"
                    >Downgrade</a
                  >
                </div>
              </div>
              <div
                class="modal fade"
                id="UpgradeModal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabelUpgrade"
                aria-hidden="true"
              >
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabelUpgrade">
                        Select Level
                      </h5>
                      <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                      >
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <form @submit.prevent="upgradeLevel" class="forms-sample">
                      <div class="modal-body">
                        <div class="form-group">
                          <select
                            v-model="form.levelName"
                            v-on:change="showAmount"
                            class="form-control"
                          >
                            <option>Select Level</option>
                            <option
                              v-for="level in levelPackage"
                              v-bind:value="level.title"
                              :key="level.title"
                            >
                              {{ level.title + " &#8358;" + level.amount }}
                            </option>
                          </select>
                          <div v-if="form.benefit != ''" class="form-group">
                            <div class="text-info">
                              {{ form.benefit }}
                            </div>
                          </div>

                          <input type="hidden" v-model="form.levelamount" />
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button
                          type="button"
                          class="btn btn-outline-primary"
                          data-dismiss="modal"
                        >
                          Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                          Proceed
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Earnings (Annual) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1">
                    Referral Bonus
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    &#8358;{{ $filters.formatNumber(home.totalCommission) }}
                  </div>
                  <div class="mt-2 mb-0 text-muted text-xs">
                    <span
                      >Ref Link:
                      <a :href="reflink">{{ reflink }}</a>
                    </span>
                    {{ home.ref_msg }}
                  </div>
                </div>
                <div class="col-auto">
                  <a
                    class="btn btn-info"
                    href="javascript:void(0);"
                    data-toggle="modal"
                    data-target="#WithModal"
                    >Transfer</a
                  >
                </div>

                <div
                  class="modal fade"
                  id="WithModal"
                  tabindex="-1"
                  role="dialog"
                  aria-labelledby="exampleModalLabelTopup"
                  aria-hidden="true"
                >
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelTopup">
                          Transfer Bonus
                        </h5>
                        <button
                          type="button"
                          class="close"
                          data-dismiss="modal"
                          aria-label="Close"
                        >
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <form @submit.prevent="makeTransfer" class="forms-sample">
                        <div class="modal-body">
                          <div class="form-group">
                            <input
                              type="number"
                              class="form-control"
                              v-model="form2.amount"
                              id="amount"
                              placeholder="Amount"
                            />
                          </div>
                        </div>

                        <div class="modal-footer">
                          <button
                            type="button"
                            class="btn btn-outline-primary"
                            data-dismiss="modal"
                          >
                            Cancel
                          </button>

                          <button
                            :disabled="form2.amount == ''"
                            type="submit"
                            class="btn btn-primary"
                          >
                            Proceed
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Requests Card Example -->
      </div>
    </div>
    <div class="col-md-12">
      <dashboard-menu></dashboard-menu>
    </div>
    <div class="col-md-12">
      <div v-html="home.footer" class="col-md-6 mx-auto mt-5"></div>
    </div>
  </div>
</template>
<script>
const token = window.localStorage.getItem("token");
import { VclTwitch } from "./ui/LoadingSkeletons.js";
import Transactions from "./data/transactions.vue";
import DashboardMenu from "./layouts/dashboardMenu.vue";
import InfoCaller from "./InfoCaller.vue";

export default {
  components: {
    VclTwitch,
    Transactions,
    DashboardMenu,
    InfoCaller,
  },

  data() {
    return {
      user: this.auth.user,
      scriptLoaded: null,
      latestUser: {},
      amount: "",
      home: {},
      charge: "",

      form: {
        amount: "",
      },
      form2: {
        amount: "",
      },

      levelPackage: [],
      isloading: false,
      loading: false,
      successflag: "",
      isCreatingReserve_providus: false,
      isCreatingReserve_rehoboth: false,
      isCreatingReserve_gtbank: false,
      isCreatingReserve_moniepoint: false,
      isCreatingReserve_wema: false,
      isCreatingReserve_palmpay: false,
      gra_errors_providus: false,
      gra_errors_rehoboth: false,
      gra_errors_gtbank: false,
      gra_errors_moniepoint: false,
      gra_errors_wema: false,
      gra_errors_palmpay: false,
      errorflag_providus: "",
      errorflag: "",
      ref: "",
      mode: "",
      reflink: this.$appUrl + "/register?ref=" + this.auth.user.id,
    };
  },
  mounted() {
    this.list();
    this.getUser();
  },

  watch: {
    amount(after, before) {
      this.form.amount = this.amount;
      this.charge = this.amount - 50;
      // this.charge =
      //     this.amount - (this.home.card_charge / 100) * this.amount;
    },
  },

  methods: {
    list() {
      var url = `/api/load-home`;
      this.isloading = true;

      axios
        .get(url, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then(({ data }) => {
          this.home = data.data;
          this.levelPackage = JSON.parse(data.data.levelPackage);
          this.isloading = false;
        })
        .catch((error) => {
          this.$toasted.show(error.response?.data?.message || 'Unable to load your dashboard. Please try again.');
          this.isloading = false;
        });
    },

    onClose: function (data) {
      // Perform other operations upon close
      console.log("Monnify Payment closed", data);
      //window.location.href = "/dashboard";
    },

    getUser() {
      return axios
        .get(`/api/users/${this.user.id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.latestUser = response.data.data;
        })
        .catch((error) => {
          if (error.response?.status !== 401) this.$toasted.show(error.response?.data?.message || 'Unable to load your account details. Please try again.');
        });
    },

 async reserve_account(bank) {
  const hasBvn = Boolean(this.latestUser?.bvn);
  const hasNin = Boolean(this.latestUser?.nin);

  const loadingKey = `isCreatingReserve_${bank}`;
  const errorKey = `gra_errors_${bank}`;
  this[errorKey] = false;

  // Helper method to trigger the API request
  const makeReserveRequest = async () => {
    this[loadingKey] = true;
    const url = `/api/reserve/account/${bank}`;
    const token = this.token || localStorage.getItem("token"); // Ensure token is retrieved

    this.$toasted?.show("Kindly wait while we create your account...");

    try {
      const response = await axios.get(url, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (response.data?.success !== true) {
        throw new Error(response.data?.message || "Account creation failed. Please try again.");
      }

      alert(
        "Account created successfully, kindly click on fund wallet, choose bank transfer to see your accounts."
      );
      window.location.href = "/dashboard";
    } catch (error) {
      console.error("Reserve account error:", error);

      if (bank === "rehoboth") {
        this.errorflag_rehoboth = "Account not available at the moment, please try other banks";
        this.$toasted?.show(this.errorflag_rehoboth);
      } else {
        const message = error.response?.data?.message || error.message || "Error occurred creating reserved account. Try again later.";
        this.$toasted?.show(message);
      }
    } finally {
      this[loadingKey] = false;
      this.isloading = false;
    }
  };

  // Determine allowed action based on BVN and NIN status
  if (!hasBvn && !hasNin) {
    if (bank === "rehoboth") {
      await makeReserveRequest();
    } else {
      this[loadingKey] = false;
      this[errorKey] = true;
    }
  } else if (hasBvn) {
    // BVN is present (covers both BVN-only and BVN+NIN cases)
    const allowedBanks = ["gtbank", "moniepoint", "wema", "rehoboth", "palmpay"];
    if (allowedBanks.includes(bank)) {
      await makeReserveRequest();
    }
  } else if (hasNin) {
    // NIN-only case (BVN is null)
    if (["moniepoint", "wema", "rehoboth", "palmpay"].includes(bank)) {
      await makeReserveRequest();
    }
  }
},


    regenerate_account() {
      var url = `/api/reserve/account?r=1`;
      this.isCreatingReserve = true;

      this.$toasted.show(
        "Kindly wait while we regenerate an account for you..."
      );

      axios
        .get(url, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then(({ data }) => {
          alert(
            "Account regenerated successfully, kindly click on fund wallet, choose bank transfer to see your accounts."
          );

          window.location.href = "/dashboard";
        })
        .catch((error) => {
          const response = { ...error.response, data: error.response?.data || {} };
          this.$toasted.show(response.data.data);
          this.errorflag = response.data.data;
          this.isloading = false;
        });
    },


    translateLevel(userlevel) {
      if (userlevel == 0) {
        return "Normal";
      } else if (userlevel == 1) {
        return "Agent";
      } else if (userlevel == 2) {
        return "Reseller";
      }
      if (userlevel == 3) {
        return "API";
      }
    },

    showAmount() {
      var c = this.levelPackage.filter(
        (sub) => sub.title === this.form.levelName
      );
      console.log(c[0].amount);
      this.form.levelamount = c[0].amount;
      this.form.benefit = c[0].benefit;

      function indexWhere(array, conditionFn) {
        const item = array.find(conditionFn);
        return array.indexOf(item);
      }

      const index = indexWhere(
        this.levelPackage,
        (item) => item.title === this.form.levelName
      );
      this.form.userlevel = index;
    },

    makePayment() {
      this.loading = true;

      this.form.user_id = this.user.id;
    //   console.log(this.form);
      axios
        .post(`/api/generate-payment-link`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
        //   console.log(response.data.data);
          var resp = response.data.data;
          localStorage.setItem("transactionReference",resp['responseBody']['transactionReference']);
          window.location.href = resp['responseBody']['checkoutUrl'];

        })
        .catch((error) => {
          this.loading = false;
          this.errorflag = error.response.data.message;
          this.$toasted.show(error.response.data.message);
          // return null;
        });
    },

    upgradeLevel() {
      this.form.user_id = this.user.id;
      // this.form.userlevel = this.user.userlevel;

      // console.log(this.form.userlevel);

      axios
        .post(`/api/user/update/level`, this.form, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data);
          // this.$router.push({ name: "users" });

          this.successflag =
            response.data.message + ", You need to logout and Login to apply";
          this.$toasted.show(response.data.message);
          this.logout();

          // window.location.href = "/dashboard";
        })
        .catch((error) => {
          console.log(error.response.data.message);
          this.$toasted.show(error.response.data.message);
        });
    },

    makeTransfer() {
      axios
        .post(`/api/user/bonus/transfer`, this.form2, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          // console.log(response.data);

          this.successflag = response.data.message;
          this.$toasted.show(response.data.message);
          window.location.href = "/dashboard";
        })
        .catch((error) => {
          console.log(error.response.data.message);
          this.$toasted.show(error.response.data.message);
        });
    },

    logout() {
      this.axios
        .post(`${this.$appUrl}/api/logout`)
        .then(({ data }) => {
          this.auth.logout(); //reset local storage
          this.$router.push(
            "/login?result=You need to login to apply new level"
          );
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style scoped>
.bank {
  margin-top: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid blue;
}
.line {
  border-bottom: 1px solid blue;
  margin-bottom: 10px;
}
</style>
