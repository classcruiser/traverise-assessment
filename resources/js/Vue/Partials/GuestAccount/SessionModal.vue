<template>
    <div :id="modalId" tabindex="-1" aria-hidden="true"
         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Session Classes
                    </h3>
                    <button type="button"
                            @click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="space-y-2">
                    <div class="">
                        <div class="px-6 py-3">
                            <h5 class="font-bold leading-normal text-base text-gray-700">Classes</h5>
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        CLASS
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        DATE
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        INSTRUCTOR
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        GUEST
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        PRICE
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b border-gray-100"
                                    v-for="session in sessionData.sessions"
                                >
                                    <td scope="row" class="px-6 py-2 text-gray-800 whitespace-nowrap">
                                        <b>{{ session.session.category.short_name }} {{ session.session.name }}</b>
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ moment(session.date).format('dddd, DD MMM YY') }}, {{ session.schedule.start_formatted }} - {{ session.schedule.end_formatted }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ session.instructor ? session.instructor.name : '-' }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ session.full_name }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        &euro;{{ session.price }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 border-t border-gray-100">
                    <div class="">
                        <div class="px-6 py-3">
                            <h5 class="font-bold leading-normal text-base text-gray-700">Guests</h5>
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        GUEST
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        EMAIL
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        SESSION
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        WEIGHT
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        DATE
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b border-gray-100"
                                    v-for="guest in sessionData.guests"
                                >
                                    <td scope="row" class="px-6 py-2 text-gray-800 whitespace-nowrap">
                                        <b>{{ guest.full_name }}</b>
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ guest.email }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ guest.session.name }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ guest.weight }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                       {{ moment(guest.date).format('ddd, DD MMM YY') }} {{ guest.schedule.start_formatted }} - {{ guest.schedule.end_formatted }}
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
export default {
    name: "SessionModal",
    props: {
        modalId: String,
        sessionData: Object
    },
    methods: {
        closeModal() {
            this.$emit('onCloseModal');
        }
    }
}
</script>

<style scoped>

</style>
