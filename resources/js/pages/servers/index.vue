<template>
    <div>
        <div class="flex justify-between items-center mt-2 mb-4">
            <h1 class="text-2xl text-gray-800">Servers</h1>
            <button class="flex items-center shadow bg-blue-500 text-white rounded px-2 py-1 font-semibold hover:bg-blue-600">
                <svg class="feather w-4 h-4 mr-1"><use xlink:href="/images/feather.svg#plus"></use></svg>
                Add new server
            </button>
        </div>
        <table class="shadow bg-white w-full rounded">
            <thead>
                <tr class="uppercase text-gray-500">
                    <th class="font-semibold p-4 text-left">Name</th>
                    <th class="font-semibold p-4 text-left">IP</th>
                    <th class="font-semibold p-4 text-right">Hosted sites</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="n in 10">
                    <td class="p-4 border-t font-semibold">
                        <router-link :to="{name: 'servers.show', params: { id: 123 }}">
                            Alba
                        </router-link>
                    </td>
                    <td class="p-4 border-t">192.52.199.1</td>
                    <td class="p-4 border-t text-right">6</td>
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
