<template>
  <div
    class="modal fade"
    id="infoModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabelTopup"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabelTopup">
            Attention
          </h5>

          <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                        @click="modalClose"
                      >
                        <span aria-hidden="true">&times;</span>
                      </button>
        </div>
        <div class="modal-body">As Directed by CBN (Central Bank of Nigeria), <br/> If you are using WEMA, STERLING, GTBANK OR MONIEPOINT TO FUND WALLET, it is compulsory that you link up your NIN OR BVN... <br/> To update means of account validation, <a class="btn btn-primary" href="/dashboard/identity-verification">click here </a></div>
      </div>
    </div>
  </div>
</template>

<script>
const token = window.localStorage.getItem("token");

export default {
  name: "InfoCaller",

  mounted() {
    this.getInfo();

  },
  methods: {

    getInfo() {
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
          const my_bvn = data.data.bvn;
          const my_nin = data.data.nin;
          if(my_bvn == null && my_nin == null){
           $(document).ready(function(){
            $('#infoModal').modal('show');
           })

            // this.$router.push("/dashboard/profile")
        }

          this.isloading = false;
        })
        .catch(({ response }) => {
          this.$toasted.show(error.response.data.data);
          this.isloading = false;
        });
    },
    modalClose(){
        $(document).ready(function(){
            $('#infoModal').modal('hide')
        })

    }
  },
};
</script>

<style>
</style>
