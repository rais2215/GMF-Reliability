<!-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\aos-content.blade.php -->
<h1 class="text-3xl font-bold mb-8 text-center text-white">Aircraft Operation Summary</h1>

<div class="container mx-auto px-4">
    <form id="aos-form" action="{{ url('/report/aos') }}" method="POST" class="bg-white p-6 rounded-xl shadow-md">
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

        <!-- Tombol di Tengah -->
        <div class="flex justify-center mt-4">
            <button id="display-report-btn" type="submit" class="bg-[#112955] hover:bg-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-300">
                Display Report
            </button>
        </div>
    </form>
</div>

<!-- Display Report Placeholder -->
<div class="mt-6 text-center text-white" id="display-data">
    <p>Please select Periode, Operator, and Aircraft Type to display the report.</p>
</div>

<!-- Enhanced Loading Skeleton -->
<div id="skeleton-loader" class="mt-6 hidden">
    <div class="container mx-auto px-4">
        <!-- Header Skeleton -->
        <div class="flex justify-between items-center mb-6">
            <div class="h-6 bg-gray-300 rounded animate-pulse w-2/3"></div>
            <div class="flex space-x-2">
                <div class="h-10 bg-gray-300 rounded animate-pulse w-24"></div>
                <div class="h-10 bg-gray-300 rounded animate-pulse w-28"></div>
            </div>
        </div>

        <!-- Loading Progress Bar -->
        <div class="mb-6">
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-1000" style="width: 0%"></div>
            </div>
            <p id="loading-text" class="text-center text-gray-600 mt-2">Preparing data...</p>
        </div>

        <!-- AOS Table Skeleton -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <!-- Table Header Skeleton -->
                <div class="grid grid-cols-14 gap-2 mb-4">
                    <div class="h-10 bg-gray-300 rounded animate-pulse"></div>
                    @for($i = 0; $i < 13; $i++)
                        <div class="h-10 bg-gray-300 rounded animate-pulse"></div>
                    @endfor
                </div>

                <!-- Table Rows Skeleton -->
                @for($row = 0; $row < 20; $row++)
                    <div class="grid grid-cols-14 gap-2 mb-2">
                        <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                        @for($i = 0; $i < 13; $i++)
                            <div class="h-8 bg-gray-100 rounded animate-pulse"></div>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>

        <!-- Additional Loading Animation -->
        <div class="flex justify-center mt-6">
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
</div>

<!-- JS untuk update aircraft type & skeleton loader -->
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

    // Enhanced skeleton loader dengan progress animation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('aos-form');
        const skeletonLoader = document.getElementById('skeleton-loader');
        const displayData = document.getElementById('display-data');
        const submitBtn = document.getElementById('display-report-btn');
        const progressBar = document.getElementById('progress-bar');
        const loadingText = document.getElementById('loading-text');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submit
                
                console.log('Form submitted'); // Debug

                // Validasi form
                const period = document.getElementById('period').value;
                const operator = document.getElementById('operator-dropdown').value;
                const aircraftType = document.getElementById('aircraft-type-dropdown').value;

                if (!period || !operator || !aircraftType) {
                    alert('Please fill all fields before submitting');
                    return false;
                }

                // Set loading flag untuk result page
                sessionStorage.setItem('aosReportLoading', 'true');

                // Show skeleton loader
                console.log('Showing skeleton loader'); // Debug
                
                if (skeletonLoader) {
                    skeletonLoader.classList.remove('hidden');
                }
                
                if (displayData) {
                    displayData.classList.add('hidden');
                }
                
                // Update button state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                
                // Progress animation
                let progress = 0;
                const loadingMessages = [
                    'Preparing data...',
                    'Fetching aircraft information...',
                    'Processing flight hours...',
                    'Calculating metrics...',
                    'Generating report...',
                    'Almost done...'
                ];
                
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(progressInterval);
                        
                        // Submit form after progress complete
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                    
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    
                    // Update loading text
                    const messageIndex = Math.floor((progress / 100) * (loadingMessages.length - 1));
                    if (loadingText) {
                        loadingText.textContent = loadingMessages[messageIndex];
                    }
                }, 300);
            });
        }

        // Reset form state if needed
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Display Report';
                }
                // Hide skeleton
                if (skeletonLoader) {
                    skeletonLoader.classList.add('hidden');
                }
                // Show display data
                if (displayData) {
                    displayData.classList.remove('hidden');
                }
                // Reset progress
                if (progressBar) {
                    progressBar.style.width = '0%';
                }
                if (loadingText) {
                    loadingText.textContent = 'Preparing data...';
                }
            }
        });
    });
</script>