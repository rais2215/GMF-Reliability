<h2 class="text-2xl md:text-3xl font-extrabold text-white mb-8 text-center animate-fade-in-up">
    Export AOS & Pilot Report and Technical Delay
</h2>

<form action="{{ route('report.combined.export.pdf') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-2xl border backdrop-blur-md animate-fade-in-up delay-100">
    @csrf

    <!-- Baris Dropdown -->
   <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8 mb-10 animate-fade-in-up delay-200">
        <!-- Periode -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-300">
            <label for="period" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">Periode</label>
            <select name="period" id="period" required class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Periode</option>
                @if(isset($periods) && $periods->count() > 0)
                    @foreach($periods as $period)
                        <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No periods available</option>
                @endif
            </select>
        </div>

        <!-- Operator -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-400">
            <label for="operator" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">Operator</label>
            <select name="operator" id="operator" class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Operator</option>
                @if(isset($operators) && $operators->count() > 0)
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No operators available</option>
                @endif
            </select>
        </div>

        <!-- Aircraft Type AOS -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-500">
            <label for="aircraft_type_aos" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">AC Type (AOS)</label>
            <select name="aircraft_type_aos" id="aircraft_type_aos" required class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Aircraft Type (AOS)</option>
                @if(isset($aircraftTypesFromMaster) && $aircraftTypesFromMaster->count() > 0)
                    @foreach ($aircraftTypesFromMaster as $type)
                        <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No AOS aircraft types available</option>
                @endif
            </select>
        </div>

        <!-- Aircraft Type Pilot -->
        <div class="w-full md:w-1/3 animate-fade-in-up delay-600">
            <label for="aircraft_type_pilot" class="block text-base font-bold mb-2 tracking-wide text-[#0572a6]">AC Type (Pilot)</label>
            <select name="aircraft_type_pilot" id="aircraft_type_pilot" required class="w-full border border-[#0572a6] rounded-xl px-4 py-2 bg-[#e6f4fa] text-[#0572a6] font-semibold focus:ring-2 focus:ring-[#0572a6] focus:outline-none transition-all duration-200 shadow-sm">
                <option value="">Select Aircraft Type (Pilot)</option>
                @if(isset($aircraftTypesFromPirep) && $aircraftTypesFromPirep->count() > 0)
                    @foreach ($aircraftTypesFromPirep as $type)
                        <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
                    @endforeach
                @else
                    <option value="" disabled>No Pilot aircraft types available</option>
                @endif
            </select>
        </div>
    </div>

    <!-- Tombol di Tengah -->
    <div class="flex justify-center mt-8 animate-fade-in-up delay-700">
        <button type="submit"
            class="bg-gradient-to-r from-[#d32f2f] to-[#6ba539] hover:from-[#b71c1c] hover:to-[#55882c] text-white font-bold py-3 px-10 rounded-2xl shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#d32f2f] flex items-center gap-2 group">
            <!-- Changed to a download icon -->
            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/>
            </svg>
            <span>Export to PDF</span>
        </button>
    </div>
</form>

<!-- Pesan di bawah form dengan animasi -->
<div class="mt-8 flex justify-center animate-fade-in-up delay-800">
    <div class="text-lg text-white px-6 py-4 transition-all duration-700 text-center font-semibold"
         style="min-width:320px;">
        Please select Periode, Operator, and Aircraft Type to export the combined report.
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
.delay-700 { animation-delay: 0.7s !important; }
.delay-800 { animation-delay: 0.8s !important; }
</style>

<script>
document.getElementById('operator').addEventListener('change', function () {
    const operator = this.value;
    const aosDropdown = document.getElementById('aircraft_type_aos');
    const pilotDropdown = document.getElementById('aircraft_type_pilot');

    aosDropdown.innerHTML = '<option value="">Select Aircraft Type (AOS)</option>';
    pilotDropdown.innerHTML = '<option value="">Select Aircraft Type (Pilot)</option>';

    if (operator) {
        fetch(`/get-aircraft-types?operator=${operator}&source=master`)
            .then(response => response.json())
            .then(data => {
                data.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.ACType;
                    option.textContent = type.ACType;
                    aosDropdown.appendChild(option);
                });
            });

        fetch(`/get-aircraft-types?operator=${operator}&source=pirep`)
            .then(response => response.json())
            .then(data => {
                data.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.ACType;
                    option.textContent = type.ACType;
                    pilotDropdown.appendChild(option);
                });
            });
    }
});
