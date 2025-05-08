<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    movie: {
        type: Object,
        required: true,
    },
    characters: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="movie.title + ' Detail'" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <!-- Main content card -->
                <div class="rounded-xl bg-white p-8 shadow-lg">
                    <!-- Movie Title -->
                    <h1 class="mb-8 text-3xl font-bold text-gray-900">
                        {{ movie.title }}
                    </h1>

                    <!-- Opening Crawl and Characters Sections Wrapper -->
                    <div class="flex space-x-12">
                        <!-- Left Column: Opening Crawl -->
                        <div class="w-1/2">
                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Opening Crawl
                            </h2>
                            <hr class="mb-4 border-gray-300" />
                            <p
                                class="whitespace-pre-line text-sm leading-relaxed text-gray-700"
                            >
                                {{ movie.opening_crawl }}
                            </p>
                        </div>

                        <!-- Right Column: Characters -->
                        <div class="w-1/2">
                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Characters
                            </h2>
                            <hr class="mb-4 border-gray-300" />
                            <div class="text-sm leading-relaxed text-gray-700">
                                <template
                                    v-if="characters && characters.length > 0"
                                >
                                    <template
                                        v-for="(character, index) in characters"
                                        :key="character.id"
                                    >
                                        <Link
                                            :href="
                                                route('people.show', {
                                                    id: character.id,
                                                })
                                            "
                                            class="text-blue-600 hover:underline"
                                        >
                                            {{ character.name }}
                                        </Link>
                                        <span
                                            v-if="index < characters.length - 1"
                                            >,
                                        </span>
                                    </template>
                                </template>
                                <p v-else class="text-gray-500">
                                    No characters listed for this movie.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Search Button -->
                    <div class="mt-10">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-8 py-3 font-bold text-white hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            BACK TO SEARCH
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
