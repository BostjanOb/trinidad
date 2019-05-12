<template>
    <div>
        <div class="flex justify-between items-center mt-2 mb-4">
            <h1 class="text-2xl text-gray-800">Users</h1>
            <button class="flex items-center shadow bg-blue-500 text-white rounded px-2 py-1 font-semibold hover:bg-blue-600">
                <svg class="feather w-4 h-4 mr-1"><use xlink:href="/images/feather.svg#plus"></use></svg>
                Add new user
            </button>
        </div>
        <table class="shadow bg-white w-full rounded">
            <thead>
                <tr class="uppercase text-gray-500">
                    <th class="font-semibold p-4 text-left">Name</th>
                    <th class="font-semibold p-4 text-left">E-mail</th>
                    <th class="font-semibold p-4 text-left">Role</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="n in 10">
                    <td class="p-4 border-t font-semibold">
                        Manja Mies-Klapp
                    </td>
                    <td class="p-4 border-t">arnaudebenoit@example.fr</td>
                    <td class="p-4 border-t">admin</td>
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
