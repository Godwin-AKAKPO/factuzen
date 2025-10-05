<template>
    <AuthenticatedLayout>

        <Head title="Tableau de bord" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Header avec actions rapides -->
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
                        <p class="text-gray-600">Vue d'ensemble de votre activité</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('invoices.create')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <PlusIcon class="w-5 h-5 inline mr-2" />
                        Nouvelle facture
                        </Link>
                        <Link :href="route('clients.create')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <UserPlusIcon class="w-5 h-5 inline mr-2" />
                        Nouveau client
                        </Link>
                    </div>
                </div>

                <!-- Cartes statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatsCard title="Total Clients" :value="stats.total_clients" icon="Users" color="blue" />
                    <StatsCard title="Factures" :value="stats.total_invoices" icon="FileText" color="green" />
                    <StatsCard title="En attente" :value="stats.pending_invoices" icon="Clock" color="orange" />
                    <StatsCard title="Revenus" :value="formatCurrency(stats.total_revenue)" icon="DollarSign"
                        color="purple" />
                </div>

                <!-- Graphique des revenus -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Évolution des revenus (12 derniers mois)
                        </h3>
                        <div class="h-80">
                            <Line :data="chartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <!-- Deux colonnes : Factures récentes + Factures en retard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Factures récentes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Factures récentes
                                </h3>
                                <Link :href="route('invoices.index')"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Voir tout
                                </Link>
                            </div>

                            <div v-if="recentInvoices.length === 0" class="text-gray-500 text-center py-8">
                                Aucune facture créée
                            </div>

                            <div v-else class="space-y-3">
                                <div v-for="invoice in recentInvoices" :key="invoice.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ invoice.reference }}</div>
                                        <div class="text-sm text-gray-600">{{ invoice.client_name }}</div>
                                        <div class="text-xs text-gray-500">{{ invoice.date }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">
                                            {{ formatCurrency(invoice.total_ttc) }}
                                        </div>
                                        <StatusBadge :status="invoice.status" :is-overdue="invoice.is_overdue" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Factures en retard -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <ExclamationTriangleIcon class="w-5 h-5 inline mr-2 text-red-500" />
                                Factures en retard
                            </h3>

                            <div v-if="overdueInvoices.length === 0" class="text-gray-500 text-center py-8">
                                <CheckCircleIcon class="w-12 h-12 mx-auto mb-2 text-green-500" />
                                Toutes vos factures sont à jour !
                            </div>

                            <div v-else class="space-y-3">
                                <div v-for="invoice in overdueInvoices" :key="invoice.id"
                                    class="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ invoice.reference }}</div>
                                        <div class="text-sm text-gray-600">{{ invoice.client_name }}</div>
                                        <div class="text-xs text-blue-600">
                                            En retard de {{ invoice.days_overdue }} jour(s)
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">
                                            {{ formatCurrency(invoice.total_ttc) }}
                                        </div>
                                        <button
                                            class="text-xs bg-red-600 text-white px-2 py-1 rounded mt-1 hover:bg-red-700">
                                            Relancer
                                        </button>
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

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatsCard from '@/Components/StatsCard.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import {
    PlusIcon,
    UserPlusIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

// Props reçues du contrôleur Laravel
const props = defineProps({
    stats: Object,
    monthlyRevenue: Array,
    recentInvoices: Array,
    overdueInvoices: Array,
})

// Configuration du graphique
const chartData = {
    labels: props.monthlyRevenue.map(item => item.month),
    datasets: [
        {
            label: 'Revenus (FCFA)',
            data: props.monthlyRevenue.map(item => item.revenue),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
        }
    ]
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function (context) {
                    return `Revenus: ${formatCurrency(context.parsed.y)}`
                }
            }
        },
    },
    scales: {
        x: {
            display: true,
            grid: {
                display: false,
            }
        },
        y: {
            display: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.1)',
            },
            ticks: {
                callback: function (value) {
                    return formatCurrency(value)
                }
            }
        }
    },
    interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
    }
}

// Fonction utilitaire pour formater la devise
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-BJ', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount)
}
</script>