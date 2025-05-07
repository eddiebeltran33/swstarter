<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Reactive state for the search form
const searchType = ref('people'); // 'people' or 'movies', 'people' is default
const searchQuery = ref('');

// Placeholder for search function (not implemented as per request)
// const performSearch = () => {
//     console.log(`Searching for ${searchType.value}: ${searchQuery.value}`);
// };

const searchResults = ref([]); // Placeholder for search results
const performSearch = () => {
    // This function would typically make an API call to fetch search results
    // For now, we will just log the search type and query
    console.log(`Searching for ${searchType.value}: ${searchQuery.value}`);
};
</script>

<template>
    <Head title="SWStarter Search" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="flex flex-col gap-8 lg:flex-row">
                    <!-- Left Column: Search Form -->
                    <div
                        class="w-full rounded-xl bg-white p-6 shadow-lg md:p-8 lg:w-1/3"
                    >
                        <h2 class="mb-6 text-xl font-semibold text-gray-800">
                            What are you searching for?
                        </h2>

                        <!-- Radio Buttons for Search Type -->
                        <div class="mb-6 flex items-center space-x-6">
                            <label
                                for="searchTypePeople"
                                class="flex cursor-pointer items-center space-x-2"
                            >
                                <input
                                    type="radio"
                                    id="searchTypePeople"
                                    name="searchTypeGroup"
                                    value="people"
                                    v-model="searchType"
                                    class="form-radio h-5 w-5 text-blue-600 accent-blue-600 focus:ring-blue-500"
                                />
                                <span class="font-medium text-gray-700"
                                    >People</span
                                >
                            </label>
                            <label
                                for="searchTypeMovies"
                                class="flex cursor-pointer items-center space-x-2"
                            >
                                <input
                                    type="radio"
                                    id="searchTypeMovies"
                                    name="searchTypeGroup"
                                    value="movies"
                                    v-model="searchType"
                                    class="form-radio h-5 w-5 text-blue-600 accent-blue-600 focus:ring-blue-500"
                                />
                                <span class="font-medium text-gray-700"
                                    >Movies</span
                                >
                            </label>
                        </div>

                        <!-- Search Input Field -->
                        <div class="mb-6">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="e.g. Chewbacca, Yoda, Boba Fett"
                                class="w-full rounded-lg border border-gray-300 p-3 placeholder-gray-400 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Search Button -->
                        <button
                            type="button"
                            class="w-full rounded-full bg-gray-200 px-4 py-3 font-semibold text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        >
                            SEARCH
                        </button>
                    </div>

                    <!-- Right Column: Results -->
                    <div
                        class="w-full rounded-xl bg-white p-6 shadow-lg md:p-8 lg:w-2/3"
                    >
                        <h2 class="mb-4 text-xl font-semibold text-gray-800">
                            Results
                        </h2>
                        <hr class="mb-6 border-t border-gray-300" />

                        <div
                            class="space-y-1 py-10 text-center text-gray-500 md:py-16"
                        >
                            <p class="text-base">There are zero matches.</p>
                            <p class="text-base">
                                Use the form to search for People or Movies.
                            </p>
                        </div>

                        <!-- Actual results would be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
