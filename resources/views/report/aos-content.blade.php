{{-- aos-content.blade.php awal: --}}
<h1 class="text-3xl font-bold mb-8 text-center">Aircraft Operation Summary</h1>

<div class="container mx-auto">
    <form action="{{ url('/report/aos') }}" method="POST">
        @csrf
        <div class="flex space-x-2">
            <!-- Periode -->
            <div class="flex space-x-1">
                <p class="mt-2">Periode :</p>
                <select name="period" class="form-select">
                    <option value="">Select Periode</option>
                    @foreach($periods as $period)
                        <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
                    @endforeach
                </select>
            </div>
    
            <!-- Operator -->
            <div class="flex space-x-1">
                <p class="mt-2">Operator :</p>
                <select name="operator" class="form-select" id="operator-dropdown">
                    <option value="">Select Operator</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                    @endforeach
                </select>
            </div>
    
            <!-- Aircraft Type -->
            <div class="flex space-x-1">
                <p class="mt-2">AC Type :</p>
                <select name="aircraft_type" class="form-select" id="aircraft-type-dropdown">
                    <option value="">Select Aircraft Type</option>
                    @foreach($aircraftTypes as $type)
                        <option value="{{ $type->ACType }}">{{ $type->ACType }}</option>
                    @endforeach
                </select>
            </div>
    
            <!-- Buttons -->
            <div class="flex space-x-1">
                <x-third-button type="submit">
                    Display Report
                </x-third-button>
                <x-third-button type="submit" name="export_excel">
                    Excel
                </x-third-button>
                <x-third-button type="submit" name="export_pdf">
                    PDF
                </x-third-button>
            </div>
        </div>
    </form>
</div>

<!-- Display Report -->
<div class="mt-4" id="display-data">
    <p>Please Select Periode, Operator and Aircraft Type First</p>
</div>

<!-- Tambahkan script untuk handle perubahan operator -->
<script>
    document.getElementById('operator-dropdown').addEventListener('change', function () {
        const operator = this.value;
        const aircraftTypeDropdown = document.getElementById('aircraft-type-dropdown');
        
        // Kosongkan dropdown AC Type
        aircraftTypeDropdown.innerHTML = '<option value="">Select Aircraft Type</option>';

        if (operator) {
            // Kirim permintaan AJAX
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
