{{-- aos-content.blade.php --}}
<h1 class="text-3xl font-bold mb-8 text-center">Aircraft Operation Summary</h1>

<div class="flex flex-wrap md:flex-nowrap gap-4">
    <!-- Periode -->
    <div class="flex flex-col w-full md:w-1/3">
        <label for="period" class="mb-1">Periode:</label>
        <select name="period" id="period" class="form-select w-full">
            <option value="">Select Periode</option>
            @foreach($periods as $period)
                <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- Operator -->
    <div class="flex flex-col w-full md:w-1/3">
        <label for="operator-dropdown" class="mb-1">Operator:</label>
        <select name="operator" id="operator-dropdown" class="form-select w-full">
            <option value="">Select Operator</option>
            @foreach($operators as $operator)
                @if(!empty($operator->Operator))
                    <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                @endif
            @endforeach
        </select>
    </div>

    <!-- Aircraft Type -->
    <div class="flex flex-col w-full md:w-1/3">
        <label for="aircraft-type-dropdown" class="mb-1">AC Type:</label>
        <select name="aircraft_type" id="aircraft-type-dropdown" class="form-select w-full">
            <option value="">Select Aircraft Type</option>
            @foreach($aircraftTypes as $type)
                <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Buttons -->
<div class="mt-4 flex justify-end">
    <x-third-button type="submit">
        Display Report
    </x-third-button>
</div>


<!-- Display Report -->
<div class="mt-4" id="display-data">
    <p>Please Select Periode, Operator and Aircraft Type First</p>
</div>

<!-- Script handle perubahan operator -->
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
