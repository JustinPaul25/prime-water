<template>
  <nav class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6" aria-label="Pagination">
      <div v-if="pagination.from">
          <p class="text-sm text-gray-900">
          Showing
          <span class="font-medium">{{ pagination.from }}</span>
          to
          <span class="font-medium">{{ (pagination.from+19 > pagination.total) ? pagination.total :  pagination.from+19}}</span>
          of
          <span class="font-medium">{{ pagination.total }}</span>
          results
          </p>
      </div>
      <div class="flex-1 flex justify-between sm:justify-end">
          <button v-if="pagination.current_page > 1" v-on:click.prevent="changePage(pagination.current_page - 1)" class="mb-2 mr-2 relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-blue hover:opacity-75">
          Previous
          </button>
          <button v-if="pagination.current_page < pagination.last_page" v-on:click.prevent="changePage(pagination.current_page + 1)" class="mb-2 relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-blue hover:opacity-75">
          Next
          </button>
      </div>
  </nav>
</template>

<script>
export default{
    props: {
      pagination: {
          type: Object,
          required: true
      },
      offset: {
          type: Number,
          default: 4
      }
  },
  computed: {
    pagesNumber() {
      if (!this.pagination.to) {
        return [];
      }
      let from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }
      let to = from + (this.offset * 2);
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }
      let pagesArray = [];
      for (let page = from; page <= to; page++) {
        pagesArray.push(page);
      }
        return pagesArray;
    }
  },
  methods : {
    changePage(page) {
      this.pagination.current_page = page;
      this.$emit('paginate', page);
    }
  }
}
</script>