<h2 class="text-2xl md:text-3xl font-extrabold text-white mb-8 text-center animate-fade-in-up">Aircraft Operation Summary</h2>
<form id="aos-form" action="{{ url('/report/aos') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-2xl border backdrop-blur-md animate-fade-in-up delay-100">
    @csrf

    <!-- Baris dropdown -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8 mb-10 animate-fade-in-up delay-200">
        <!-- Periode -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-300">
            <label for="period" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">Periode</label>
            <select name="period" id="period" class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Periode</option>
                @foreach(collect($periods)->sortBy('formatted') as $period)
                    <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Operator -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-400">
            <label for="operator-dropdown" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">Operator</label>
            <select name="operator" id="operator-dropdown" class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Operator</option>
                @foreach(collect($operators)->filter(fn($o) => !empty($o->Operator))->sortBy('Operator') as $operator)
                    <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                @endforeach
            </select>
        </div>

        <!-- Aircraft Type -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-500">
            <label for="aircraft-type-dropdown" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">AC Type</label>
            <select name="aircraft_type" id="aircraft-type-dropdown" class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Aircraft Type</option>
                @foreach(collect($aircraftTypes)->sortBy('ACType') as $type)
                    <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tombol di Tengah -->
    <div class="flex justify-center mt-8 animate-fade-in-up delay-600">
        <button id="display-report-btn" type="submit"
            class="bg-gradient-to-r from-[#0572a6] to-[#6ba539] hover:from-[#035c85] hover:to-[#55882c] text-white font-bold py-3 px-10 rounded-2xl shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#0572a6] flex items-center gap-2 group">
            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16m-7-7l7 7-7 7"/>
            </svg>
            <span>Display Report</span>
        </button>
    </div>
</form>

<!-- Pesan di bawah form dengan animasi -->
<div class="mt-8 flex justify-center animate-fade-in-up delay-700">
    <div class="text-lg text-white px-6 py-4 transition-all duration-700 text-center font-semibold"
         style="min-width:320px;">
        Please select Periode, Operator, and Aircraft Type to display the report.
    </div>
</div>

<style>
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(32px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fade-in-up 0.7s cubic-bezier(.4,2,.6,1) both;
}
.delay-100 { animation-delay: 0.1s !important; }
.delay-200 { animation-delay: 0.2s !important; }
.delay-300 { animation-delay: 0.3s !important; }
.delay-400 { animation-delay: 0.4s !important; }
.delay-500 { animation-delay: 0.5s !important; }
.delay-600 { animation-delay: 0.6s !important; }
.delay-700 { animation-delay: 0.7s !important;
