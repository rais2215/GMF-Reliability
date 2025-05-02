<h1 class="text-3xl font-bold mb-8 text-center">Pilot Report And Technical Delay</h1>

<div class="container mx-auto">
    <form action="{{ url('/report/pilot') }}" method="POST">
        @csrf
        <div class="flex space-x-2">
            <div class="flex space-x-1">
                <p class="mt-2">Periode :</p>
                <select name="period" class="form-select">
                    <option value="">Select Periode</option>
                    @foreach($periods as $period)
                        <option value="{{ $period['original'] }}">{{ $period['formatted'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex space-x-1">
                <p class="mt-2">Operator :</p>
                <select name="operator" class="form-select">
                    <option value="">Select Operator</option>
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->Operator }}">{{ $operator->Operator }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-1">
                <p class="mt-2">AC Type :</p>
                <select name="aircraft_type" class="form-select">
                    <option value="">Select Aircraft Type</option>
                    @foreach ($aircraftTypes as $type)
                        <option value="{{ $type->ACTYPE }}">{{ $type->ACTYPE }}</option>
                    @endforeach
                </select>
            </div>

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
