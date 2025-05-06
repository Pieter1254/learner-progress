<template>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Learner Progress Dashboard</h1>
                <p class="text-gray-600 mt-1">Track and analyze learner performance across courses</p>
            </div>
            <button @click="fetchLearnerProgress"
                class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                :disabled="isLoading">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"
                    v-if="!isLoading">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ isLoading ? 'Refreshing...' : 'Refresh Data' }}</span>
            </button>
        </div>

        <div v-if="!isLoading && !error" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <div class="text-gray-500 text-sm font-medium">Total Learners</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ learners.length }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <div class="text-gray-500 text-sm font-medium">Active Courses</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ uniqueCourses.length }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <div class="text-gray-500 text-sm font-medium">Avg. Progress</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ averageProgress }}%</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <div class="text-gray-500 text-sm font-medium">Filtered</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ selectedCourse || 'All Courses' }}</div>
            </div>
        </div>

        <div v-if="isLoading" class="flex flex-col items-center justify-center py-16">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mb-4"></div>
            <p class="text-gray-600 text-lg">Loading learner progress data...</p>
            <p class="text-gray-400 text-sm mt-2">Please wait while we fetch the latest information</p>
        </div>

        <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error loading data</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ error }}</p>
                    </div>
                    <div class="mt-4">
                        <button @click="fetchLearnerProgress"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                    clip-rule="evenodd" />
                            </svg>
                            Retry
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filter and Sort Options</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="courseFilter" class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter by Course
                            </span>
                        </label>
                        <div class="relative">
                            <select id="courseFilter" v-model="selectedCourse" @change="fetchLearnerProgress"
                                class="block w-full pl-10 pr-3 py-2 text-base border border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-indigo-50 text-indigo-900">
                                <option value="">All Courses</option>
                                <option v-for="course in uniqueCourses" :key="course" :value="course">{{ course }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="sortProgress" class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-sky-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                                </svg>
                                Sort Learners By
                            </span>
                        </label>
                        <div class="flex space-x-2">
                            <div class="relative flex-1">
                                <select id="sortProgress" v-model="sortDirection" @change="fetchLearnerProgress"
                                    class="block w-full pl-10 pr-3 py-2 text-base border border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 sm:text-sm rounded-md bg-sky-50 text-sky-900">
                                    <option value="">Default Order</option>
                                    <option value="asc">Lowest Progress First</option>
                                    <option value="desc">Highest Progress First</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <button @click="resetFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="learners.length === 0"
                class="bg-white p-8 rounded-lg shadow border border-gray-100 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No learners found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
                <div class="mt-6">
                    <button @click="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset all filters
                    </button>
                </div>
            </div>

            <div v-else class="space-y-6">
                <div v-for="learner in learners" :key="learner.id"
                    class="bg-white overflow-hidden shadow rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-200">

                    <div
                        class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Learner #{{ learner.id }}
                                <span v-if="learner.fullName" class="text-gray-600">- {{ learner.fullName }}</span>
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ learner.enrollments.length }} enrolled courses
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ calculateAverageProgress(learner) }}% Avg. Progress
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Progress
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="enrollment in learner.enrollments" :key="enrollment.courseId">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ enrollment.courseName }}</div>
                                        <div class="text-sm text-gray-500">Course ID: {{ enrollment.courseId }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-32 mr-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="h-2.5 rounded-full" :class="{
                                                        'bg-green-500': enrollment.progress >= 70,
                                                        'bg-blue-500': enrollment.progress >= 40 && enrollment.progress < 70,
                                                        'bg-yellow-400': enrollment.progress < 40
                                                    }" :style="`width: ${enrollment.progress}%`">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 w-12 text-right">
                                                {{ enrollment.progress }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': enrollment.progress >= 70,
                                                'bg-blue-100 text-blue-800': enrollment.progress >= 40 && enrollment.progress < 70,
                                                'bg-yellow-100 text-yellow-800': enrollment.progress < 40
                                            }">
                                            {{ getProgressStatus(enrollment.progress) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// state
const learners = ref([])
const isLoading = ref(true)
const error = ref(null)
const selectedCourse = ref('')
const sortDirection = ref('')

onMounted(() => {
    fetchLearnerProgress()
})

const uniqueCourses = computed(() => {
    const courses = new Set()
    learners.value.forEach(learner => {
        learner.enrollments?.forEach(enrollment => {
            courses.add(enrollment.courseName)
        })
    })
    return Array.from(courses).sort()
})

const averageProgress = computed(() => {
    if (learners.value.length === 0) return 0
    const total = learners.value.reduce((sum, learner) => {
        return sum + calculateAverageProgress(learner)
    }, 0)
    return Math.round(total / learners.value.length)
})

function calculateAverageProgress(learner) {
    if (!learner.enrollments?.length) return 0
    const total = learner.enrollments.reduce((sum, e) => sum + e.progress, 0)
    return Math.round(total / learner.enrollments.length)
}

function getProgressStatus(progress) {
    if (progress >= 70) return 'Excellent'
    if (progress >= 40) return 'Good'
    return 'Needs Improvement'
}

// get user data from the filter and sorting options from the backend
async function fetchLearnerProgress() {
    isLoading.value = true
    error.value = null

    try {
        const params = new URLSearchParams()

        if (selectedCourse.value) {
            params.append('course', selectedCourse.value)
        }

        if (sortDirection.value) {
            params.append('sort', sortDirection.value)
        }

        const response = await axios.get(`/api/learner-progress?${params.toString()}`)
        learners.value = response.data.learners
    } catch (err) {
        error.value = 'Failed to load learners.'
        console.error('Error:', err)
    } finally {
        isLoading.value = false
    }
}

function resetFilters() {
    selectedCourse.value = ''
    sortDirection.value = ''
    fetchLearnerProgress()
}
</script>