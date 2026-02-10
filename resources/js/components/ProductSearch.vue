<template>
    <div class="container mx-auto px-4 py-8">
        <!-- Search and Filters Toggle -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                <!-- Search Input -->
                <div class="flex-1 w-full">
                    <div class="relative">
                        <input
                            v-model="filters.q"
                            type="text"
                            :placeholder="$t('filters.searchPlaceholder')"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                        <svg
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Filters Toggle Button -->
                <div class="w-full md:w-auto">
                    <button
                        @click="toggleFilters"
                        class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 flex items-center justify-center space-x-2 font-medium shadow-sm"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                            />
                        </svg>
                        <span>{{ showFilters ? $t('filters.hideFilters') : $t('filters.showFilters') }}</span>
                        <span
                            v-if="activeFiltersCount > 0"
                            class="ml-1 px-2 py-0.5 bg-blue-800 rounded-full text-xs font-semibold"
                        >
                            {{ activeFiltersCount }}
                        </span>
                        <svg
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': showFilters }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Panel (Collapsible) -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform -translate-y-4"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform -translate-y-4"
        >
            <div
                v-if="showFilters"
                class="bg-white rounded-lg shadow-md p-6 mb-6"
            >
                <h2 class="text-xl font-semibold mb-4 text-gray-800">{{ $t('filters.title') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Price from -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.priceFrom') }}
                        </label>
                        <input
                            v-model.number="filters.price_from"
                            type="number"
                            step="0.01"
                            placeholder="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                    </div>

                    <!-- Price to -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.priceTo') }}
                        </label>
                        <input
                            v-model.number="filters.price_to"
                            type="number"
                            step="0.01"
                            placeholder="10000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.category') }}
                        </label>
                        <select
                            v-model.number="filters.category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="search"
                        >
                            <option :value="null">{{ $t('filters.allCategories') }}</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.stock') }}
                        </label>
                        <select
                            v-model="filters.in_stock"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="search"
                        >
                            <option :value="null">{{ $t('filters.all') }}</option>
                            <option :value="true">{{ $t('filters.inStock') }}</option>
                            <option :value="false">{{ $t('filters.outOfStock') }}</option>
                        </select>
                    </div>

                    <!-- Rating from -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.ratingFrom') }}
                        </label>
                        <input
                            v-model.number="filters.rating_from"
                            type="number"
                            step="0.1"
                            min="0"
                            max="5"
                            placeholder="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                    </div>
                </div>

                <!-- Sort and per page -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.sort') }}
                        </label>
                        <select
                            v-model="filters.sort"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="search"
                        >
                            <option value="newest">{{ $t('filters.newest') }}</option>
                            <option value="price_asc">{{ $t('filters.priceAsc') }}</option>
                            <option value="price_desc">{{ $t('filters.priceDesc') }}</option>
                            <option value="rating_desc">{{ $t('filters.ratingDesc') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $t('filters.perPage') }}
                        </label>
                        <select
                            v-model.number="filters.per_page"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="changePerPage"
                        >
                            <option :value="15">15</option>
                            <option :value="30">30</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Reset button -->
                <div class="mt-4 flex justify-end">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-200 flex items-center space-x-2"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                        <span>{{ $t('filters.reset') }}</span>
                    </button>
                </div>
            </div>
        </transition>

        <!-- Results -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">{{ $t('products.loading') }}</p>
        </div>

        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ error }}
        </div>

        <div v-else>
            <div class="mb-4 text-gray-600">
                {{ $t('products.found') }}: {{ pagination?.total || 0 }}
            </div>

            <!-- Products list -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
                >
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ product.name }}
                            </h3>
                            <span
                                :class="[
                                    'px-2 py-1 text-xs rounded',
                                    product.in_stock
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                ]"
                            >
                                {{ product.in_stock ? $t('filters.inStock') : $t('filters.outOfStock') }}
                            </span>
                        </div>

                        <p v-if="product.category" class="text-sm text-gray-500 mb-2">
                            {{ product.category.name }}
                        </p>

                        <div class="flex items-center justify-between mt-4">
                            <div>
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ formatPrice(product.price) }}
                                </p>
                                <div class="flex items-center mt-1">
                                    <span class="text-yellow-500">★</span>
                                    <span class="ml-1 text-sm text-gray-600">
                                        {{ product.rating.toFixed(1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1" class="flex justify-center items-center space-x-2">
                <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    :class="[
                        'px-4 py-2 rounded-md',
                        pagination.current_page === 1
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-blue-500 text-white hover:bg-blue-600'
                    ]"
                >
                    {{ $t('products.prev') }}
                </button>

                <span class="px-4 py-2 text-gray-700">
                    {{ $t('products.page') }} {{ pagination.current_page }} {{ $t('products.of') }} {{ pagination.last_page }}
                </span>

                <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    :class="[
                        'px-4 py-2 rounded-md',
                        pagination.current_page === pagination.last_page
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-blue-500 text-white hover:bg-blue-600'
                    ]"
                >
                    {{ $t('products.next') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const products = ref([]);
const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const showFilters = ref(false);

const filters = reactive({
    q: '',
    price_from: null,
    price_to: null,
    category_id: null,
    in_stock: null,
    rating_from: null,
    sort: 'newest',
    page: 1,
    per_page: 15,
});

// Count active filters (excluding search and sort)
const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.price_from !== null && filters.price_from !== '') count++;
    if (filters.price_to !== null && filters.price_to !== '') count++;
    if (filters.category_id !== null) count++;
    if (filters.in_stock !== null) count++;
    if (filters.rating_from !== null && filters.rating_from !== '') count++;
    return count;
});

