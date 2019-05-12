<template>
    <div>
        <div class="flex justify-between items-center mt-2 mb-4">
            <h1 class="text-2xl text-gray-800">Sites</h1>
            <button class="flex items-center shadow bg-blue-500 text-white rounded px-2 py-1 font-semibold hover:bg-blue-600">
                <svg class="feather w-4 h-4 mr-1"><use xlink:href="/images/feather.svg#plus"></use></svg>
                Add new site
            </button>
        </div>
        <table class="shadow bg-white w-full rounded">
            <thead>
                <tr class="uppercase text-gray-500">
                    <th></th>
                    <th class="font-semibold p-4 text-left">Name</th>
                    <th class="font-semibold p-4 text-left">URL</th>
                    <th class="font-semibold p-4 text-left">Server</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="n in 10">
                    <td class="text-center border-t w-16">
                        <img src="https://placeimg.com/150/150/tech" class="rounded-full mx-auto w-10 h-10">
                    </td>
                    <td class="p-4 border-t font-semibold">
                        Neuschaefer
                    </td>
                    <td class="p-4 border-t">
                        https://www.neuschaefer.com/
                    </td>
                    <td class="p-4 border-t">
                        <router-link :to="{name: 'servers.show', params: { id: 123 }}">
                            BlanchedAlmond
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
  export default {
    data() {
      return {};
    },
    methods: {},
  };
</script>
