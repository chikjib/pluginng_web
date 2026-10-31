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
export default {
  name: 'InfoCaller',
  props: { identity: { type: Object, default: () => ({}) } },
  computed: {
    needsVerification() {
      return this.identity?.bvn === null && this.identity?.nin === null;
    },
  },
  watch: {
    needsVerification: {
      immediate: true,
      handler(needed) {
        if (needed) this.$nextTick(() => this.toggleModal('show'));
      },
    },
  },
  beforeUnmount() { this.toggleModal('hide'); },
  methods: {
    toggleModal(action) {
      const modal = window.jQuery?.('#infoModal');
      modal?.modal?.(action);
    },
    modalClose() { this.toggleModal('hide'); },
  },
};
</script>

<style>
</style>