let searchTimeout = null;

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        filters.page = 1;
        search();
    }, 500);
};

const search = async () => {
    loading.value = true;
    error.value = null;

    try {
        const params = new URLSearchParams();
        
        if (filters.q) params.append('q', filters.q);
        if (filters.price_from) params.append('price_from', filters.price_from);
        if (filters.price_to) params.append('price_to', filters.price_to);
        if (filters.category_id) params.append('category_id', filters.category_id);
        if (filters.in_stock !== null) params.append('in_stock', filters.in_stock);
        if (filters.rating_from) params.append('rating_from', filters.rating_from);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.page) params.append('page', filters.page);
        if (filters.per_page) params.append('per_page', filters.per_page);

        const response = await window.axios.get(`/api/products?${params.toString()}`);
        products.value = response.data.data;
        
        // Laravel API Resources return pagination in meta object
        if (response.data.meta) {
            pagination.value = {
                current_page: response.data.meta.current_page,
                last_page: response.data.meta.last_page,
                total: response.data.meta.total,
            };
        } else {
            // Fallback for direct pagination response
            pagination.value = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                total: response.data.total,
            };
        }
    } catch (err) {
        error.value = t('products.error');
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        filters.page = page;
        search();
    }
};

const changePerPage = () => {
    filters.page = 1;
    search();
};

const resetFilters = () => {
    filters.q = '';
    filters.price_from = null;
    filters.price_to = null;
    filters.category_id = null;
    filters.in_stock = null;
    filters.rating_from = null;
    filters.sort = 'newest';
    filters.page = 1;
    search();
};

const formatPrice = (price) => {
    const currentLocale = locale.value === 'ru' ? 'ru-RU' : 'en-US';
    return new Intl.NumberFormat(currentLocale, {
        style: 'currency',
        currency: locale.value === 'ru' ? 'RUB' : 'USD',
    }).format(price);
};

const loadCategories = async () => {
    try {
        const response = await window.axios.get('/api/categories');
        categories.value = response.data;
    } catch (err) {
        console.error('Error loading categories:', err);
    }
};

onMounted(() => {
    loadCategories();
    search();
});
</script>
