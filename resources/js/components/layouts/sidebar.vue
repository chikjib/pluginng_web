<template>
  <!-- Sidebar -->
  <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <a
      class="sidebar-brand d-flex align-items-center justify-content-center"
      href="/dashboard"
    >
      <div class="sidebar-brand-icon">
        <img :src="'/template/img/logo/favicon.png'" />
      </div>
      <div class="sidebar-brand-text mx-2">PluginNG</div>
    </a>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item active">
      <router-link to="/dashboard" class="nav-link">
        <i class="fas fa-fw fa-tachometer-alt"></i>

        <span class="menu-title">Dashboard</span>
      </router-link>
    </li>
    <hr class="sidebar-divider" />
    <div class="sidebar-heading">Data & Airtime</div>

    <li class="nav-item">
      <router-link to="/dashboard/airtime" class="nav-link">
        <i class="fas fa-fw fa-mobile"></i>

        <span class="menu-title">Airtime</span>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/data" class="nav-link">
        <i class="fas fa-fw fa-wifi"></i>

        <span class="menu-title">Data</span>
      </router-link>
    </li>

    <hr class="sidebar-divider" />
    <div class="sidebar-heading">Utility Bill</div>
    <li class="nav-item">
      <router-link to="/dashboard/cable" class="nav-link">
        <i class="fas fa-fw fa-laptop"></i>

        <span class="menu-title">Cable Bill</span>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/electricity" class="nav-link">
        <i class="fas fa-fw fa-burn"></i>

        <span class="menu-title">Electricity</span>
      </router-link>
    </li>

    <hr class="sidebar-divider" />
    <div class="sidebar-heading">Exams</div>

    <li class="nav-item">
      <router-link to="/dashboard/exams" class="nav-link">
        <i class="fas fa-fw fa-book"></i>

        <span class="menu-title">Exam Pin</span>
      </router-link>
    </li>
    <hr class="sidebar-divider" />
    <div class="sidebar-heading">Bulk Data</div>
    <li class="nav-item">
        <router-link to="/dashboard/buy-bulk-data" class="nav-link">
          <i class="fas fa-fw fa-wifi"></i>

          <span class="menu-title">Buy Bulk Data</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/vend-bulk-data" class="nav-link">
          <i class="fas fa-fw fa-wifi"></i>

          <span class="menu-title">Vend from Bulk Data</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/bulk-data-transactions" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Bulk Data Transactions</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/general-bucket-balances" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title"
            >View Bucket Data Balance</span
          >
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/bucket-data-pricing-list" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title"
            >Bucket Data Pricing List</span
          >
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/user-bucket-sales-analysis" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title"
            >Bucket Sales Analysis</span
          >
        </router-link>
      </li>


    <hr class="sidebar-divider" />
    <div class="sidebar-heading">Others</div>

    <li class="nav-item">
      <router-link to="/dashboard/withdraw" class="nav-link">
        <i class="fas fa-fw fa-money-bill"></i>

        <span class="menu-title">Withdraw</span>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/airtime2cash" class="nav-link">
        <i class="fas fa-fw fa-money-bill"></i>

        <span class="menu-title">Airtime to Cash</span>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/sms" class="nav-link">
        <i class="fas fa-fw fa-envelope"></i>

        <span class="menu-title">SMS</span>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/user_sales_analysis" class="nav-link">
        <i class="fas fa-fw fa-th"></i>

        <span class="menu-title"
          >Sales Analysis<span class="badge badge-info">New</span></span
        >
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/dashboard/transactions" class="nav-link">
        <i class="fas fa-fw fa-th"></i>

        <span class="menu-title">Transactions</span>
      </router-link>
    </li>

    <li class="nav-item">
      <a href="https://documenter.getpostman.com/view/34324781/2sA3BkcYii" target="_blank" class="nav-link">
        <i class="fas fa-fw fa-edit"></i>

        <span class="menu-title">API Documentation<span class="badge badge-info">New</span></span>
      </a>
    </li>


    <div v-if="this.auth.user.role == 1">
      <hr class="sidebar-divider" />
      <div class="sidebar-heading">Admin</div>

      <li class="nav-item">
        <router-link to="/dashboard/sales_analysis" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Sales Analysis</span>
        </router-link>
      </li>
       <li class="nav-item">
        <router-link to="/dashboard/accounting-analysis" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Sales Accounting Analysis</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/users" class="nav-link">
          <i class="fas fa-fw fa-money-bill"></i>

          <span class="menu-title">Users</span>
        </router-link>
      </li>

      <li class="nav-item">
        <a href="/dashboard/products" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Products</span>
        </a>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/alltransactions" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">All Transactions</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link
          to="/dashboard/allunconfirmedtransactions"
          class="nav-link"
        >
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Unconfirmed Transact</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/bucket-sales-analysis" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Bucket Sales Analysis</span>
        </router-link>
      </li>


      <li class="nav-item">
        <a href="/dashboard/buckets" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Buckets</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/dashboard/all-bucket-transactions" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">All Bucket Transactions</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/dashboard/all-unconfirmed-bucket-transactions" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Unconfirmed Bucket Trans.</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="/dashboard/admin-general-bucket-balances" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">General Bucket Balances</span>
        </a>
      </li>



      <li class="nav-item">
        <router-link to="/dashboard/slides" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Slides</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/mtn-logins" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Mtn Logins</span>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/dashboard/settings" class="nav-link">
          <i class="fas fa-fw fa-th"></i>

          <span class="menu-title">Settings</span>
        </router-link>
      </li>
    </div>

    <hr class="sidebar-divider" />
    <div class="version" id="version-ruangadmin"></div>
  </ul>
  <!-- Sidebar -->
</template>



<script>
export default {
  methods: {
    logout() {
      this.axios
        .post(`${this.$appUrl}/api/logout`)
        .then(({ data }) => {
          this.auth.logout(); //reset local storage
          this.$router.push("/login");
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
