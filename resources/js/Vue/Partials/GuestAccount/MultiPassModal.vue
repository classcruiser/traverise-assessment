<template>
    <div :id="modalId" tabindex="-1" aria-hidden="true"
         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Multi Pass Using Stats
                    </h3>
                    <button type="button"
                            @click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" >
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
                    <div class="px-6 py-3">
                        <h5 class="font-bold leading-normal text-base text-gray-700">Multi Pass</h5>
                    </div>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Ref
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Multi Pass
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Remaining Usage
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="bg-white border-b border-gray-100">
                                <th scope="row" class="px-6 py-2 font-normal whitespace-nowrap">
                                    {{ multiPassData.ref }}
                                </th>
                                <td class="px-6 py-2 whitespace-nowrap">
                                    {{multiPassData.multi_pass.name }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ (multiPassData.multi_pass.type == 'CREDIT' ? '&euro;' : '') }}
                                    {{ multiPassData.multi_pass.amount }}
                                    {{ (multiPassData.multi_pass.type == 'SESSION' ? ' Sessions' : '') }}
                                </td>
                                <td class="px-6 py-2">
                                    {{ (multiPassData.multi_pass.type == 'CREDIT' ? '&euro;' : '') }}
                                    {{ multiPassData.remaining }}
                                    {{ (multiPassData.multi_pass.type == 'SESSION' ? ' Sessions' : '') }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "MultiPassModal",
    props: {
        modalId: String,
        multiPassData: Object
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
