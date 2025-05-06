<template>
    <div class="p-6 max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Learner Progress</h1>

        <div v-if="isLoading" class="flex flex-col items-center justify-center py-10">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            <p class="mt-4 text-gray-600">Loading learner progress data...</p>
        </div>

        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline">{{ error }}</span>
            <div class="mt-2">
                <button @click="fetchLearnerProgress"
                    class="text-sm px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    <i class="fas fa-sync-alt mr-2"></i>Retry
                </button>
            </div>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="courseFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by
                        Course</label>
                    <select id="courseFilter" v-model="selectedCourse"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        :disabled="isLoading">
                        <option value="">All Courses</option>
                        <option v-for="course in uniqueCourses" :key="course" :value="course">{{ course }}</option>
                    </select>
                </div>

                <div>
                    <label for="sortProgress" class="block text-sm font-medium text-gray-700 mb-1">Sort by
                        Progress</label>
                    <select id="sortProgress" v-model="sortDirection"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        :disabled="isLoading">
                        <option value="">Default Order</option>
                        <option value="asc">Lowest First</option>
                        <option value="desc">Highest First</option>
                    </select>
                </div>
            </div>

            <div v-if="filteredLearners.length === 0"
                class="bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded">
                <i class="fas fa-info-circle mr-2"></i>No learners found matching your criteria.
            </div>

            <div v-else>
                <div v-for="learner in filteredLearners" :key="learner.id"
                    class="bg-white shadow-md rounded-lg mb-6 border border-gray-200">
                    <div class="p-4 bg-gray-100 flex items-center justify-between rounded-t-lg">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Learner #{{ learner.id }}</h2>
                            <p v-if="learner.fullName" class="text-sm text-gray-600">Name: {{ learner.fullName }}</p>
                        </div>
                        <span class="inline-block bg-blue-500 text-white text-xs px-3 py-1 rounded-full">
                            {{ learner.enrollments.length }} course(s)
                        </span>
                    </div>

                    <div class="p-4">
                        <h3 class="text-md font-medium text-gray-700 mb-3">Enrolled Courses:</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left text-gray-700">
                                <thead class="bg-gray-200 text-gray-600 uppercase tracking-wider">
                                    <tr>
                                        <th class="py-2 px-4">Course Name</th>
                                        <th class="py-2 px-4 text-center">Progress (%)</th>
                                        <th class="py-2 px-4">Progress Bar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="enrollment in learner.enrollments" :key="enrollment.courseId"
                                        class="border-t">
                                        <td class="py-2 px-4 font-medium">{{ enrollment.courseName }}</td>
                                        <td class="py-2 px-4 text-center">{{ enrollment.progress }}%</td>
                                        <td class="py-2 px-4">
                                            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                                <div class="h-full text-white text-xs text-center leading-4 rounded-full"
                                                    :class="{
                                                        'bg-green-500': enrollment.progress >= 70,
                                                        'bg-blue-500': enrollment.progress >= 40 && enrollment.progress < 70,
                                                        'bg-yellow-400': enrollment.progress < 40
                                                    }" :style="`width: ${enrollment.progress}%`">
                                                    {{ enrollment.progress }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'


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
        learner.enrollments.forEach(enrollment => {
            courses.add(enrollment.courseName)
        })
    })
    return Array.from(courses).sort()
})

const filteredLearners = computed(() => {
    let result = [...learners.value]

    if (selectedCourse.value) {
        result = result.filter(learner =>
            learner.enrollments.some(
                enrollment => enrollment.courseName === selectedCourse.value
            )
        )
    }


    if (sortDirection.value) {
        result.sort((a, b) => {
            const avgA = calculateAverageProgress(a)
            const avgB = calculateAverageProgress(b)
            return sortDirection.value === 'asc' ? avgA - avgB : avgB - avgA
        })
    }

    return result
})

function calculateAverageProgress(learner) {
    if (!learner.enrollments.length) return 0
    const total = learner.enrollments.reduce((sum, e) => sum + e.progress, 0)
    return total / learner.enrollments.length
}

async function fetchLearnerProgress() {
    isLoading.value = true
    error.value = null

    try {
        const response = await axios.get('/api/learner-progress')
        const data = response.data
        console.log('Learners data:', JSON.stringify(data))


        learners.value = data.learners

        console.log('Learners data:', JSON.stringify(learners.value))
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch learner progress data'
        console.error('API error:', err)
        learners.value = []
    } finally {
        isLoading.value = false
    }
}

</script>