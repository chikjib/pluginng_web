<template>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="col-lg-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h4 class="card-title card-title-dash">Admin Settings</h4>
            </div>
          </div>
          <div class="mt-3">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="setting in settings.data" :key="setting.id">
                    <td>{{ setting.id }}</td>
                    <td>{{ setting.key }}</td>
                    <td>{{ setting.value }}</td>

                    <td>{{ setting.created_at }}</td>
                    <td>
                      <div class="btn-group" role="group">
                        <a
                          class="btn btn-primary"
                          href="#"
                          @click.stop="toggleModal(setting.id)"
                          >Edit</a
                        >

                        <edit-settings-modal
                          :show="showModal(setting.id)"
                          :setting="setting"
                          @close="toggleModal(setting.id)"
                        />
                        <button
                          class="btn btn-danger"
                          @click="deleteSetting(setting.id)"
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
 
<script>
import EditSettingsModal from "./EditSettingsModal.vue";
//import farmersprofile from "./farmersprofile.vue";

const token = window.localStorage.getItem("token");

export default {
  components: { EditSettingsModal },
  data() {
    return {
      settings: {},

      keyword: null,

      activeModal: 0,
    };
  },

  mounted() {
    //this.getusers();
    this.getProd();
  },

  watch: {
    keyword(after, before) {
      // this.getResults();
    },
  },

  methods: {
    showModal: function (id) {
      return this.activeModal === id;
    },
    toggleModal: function (id) {
      if (this.activeModal !== 0) {
        this.activeModal = 0;
        return false;
      }
      this.activeModal = id;
    },

    getProd() {
      axios
        .get(`/api/admin/settings`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          console.log(response.data);
          // this.users = response.data.data;
          this.settings = response.data;
        });
    },

    deleteSetting(id) {
      if (confirm("Do you really want to delete?")) {
        axios
          .delete(`/api/admin/settings/${id}`, {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          })
          .then((response) => {
            let i = this.settings.data.map((item) => item.id).indexOf(id); // find index of object
            this.settings.data.splice(i, 1);
          });
      }
    },
  },
};
</script>