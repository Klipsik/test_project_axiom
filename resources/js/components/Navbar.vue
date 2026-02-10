<template>
    <nav class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Title -->
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg">
                        <svg
                            class="w-6 h-6 text-blue-600"
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
                    <h1 class="text-xl font-bold text-white">
                        {{ $t('nav.title') }}
                    </h1>
                </div>

                <!-- Navigation Links & Language Switcher -->
                <div class="flex items-center space-x-4">
                    <!-- Refresh Button -->
                    <button
                        @click="refreshPage"
                        class="p-2 text-white hover:text-blue-100 rounded-md transition duration-200 hover:bg-blue-500"
                        :title="$t('nav.refresh')"
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
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                    </button>

                    <!-- Language Switcher -->
                    <div class="relative">
                        <button
                            @click.stop="toggleLanguageMenu"
                            class="flex items-center space-x-2 px-3 py-2 text-white hover:text-blue-100 rounded-md text-sm font-medium transition duration-200 hover:bg-blue-500 bg-white/10 backdrop-blur-sm"
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
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
                                />
                            </svg>
                            <span class="font-semibold">{{ currentLanguage.toUpperCase() }}</span>
                            <svg
                                class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180': showLanguageMenu }"
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

                        <!-- Language Dropdown -->
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div
                                v-if="showLanguageMenu"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-200"
                            >
                                <button
                                    @click="switchLanguage('en')"
                                    :class="[
                                        'w-full text-left px-4 py-2.5 text-sm transition flex items-center',
                                        currentLanguage === 'en'
                                            ? 'bg-blue-50 text-blue-600 font-medium'
                                            : 'text-gray-700 hover:bg-gray-50'
                                    ]"
                                >
                                    <span class="mr-3 text-lg">🇺🇸</span>
                                    <span>English</span>
                                    <svg
                                        v-if="currentLanguage === 'en'"
                                        class="w-4 h-4 ml-auto text-blue-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="switchLanguage('ru')"
                                    :class="[
                                        'w-full text-left px-4 py-2.5 text-sm transition flex items-center',
                                        currentLanguage === 'ru'
                                            ? 'bg-blue-50 text-blue-600 font-medium'
                                            : 'text-gray-700 hover:bg-gray-50'
                                    ]"
                                >
                                    <span class="mr-3 text-lg">🇷🇺</span>
                                    <span>Русский</span>
                                    <svg
                                        v-if="currentLanguage === 'ru'"
                                        class="w-4 h-4 ml-auto text-blue-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const showLanguageMenu = ref(false);

const currentLanguage = computed(() => locale.value);

const toggleLanguageMenu = () => {
    showLanguageMenu.value = !showLanguageMenu.value;
};

const switchLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
    // Update axios header for backend locale synchronization
    if (window.axios) {
        window.axios.defaults.headers.common['X-Locale'] = lang;
    }
    showLanguageMenu.value = false;
};

const refreshPage = () => {
    window.location.reload();
};

const closeMenuOnClickOutside = (event) => {
    const target = event.target;
    if (!target.closest('.relative') && !target.closest('button')) {
        showLanguageMenu.value = false;
    }
};

onMounted(() => {
    // Load saved language preference
    const savedLocale = localStorage.getItem('locale');
    if (savedLocale && ['en', 'ru'].includes(savedLocale)) {
        locale.value = savedLocale;
        // Update axios header for backend locale synchronization
        if (window.axios) {
            window.axios.defaults.headers.common['X-Locale'] = savedLocale;
        }
    }

    // Close menu on outside click
    document.addEventListener('click', closeMenuOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', closeMenuOnClickOutside);
});
</script>
