<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="p-6 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl text-white">
            <h1 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 opacity-90">Dashboard Guru Bimbingan Konseling</p>
            <p class="mt-2 text-sm opacity-75">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        {{-- Stats Widgets --}}
        @livewire(\App\Filament\Bk\Widgets\StatsOverviewWidget::class)

        {{-- Main Content --}}
        <div class="grid grid-cols-1 gap-6">
            {{-- Pending Diagnosis --}}
            @livewire(\App\Filament\Bk\Widgets\PendingDiagnosisWidget::class)

            {{-- Recent Reports --}}
            @livewire(\App\Filament\Bk\Widgets\RecentReportsWidget::class)
        </div>
    </div>
</x-filament-panels::page>