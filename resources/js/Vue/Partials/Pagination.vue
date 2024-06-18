<template>
    <div>
        <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px">
                <li class="previous-page" @click.prevent="changePage(prevPage)" v-if="hasPrev">
                    <a href="#" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                </li>
                <li @click.prevent="changePage(page)"  v-for="page in pages">
                    <a href="#" :class="pageBtnClass(page)"
                       class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                       v-text="page"></a>
                </li>
                <li @click.prevent="changePage(nextPage)" v-if="hasNext">
                    <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        current: {
            type: Number,
            default: 1,
            required: true
        },
        total: {
            type: Number,
            default: 0,
            required: true
        },
        perPage: {
            type: Number,
            default: 0,
            required: true
        },
        pageSidesRange: {
            type: Number,
            default: 3
        }
    },

    computed: {
        pages() {
            var pages = []

            for (var i = this.rangeStart; i <= this.rangeEnd; i++) {
                pages.push(i)
            }

            return pages
        },
        rangeStart() {
            let start = this.current - this.pageSidesRange
            return (start > 0) ? start : 1
        },
        rangeEnd() {
            let end = this.current + this.pageSidesRange
            return (end < this.totalPages) ? end : this.totalPages
        },
        totalPages() {
            return Math.ceil(this.total / this.perPage)
        },
        nextPage() {
            return this.current + 1
        },
        prevPage() {
            return this.current - 1
        },
        hasPrev() {
            return this.current > 1
        },
        hasNext() {
            return this.current < this.totalPages
        },

    },

    methods: {
        changePage(page) {
            this.$emit('page-changed', page)
        },
        pageBtnClass(page) {
            return {
                'font-bold': this.current == page,
                'rounded-r-lg': !this.hasNext && page == this.rangeEnd,
                'rounded-l-lg': !this.hasPrev && page == this.rangeStart
            }
        }
    }
}
</script>
