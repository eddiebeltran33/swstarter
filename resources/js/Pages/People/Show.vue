<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Ensure Link is imported

defineProps({
    person: {
        type: Object,
        required: true,
    },
    movies: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="person.name" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <!-- Main content card -->
                <div class="rounded-xl bg-white p-8 shadow-lg">
                    <!-- Character Name -->
                    <h1 class="mb-8 text-3xl font-bold text-gray-900">
                        {{ person.name }}
                    </h1>

                    <!-- Details and Movies Sections Wrapper -->
                    <div class="flex space-x-12">
                        <!-- Left Column: Details -->
                        <div class="w-1/2">
                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Details
                            </h2>
                            <hr class="mb-4 border-gray-300" />
                            <div class="space-y-1 text-base text-gray-700">
                                <p>Birth Year: {{ person.birth_year }}</p>
                                <p>Gender: {{ person.gender }}</p>
                                <p>Eye Color: {{ person.eye_color }}</p>
                                <p>Hair Color: {{ person.hair_color }}</p>
                                <p>Height: {{ person.height }}</p>
                                <p>Mass: {{ person.mass }}</p>
                            </div>
                        </div>

                        <!-- Right Column: Movies -->
                        <div class="w-1/2">
                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Movies
                            </h2>
                            <hr class="mb-4 border-gray-300" />
                            <div class="space-y-1">
                                <template v-if="movies && movies.length > 0">
                                    <Link
                                        v-for="movie in movies"
                                        :key="movie.id"
                                        :href="route('movies.show', movie.id)"
                                        class="block text-base text-blue-600 hover:underline"
                                    >
                                        {{ movie.title }}
                                    </Link>
                                </template>
                                <p v-else class="text-base text-gray-500">
                                    No movies listed for this character.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Search Button -->
                    <div class="mt-10">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-8 py-3 font-bold text-white  hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            BACK TO SEARCH
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
