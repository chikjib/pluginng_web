
<template>
  <div>
    <!-- TopBar -->
    <nav
      class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top"
    >
      <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
      </button>
      <a
        class="btn btn-info rounded-circle mr-3"
        target="_blank"
        href="https://play.google.com/store/apps/details?id=com.pluginng.pluginng
"
        >Get App</a
      >

      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="userDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <img
              class="img-profile rounded-circle"
              :src="'/template/img/boy.png'"
              style="max-width: 60px"
            />
            <span class="ml-2 d-none d-lg-inline text-white small">{{
              user.lastname
            }}</span>
          </a>
          <div
            class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="userDropdown"
          >
            <a class="dropdown-item" href="/dashboard/profile">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Profile
            </a>

            <div class="dropdown-divider"></div>
            <a
              class="dropdown-item"
              href="javascript:void(0);"
              data-toggle="modal"
              data-target="#logoutModal"
            >
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Logout
            </a>
          </div>
        </li>
      </ul>
    </nav>
    <!-- Topbar -->

    <!-- Modal Logout -->
    <div
      class="modal fade"
      id="logoutModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabelLogout"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabelLogout">Ohh No!</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to logout?</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-outline-primary"
              data-dismiss="modal"
            >
              Cancel
            </button>

            <a
              class="btn btn-primary"
              href="javascript:void(0)"
              @click="logout()"
            >
              Logout</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

          

<script>
export default {
  data() {
    return {
      user: this.auth.user,
    };
  },

  methods: {
    logout() {
      this.axios
        .post(`/api/logout`)
        .then(({ data }) => {
          this.auth.logout(); //reset local storage
          // this.$router.push("/login");
          window.location.href = "/login";
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style>
</style>