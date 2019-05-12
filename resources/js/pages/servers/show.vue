<template>
    <div class="flex justify-between mt-4">
        <div class="w-1/3 pr-4">
            <div class="bg-white shadow rounded text-center pb-4 flex flex-col">
                <div class="bg-cover bg-gray-200 h-32 bg-cogs"></div>
                <div>
                    <img src="https://placeimg.com/150/150/tech" class="rounded-full w-24 h-24 -mt-12 mx-auto border-4 border-white">
                    <h1 class="mt-4 text-gray-700 font-semibold text-3xl">Server name</h1>
                    <h2 class="text-gray-600 my-2 text-lg">49.227.50.41</h2>

                    <div>
                        <a href="ssh:" class="inline-flex uppercase items-center border px-2 py-1 font-semibold rounded bg-blue-500 text-white hover:bg-blue-600">
                            <svg class="feather w-4 h-4 mr-1"><use xlink:href="/images/feather.svg#key"></use></svg>
                            ssh
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/3">
            <div class="bg-white shadow rounded">
                <h1 class="text-2xl text-gray-800 px-4 py-2">Sites</h1>
                <table class="bg-white w-full rounded border-t">
                    <thead>
                        <tr class="uppercase text-gray-500">
                            <th class="font-semibold px-4 py-2 text-left">Name</th>
                            <th class="font-semibold px-4 py-2 text-left">URL</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr v-for="n in 10">
                            <td class="px-4 py-2 border-t font-semibold">Palacio verdugo</td>
                            <td class="px-4 py-2 border-t">https://www.palacio-verdugo.com/</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
