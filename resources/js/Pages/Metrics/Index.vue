<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
const selectedDate = ref(''); // Will store YYYY-MM-DD
const metricsData = ref([]);
const loading = ref(false);
const error = ref(null);

// --- Helper Functions ---
function formatDateToYYYYMMDD(date) {
    // Formats a Date object to YYYY-MM-DD string for the date input
    const d = new Date(date);
    const year = d.getFullYear();
    const month = ('0' + (d.getMonth() + 1)).slice(-2); // Months are 0-indexed
    const day = ('0' + d.getDate()).slice(-2);
    return `${year}-${month}-${day}`;
}

function getMetricTitle(name) {
    if (!name) return 'Metric';
    return name
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function formatTime(dateString) {
    if (!dateString) return '';
    try {
        // Display time in user's local timezone
        return new Date(dateString).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });
    } catch (e) {
        return dateString; // fallback
    }
}

// --- API Fetching ---
async function fetchMetrics() {
    if (!selectedDate.value) {
        metricsData.value = []; // Clear data if no date is selected
        return;
    }

    loading.value = true;
    error.value = null;
    metricsData.value = []; // Clear previous data

    try {
        // Now just passing the day parameter directly
        const response = await fetch(
            `/api/v1/metrics?day=${encodeURIComponent(selectedDate.value)}`,
        );

        if (!response.ok) {
            const errorData = await response.text();
            throw new Error(
                `HTTP error! status: ${response.status}, message: ${errorData}`,
            );
        }
        const data = await response.json();
        // The 'value' field is a JSON string, so we need to parse it
        metricsData.value = data.map((metric) => {
            try {
                return {
                    ...metric,
                    value:
                        typeof metric.value === 'string'
                            ? JSON.parse(metric.value)
                            : metric.value,
                };
            } catch (e) {
                console.error(
                    'Failed to parse metric value:',
                    metric.name,
                    metric.value,
                    e,
                );
                return { ...metric, value: { error: 'Could not parse value' } }; // Graceful degradation
            }
        });
    } catch (e) {
        console.error('Failed to fetch metrics:', e);
        error.value = e.message;
    } finally {
        loading.value = false;
    }
}

// --- Lifecycle and Watchers ---
onMounted(() => {
    const today = new Date();
    selectedDate.value = formatDateToYYYYMMDD(today);
    // The watcher will automatically call fetchMetrics when selectedDate is set.
});

watch(selectedDate, (newDateValue) => {
    if (newDateValue) {
        fetchMetrics(); // Call fetchMetrics when selectedDate changes
    } else {
        metricsData.value = [];
        error.value = 'Please select a day.';
    }
});
</script>

