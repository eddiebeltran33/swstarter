<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Import Link component
import { ref, computed, watch } from 'vue';

// Reactive state for the search form
const searchType = ref('people'); // 'people' or 'movies', 'people' is default
const searchQuery = ref('');
const searchResults = ref([]); // Placeholder for search results
const isLoading = ref(false); // Add loading state
const placeholder = computed(() => {
    return searchType.value === 'people'
        ? 'e.g. Chewbacca, Yoda, Boba Fett'
        : 'e.g. A New Hope, The Empire Strikes Back, Return of the Jedi';
});
const performSearch = async () => {
    isLoading.value = true; // Set loading state while fetching

    const endpoint =
        searchType.value === 'people' ? '/api/v1/people' : '/api/v1/movies';

    try {
        const response = await fetch(`${endpoint}?search=${searchQuery.value}`);

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        searchResults.value = data.data; // Update reactive state with results
    } catch (error) {
        console.error('Error fetching search results:', error);
        searchResults.value = []; // Reset results on error
    } finally {
        isLoading.value = false; // Reset loading state
    }
};
watch(
    searchType,
    () => {
        searchResults.value = []; // Clear results if query is empty
    },
    { immediate: true },
);
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
                                :placeholder="placeholder"
                                class="w-full rounded-lg border border-gray-300 p-3 placeholder-gray-400 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                @keydown.enter="performSearch"
                            />
                        </div>

                        <!-- Search Button -->
                        <button
                            type="button"
                            class="w-full rounded-full bg-emerald-500 px-4 py-3 font-semibold text-white shadow-sm transition-colors duration-150 ease-in-out hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-gray-400"
                            @click="performSearch"
                            :disabled="isLoading"
                        >
                            {{ isLoading ? 'SEARCHING...' : 'SEARCH' }}
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

                        <!-- Loading indicator -->
                        <div
                            v-if="isLoading"
                            class="py-10 text-center text-gray-500"
                        >
                            <p class="text-base">Loading results...</p>
                        </div>

                        <!-- Empty state message -->
                        <div
                            v-else-if="searchResults.length === 0"
                            class="space-y-1 py-10 text-center text-gray-500 md:py-16"
                        >
                            <p class="text-base">There are zero matches.</p>
                            <p class="text-base">
                                Use the form to search for People or Movies.
                            </p>
                        </div>

                        <!-- Results list -->
                        <div v-else class="space-y-4">
                            <!-- People results -->
                            <div
                                v-if="searchType === 'people'"
                                class="space-y-4"
                            >
                                <div
                                    v-for="person in searchResults"
                                    :key="person.id"
                                    class="rounded-lg border border-gray-200 p-4 hover:bg-gray-50"
                                >
                                    <Link
                                        :href="`/people/${person.id}`"
                                        class="text-lg font-medium text-blue-600 hover:underline"
                                    >
                                        {{ person.name }}
                                    </Link>
                                </div>
                            </div>

                            <!-- Movie results -->
                            <div
                                v-if="searchType === 'movies'"
                                class="space-y-4"
                            >
                                <div
                                    v-for="movie in searchResults"
                                    :key="movie.id"
                                    class="rounded-lg border border-gray-200 p-4 hover:bg-gray-50"
                                >
                                    <Link
                                        :href="`/movies/${movie.id}`"
                                        class="text-lg font-medium text-blue-600 hover:underline"
                                    >
                                        {{ movie.title }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
