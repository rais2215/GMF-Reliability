<h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Aircraft Operation Summary</h1>

<div class="container mx-auto px-4">
    <form action="{{ url('/report/aos') }}" method="POST" class="bg-white p-6 rounded-xl shadow-md">
        @csrf

        <!-- Baris dropdown -->
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
                <label for="operator-dropdown" class="block text-sm font-medium text-gray-700 mb-1">Operator</label>
                <select name="operator" id="operator-dropdown" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Operator</option>
                    @foreach($operators as $operator)
                        @if(!empty($operator->Operator))
                            <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Aircraft Type -->
            <div class="w-full md:w-1/3">
                <label for="aircraft-type-dropdown" class="block text-sm font-medium text-gray-700 mb-1">AC Type</label>
                <select name="aircraft_type" id="aircraft-type-dropdown" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Select Aircraft Type</option>
                    @foreach($aircraftTypes as $type)
                        <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tombol di tengah -->
        <div class="flex justify-center mt-4">
            <x-third-button type="submit" class="px-6">
                Display Report
            </x-third-button>
        </div>
    </form>
</div>

<!-- Display Report Placeholder -->
<div class="mt-6 text-center text-gray-600" id="display-data">
    <p>Please select Periode, Operator, and Aircraft Type to display the report.</p>
</div>

<!-- JS untuk update aircraft type -->
<script>
    document.getElementById('operator-dropdown').addEventListener('change', function () {
        const operator = this.value;
        const aircraftTypeDropdown = document.getElementById('aircraft-type-dropdown');

        aircraftTypeDropdown.innerHTML = '<option value="">Select Aircraft Type</option>';

        if (operator) {
            fetch(`/get-aircraft-types?operator=${operator}`)
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