<template>
    <Head title="Daily Metrics Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-lg">
                    <div class="p-8">
                        <div class="mb-8">
                            <h2 class="mb-2 text-xl font-bold text-gray-900">
                                Filter Metrics
                            </h2>
                            <hr class="mb-4 border-gray-300" />
                            <!-- Updated layout for a single date picker and a button -->
                            <div class="flex items-end space-x-4">
                                <div>
                                    <label
                                        for="selectedDate"
                                        class="block text-sm font-medium text-gray-700"
                                        >Select Day</label
                                    >
                                    <input
                                        type="date"
                                        id="selectedDate"
                                        v-model="selectedDate"
                                        class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                                    />
                                </div>
                                <button
                                    @click="fetchMetrics"
                                    :disabled="loading || !selectedDate"
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-8 py-3 font-bold text-white hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{
                                        loading ? 'Loading...' : 'Refresh Data'
                                    }}
                                </button>
                            </div>
                        </div>

                        <div v-if="loading" class="py-10 text-center">
                            <p class="text-lg text-gray-500">
                                Loading metrics...
                            </p>
                        </div>
                        <div
                            v-else-if="error"
                            class="relative rounded border border-red-400 bg-red-100 px-4 py-10 py-3 text-center text-red-700"
                            role="alert"
                        >
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ error }}</span>
                        </div>
                        <div
                            v-else-if="metricsData.length === 0 && !loading"
                            class="py-10 text-center"
                        >
                            <p class="text-lg text-gray-500">
                                No metrics found for
                                {{ selectedDate || 'the selected period' }}.
                            </p>
                        </div>

                        <div
                            v-else
                            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                        >
                            <div
                                v-for="metric in metricsData"
                                :key="metric.id"
                                class="flex flex-col rounded-lg bg-white p-6 shadow-md"
                            >
                                <h3
                                    class="mb-1 text-lg font-semibold text-emerald-700"
                                >
                                    {{ getMetricTitle(metric.name) }}
                                </h3>
                                <!-- The period display will show the start and end times from the metric data -->
                                <p class="mb-3 text-xs text-gray-500">
                                    Period: {{ formatTime(metric.start_at) }} -
                                    {{ formatTime(metric.end_at) }}
                                    <br />
                                    On Date:
                                    {{
                                        new Date(
                                            metric.start_at,
                                        ).toLocaleDateString()
                                    }}
                                </p>

                                <div class="mt-auto">
                                    <!-- Average Request Duration -->
                                    <div
                                        v-if="
                                            metric.name ===
                                                'average_request_duration' &&
                                            metric.value
                                        "
                                    >
                                        <p
                                            class="text-3xl font-bold text-gray-800"
                                        >
                                            {{
                                                parseFloat(
                                                    metric.value
                                                        .average_duration_ms,
                                                ).toFixed(2)
                                            }}
                                            ms
                                        </p>
                                    </div>

                                    <!-- Total Errors -->
                                    <div
                                        v-else-if="
                                            metric.name === 'total_errors' &&
                                            metric.value
                                        "
                                    >
                                        <p
                                            class="text-3xl font-bold text-gray-800"
                                        >
                                            {{ metric.value.total_errors }}
                                        </p>
                                    </div>

                                    <!-- Top Search Terms (People or Movie) -->
                                    <div
                                        v-else-if="
                                            metric.name.includes(
                                                '_search_terms',
                                            ) && Array.isArray(metric.value)
                                        "
                                    >
                                        <ul
                                            v-if="metric.value.length > 0"
                                            class="space-y-1 text-sm"
                                        >
                                            <li
                                                v-for="(
                                                    item, index
                                                ) in metric.value"
                                                :key="index"
                                                class="flex items-center justify-between"
                                            >
                                                <span
                                                    class="truncate text-gray-700"
                                                    :title="item.search_term"
                                                    >{{
                                                        item.search_term
                                                    }}</span
                                                >
                                                <span
                                                    class="ml-2 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700"
                                                    >{{ item.count }}</span
                                                >
                                            </li>
                                        </ul>
                                        <p v-else class="text-sm text-gray-500">
                                            No search terms recorded.
                                        </p>
                                    </div>

                                    <!-- Top Resources Visited (People or Movie) -->
                                    <div
                                        v-else-if="
                                            metric.name.includes(
                                                '_resources_visited',
                                            ) && Array.isArray(metric.value)
                                        "
                                    >
                                        <ul
                                            v-if="metric.value.length > 0"
                                            class="space-y-1 text-sm"
                                        >
                                            <li
                                                v-for="(
                                                    item, index
                                                ) in metric.value"
                                                :key="index"
                                                class="flex items-center justify-between"
                                            >
                                                <span class="text-gray-700"
                                                    >Resource ID:
                                                    {{ item.resource_id }}</span
                                                >
                                                <span
                                                    class="ml-2 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700"
                                                    >{{ item.count }}</span
                                                >
                                            </li>
                                        </ul>
                                        <p v-else class="text-sm text-gray-500">
                                            No resources visited.
                                        </p>
                                    </div>

                                    <!-- Fallback for unknown metric types or parse errors -->
                                    <div v-else>
                                        <p class="text-sm text-gray-500">
                                            Data not displayable or value is
                                            complex.
                                        </p>
                                        <pre
                                            v-if="
                                                metric.value &&
                                                metric.value.error
                                            "
                                            class="mt-2 rounded bg-red-50 p-2 text-xs text-red-500"
                                            >{{ metric.value.error }}</pre
                                        >
                                        <pre
                                            v-else-if="
                                                typeof metric.value === 'object'
                                            "
                                            class="mt-2 overflow-x-auto rounded bg-gray-50 p-2 text-xs"
                                            >{{
                                                JSON.stringify(
                                                    metric.value,
                                                    null,
                                                    2,
                                                )
                                            }}</pre
                                        >
                                        <p v-else class="text-xs text-gray-500">
                                            {{ metric.value }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
