<!-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\combined-content.blade.php -->
<h1 class="text-3xl font-bold mb-8 text-center text-white">Combined AOS & Pilot Report and Technical Delay</h1>

<div class="container mx-auto px-4">
    <form action="{{ route('report.combined.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-md">
        @csrf

        <!-- Baris Dropdown -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
            <!-- Periode -->
            <div class="w-full md:w-1/3">
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                <select name="period" id="period" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
            <div class="w-full md:w-1/3">
                <label for="operator" class="block text-sm font-medium text-gray-700 mb-1">Operator</label>
                <select name="operator" id="operator" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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

            <!-- Aircraft Type AOS (dari TblMasterac) -->
            <div class="w-full md:w-1/3">
                <label for="aircraft_type_aos" class="block text-sm font-medium text-gray-700 mb-1">AC Type (AOS)</label>
                <select name="aircraft_type_aos" id="aircraft_type_aos" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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

            <!-- Aircraft Type Pilot (dari TblPirepSwift) -->
            <div class="w-full md:w-1/3">
                <label for="aircraft_type_pilot" class="block text-sm font-medium text-gray-700 mb-1">AC Type (Pilot)</label>
                <select name="aircraft_type_pilot" id="aircraft_type_pilot" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
        <div class="flex justify-center mt-4">
            <button type="submit" class="bg-[#112955] hover:bg-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md">
                Display Combined Report
            </button>
        </div>
    </form>
</div>

<!-- Display Report Placeholder -->
<div class="mt-6 text-center text-white" id="display-data">
    <p>Please select Periode and Aircraft Type to display the combined report.</p>
</div>

<!-- Fixed JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const operatorSelect = document.getElementById('operator');
        const aircraftTypeSelect = document.getElementById('aircraft_type');
        const periodSelect = document.getElementById('period');
        const pdfButton = document.getElementById('pdf_button');

        function updatePdfForm() {
            document.getElementById('pdf_period').value = periodSelect.value;
            document.getElementById('pdf_operator').value = operatorSelect.value;
            document.getElementById('pdf_aircraft_type').value = aircraftTypeSelect.value;

            // Enable PDF button if period and aircraft_type are selected
            pdfButton.disabled = !(periodSelect.value && aircraftTypeSelect.value);
        }

        // Event listeners
        periodSelect.addEventListener('change', updatePdfForm);
        operatorSelect.addEventListener('change', updatePdfForm);
        aircraftTypeSelect.addEventListener('change', updatePdfForm);

        // AJAX untuk dynamic aircraft types berdasarkan operator
        operatorSelect.addEventListener('change', function() {
            const operator = this.value;
            
            if (operator) {
                fetch(`{{ route('get.aircraft.types') }}?operator=${encodeURIComponent(operator)}`)
                    .then(response => response.json())
                    .then(data => {
                        aircraftTypeSelect.innerHTML = '<option value="">Select Aircraft Type</option>';
                        data.forEach(type => {
                            aircraftTypeSelect.innerHTML += `<option value="${type.ACType}">${type.ACType}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching aircraft types:', error);
                    });
            } else {
                // Reset aircraft types jika operator tidak dipilih
                aircraftTypeSelect.innerHTML = '<option value="">Select Aircraft Type</option>';
                @if(isset($aircraftTypes))
                    @foreach ($aircraftTypes as $type)
                        aircraftTypeSelect.innerHTML += '<option value="{{ $type->ACTYPE }}">{{ $type->ACTYPE }}</option>';
                    @endforeach
                @endif
            }
            updatePdfForm();
        });

        // Initialize
        updatePdfForm();
    });
</script>