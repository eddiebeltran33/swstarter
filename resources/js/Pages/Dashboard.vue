<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// Reactive state for the search form
const searchType = ref('people'); // 'people' or 'movies', 'people' is default
const searchQuery = ref('');
const searchResults = ref([]); // Placeholder for search results
const isLoading = ref(false); // Add loading state

// Pagination state for People
const peopleCurrentPage = ref(1);
const peopleNextPage = ref(null); // Will store the next page number, or null
const isLoadingMore = ref(false); // Loading state for the "Next" button

const placeholder = computed(() => {
    return searchType.value === 'people'
        ? 'e.g. Chewbacca, Yoda, Boba Fett'
        : 'e.g. A New Hope, The Empire Strikes Back, Return of the Jedi';
});

const performSearch = async () => {
    isLoading.value = true;
    searchResults.value = []; // Clear previous results for any new search

    let endpoint = '';
    let queryParams = `search=${searchQuery.value}`;

    if (searchType.value === 'people') {
        peopleCurrentPage.value = 1; // Reset to page 1 for a new people search
        peopleNextPage.value = null; // Reset next page indicator
        endpoint = '/api/v1/people';
        queryParams += `&page=${peopleCurrentPage.value}`;
    } else {
        endpoint = '/api/v1/movies';
    }

    try {
        const response = await fetch(`${endpoint}?${queryParams}`);

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        searchResults.value = data.data; // Update reactive state with results

        if (searchType.value === 'people') {
            peopleCurrentPage.value = parseInt(data.current_page, 10);
            peopleNextPage.value = data.next_page_number
                ? parseInt(data.next_page_number, 10)
                : null;
        }
    } catch (error) {
        console.error('Error fetching search results:', error);
        searchResults.value = []; // Reset results on error
        if (searchType.value === 'people') {
            peopleNextPage.value = null; // Ensure no "Next" button on error
        }
    } finally {
        isLoading.value = false;
    }
};

const loadMorePeople = async () => {
    if (!peopleNextPage.value || isLoadingMore.value) return;

    isLoadingMore.value = true;
    try {
        const response = await fetch(
            `/api/v1/people?search=${searchQuery.value}&page=${peopleNextPage.value}`,
        );

        if (!response.ok) {
            throw new Error(
                'Network response was not ok when loading more people',
            );
        }

        const data = await response.json();
        searchResults.value.push(...data.data); // Append new results
        peopleCurrentPage.value = parseInt(data.current_page, 10);
        peopleNextPage.value = data.next_page_number
            ? parseInt(data.next_page_number, 10)
            : null;
    } catch (error) {
        console.error('Error fetching more people results:', error);
        // Optionally, provide user feedback here
    } finally {
        isLoadingMore.value = false;
    }
};

watch(
    searchType,
    () => {
        searchResults.value = []; // Clear results when search type changes
        // Reset pagination state for people if switching type
        peopleCurrentPage.value = 1;
        peopleNextPage.value = null;
        isLoadingMore.value = false; // Reset loading more state
    },
    { immediate: true }, // immediate:true clears results on initial load, which is fine for a search page
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
                                class="w-full rounded-lg border border-gray-300 p-3 placeholder-gray-400 focus:border-transparent focus:ring-blue-500"
                                @keydown.enter="performSearch"
                            />
                        </div>

                        <!-- Search Button -->
                        <button
                            type="button"
                            class="w-full rounded-full bg-emerald-500 px-4 py-3 font-semibold text-white shadow-sm hover:bg-emerald-600 focus:ring-gray-400"
                            @click="performSearch"
                            :disabled="isLoading"
                        >
                            {{ isLoading ? 'SEARCHING...' : 'SEARCH' }}
                        </button>

                        <div class="mt-4 text-center">
                            <Link
                                :href="route('metrics.index')"
                                class="inline-block text-blue-600 hover:text-blue-800 hover:underline"
                            >
                                Go to metrics
                            </Link>
                        </div>
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
                            v-else-if="!isLoading && searchResults.length === 0"
                            class="space-y-1 py-10 text-center text-gray-500 md:py-16"
                        >
                            <p class="text-base">There are zero matches.</p>
                            <p class="text-base">
                                Use the form to search for People or Movies.
                            </p>
                        </div>

                        <!-- Results list -->
                        <div v-else>
                            <!-- People results -->
                            <div v-if="searchType === 'people'">
                                <div
                                    v-for="person in searchResults"
                                    :key="person.id"
                                    class="flex items-center justify-between border-b border-gray-200 py-4"
                                >
                                    <span
                                        class="text-base font-bold text-gray-800"
                                    >
                                        {{ person.name }}
                                    </span>
                                    <Link
                                        :href="`/people/${person.id}`"
                                        class="ml-4 rounded-full bg-emerald-500 px-6 py-2 text-xs font-semibold uppercase text-white shadow-sm hover:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        See Details
                                    </Link>
                                </div>
                                <!-- "Next" Button for People Pagination -->
                                <div
                                    v-if="
                                        peopleNextPage &&
                                        searchResults.length > 0
                                    "
                                    class="mt-6 text-center"
                                >
                                    <button
                                        @click="loadMorePeople"
                                        :disabled="isLoadingMore"
                                        class="rounded-full bg-emerald-500 px-8 py-3 font-semibold text-white shadow-sm hover:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {{
                                            isLoadingMore
                                                ? 'LOADING...'
                                                : 'MORE'
                                        }}
                                    </button>
                                </div>
                            </div>

                            <!-- Movie results -->
                            <div v-if="searchType === 'movies'">
                                <div
                                    v-for="movie in searchResults"
                                    :key="movie.id"
                                    class="flex items-center justify-between border-b border-gray-200 py-4"
                                >
                                    <span
                                        class="text-base font-bold text-gray-800"
                                    >
                                        {{ movie.title }}
                                    </span>
                                    <Link
                                        :href="`/movies/${movie.id}`"
                                        class="ml-4 rounded-full bg-emerald-500 px-6 py-2 text-xs font-semibold uppercase text-white shadow-sm hover:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        See Details
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
