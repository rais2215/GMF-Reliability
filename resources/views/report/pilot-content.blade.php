<h1 class="text-3xl font-bold mb-8 text-center text-white">Aircraft Operation Summary & Pilot Report And Technical Delay</h1>

<div class="container mx-auto px-4">
    <form action="{{ url('/report/combined') }}" method="POST" class="bg-white p-6 rounded-xl shadow-md">
        @csrf

        <!-- Baris Dropdown -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
            <!-- Periode -->
            <div class="w-full md:w-1/3">
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                <select name="period" id="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Periode</option>
                    @foreach($periods as $period)
                        <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Operator -->
            <div class="w-full md:w-1/3">
                <label for="operator" class="block text-sm font-medium text-gray-700 mb-1">Operator</label>
                <select name="operator" id="operator" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Operator</option>
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Aircraft Type AOS-->
            <div class="w-full md:w-1/3">
                <label for="aircraft_type" class="block text-sm font-medium text-gray-700 mb-1">AC Type</label>
                <select name="aircraft_type" id="aircraft_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Aircraft Type (AOS)</option>
                    @foreach ($aircraftTypes as $type)
                        <option value="{{ $type->ACTYPE }}">{{ $type->ACTYPE }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Aircraft Type Pilot Report-->
            <div class="w-full md:w-1/3">
                <label for="aircraft_type" class="block text-sm font-medium text-gray-700 mb-1">AC Type</label>
                <select name="aircraft_type" id="aircraft_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Aircraft Type (Pilot Report)</option>
                    @foreach ($aircraftTypes as $type)
                        <option value="{{ $type->ACTYPE }}">{{ $type->ACTYPE }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tombol di Tengah -->
        <div class="flex justify-center mt-4">
            <button type="submit" class="bg-[#112955] hover:bg-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md">
                Display Report
            </button>
        </div>
    </form>
</div>

<!-- Display Report Placeholder -->
<div class="mt-6 text-center text-white" id="display-data">
    <p>Please select Periode, Operator, and Aircraft Type to display the report.</p>
</div>

<!-- JS untuk update aircraft type -->
<script>
    document.getElementById('operator-dropdown').addEventListener('change', function () {
        const operator = this.value;
        const aircraftTypeDropdown = document.getElementById('aircraft-type-dropdown');

        aircraftTypeDropdown.innerHTML = '<option value="">Select Aircraft Type</option>';

        if (operator) {
            fetch(/get-aircraft-types?operator=${operator})
                .then(response => response.json())
                .then(data => {
                    data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.ACType;
                        option.textContent = type.ACType;
                        aircraftTypeDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching aircraft types:', error));
        }
    });
</script>