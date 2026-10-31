<template>
  <nav v-if="lastPage > 1" aria-label="Pagination">
    <ul class="pagination" :class="{ 'justify-content-center': align === 'center', 'justify-content-end': align === 'right' }">
      <li class="page-item" :class="{ disabled: currentPage <= 1 }">
        <button type="button" class="page-link" aria-label="Previous page" :disabled="currentPage <= 1" @click="select(currentPage - 1)">«</button>
      </li>
      <li v-for="(page, index) in pages" :key="`${page}-${index}`" class="page-item" :class="{ active: page === currentPage, disabled: page === '…' }">
        <span v-if="page === '…'" class="page-link" aria-hidden="true">…</span>
        <button v-else type="button" class="page-link" :aria-label="`Go to page ${page}`" :aria-current="page === currentPage ? 'page' : undefined" @click="select(page)">{{ page }}</button>
      </li>
      <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
        <button type="button" class="page-link" aria-label="Next page" :disabled="currentPage >= lastPage" @click="select(currentPage + 1)">»</button>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'LaravelPagination',
  props: { data: { type: Object, default: () => ({}) }, limit: { type: [Number, String], default: 2 }, align: { type: String, default: 'left' } },
  emits: ['pagination-change-page'],
  computed: {
    meta() { return this.data.meta || this.data; },
    currentPage() { return Number(this.meta.current_page) || 1; },
    lastPage() { return Number(this.meta.last_page) || 1; },
    pages() {
      const radius = Math.max(1, Number(this.limit) || 2);
      const range = new Set([1, this.lastPage]);
      for (let page = Math.max(1, this.currentPage - radius); page <= Math.min(this.lastPage, this.currentPage + radius); page++) range.add(page);
      const result = [];
      for (const page of [...range].sort((a, b) => a - b)) {
        const previous = result[result.length - 1];
        if (typeof previous === 'number' && page - previous > 1) result.push('…');
        result.push(page);
      }
      return result;
    },
  },
  methods: {
    select(page) {
      if (page >= 1 && page <= this.lastPage && page !== this.currentPage) this.$emit('pagination-change-page', page);
    },
  },
};
</script>
