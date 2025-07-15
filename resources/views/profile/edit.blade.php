<x-app-layout>
    {{-- Page Loader --}}
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center backdrop-blur-2xl bg-white/40 transition-all duration-500">
        <div class="glass-card rounded-3xl shadow-2xl p-12 border border-white/20 max-w-sm w-full mx-4 bg-transparent">
            <div class="text-center space-y-4">
                <span id="loader-text" class="text-2xl font-extrabold" style="color: #0069a1;">Loading Profile...</span>
                <p class="text-base font-bold" style="color: #0069a1;">Please wait while we prepare your data</p>
            </div>
            <div class="mt-6 w-full h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full progress-bar"></div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses & Error --}}
    @if (session('status'))
        <div id="notif-success" class="notif-animate fixed top-8 right-8 z-[9999] flex items-center gap-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl font-semibold border-l-4 border-green-700">
            <svg class="w-6 h-6 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('status') }}</span>
            <button onclick="document.getElementById('notif-success').style.display='none'" class="ml-4 focus:outline-none">
                <svg class="w-5 h-5 text-white/80 hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div id="notif-error" class="notif-animate fixed top-8 right-8 z-[9999] flex items-center gap-3 bg-gradient-to-r from-red-500 to-rose-500 text-white px-6 py-4 rounded-2xl shadow-2xl font-semibold border-l-4 border-red-700">
            <svg class="w-6 h-6 text-white animate-shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button onclick="document.getElementById('notif-error').style.display='none'" class="ml-4 focus:outline-none">
                <svg class="w-5 h-5 text-white/80 hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <x-slot name="header">
        <div class="flex items-center justify-between px-6 py-3 bg-white border-b header-responsive animate-element"
            style="border-color:#006ba1; border-bottom-width:1.5px; border-style:solid; border-bottom-left-radius:1rem; border-bottom-right-radius:1rem; box-shadow:0 1px 4px 0 rgba(0,107,161,0.07);">
            <div class="flex items-center gap-3 flex-wrap">
                <button id="back-button"
                    class="flex items-center gap-2 px-3 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-900 transition btn-enhanced"
                    title="Back to Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span class="font-medium">Back</span>
                </button>
                <h2 class="font-bold text-2xl text-[#006ba1] flex items-center gap-2">
                    <span class="inline-block w-1 h-6 rounded-full bg-[#006ba1]"></span>
                    Profile
                </h2>
            </div>
            <span class="text-base font-semibold text-[#006ba1]">GMF Reliability</span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 py-12 px-4 sm:px-8">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100 animate-element card-enhanced" data-delay="200">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100 animate-element card-enhanced" data-delay="400">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account Button --}}
            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100 animate-element card-enhanced" data-delay="600">
                <div class="max-w-xl mx-auto text-center space-y-6">
                    <p class="text-gray-700 text-base text-justify">
                        Once your account is deleted, all of its resources and data will be
                        <span class="font-semibold text-red-600">permanently deleted</span>.
                        Before deleting your account, please download any data or information that you wish to retain.
                    </p>
                    <button
                        onclick="openDeleteModal()"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-500 text-white font-bold shadow-lg hover:from-red-600 hover:to-rose-600 transition btn-enhanced"
                    >
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete Account -->
    <div id="deleteAccountModal" class="fixed inset-0 z-[9999] items-center justify-center bg-white/60 backdrop-blur-sm hidden">
        <div class="relative bg-white rounded-3xl shadow-2xl border border-blue-100 max-w-md w-full p-8 flex flex-col items-center">
            <div class="flex flex-col items-center text-center space-y-4 w-full">
                <div class="bg-red-100 rounded-full p-3 mb-2">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-red-700">Delete Account?</h3>
                <p class="text-gray-700 text-base text-center">
                    Once deleted, all your data will be <span class="font-semibold text-red-600">permanently removed</span>.<br>
                    Please enter your password to confirm.
                </p>
                <form method="POST" action="{{ route('profile.destroy') }}" class="w-full space-y-4 flex flex-col items-center">
                    @csrf
                    @method('DELETE')
                    <input type="password" name="password" required placeholder="Password"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition input-enhanced bg-white" autocomplete="current-password">
                    <div class="flex justify-between gap-3 mt-2 w-full">
                        <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 to-rose-500 text-white font-bold shadow-lg hover:from-red-600 hover:to-rose-600 transition">Delete Account</button>
                    </div>
                </form>
            </div>
            <button onclick="closeDeleteModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    </div>

    <style>
        /* ==============================================
           LOADER ANIMATIONS - Fixed to match register
        ============================================== */
        @keyframes loader-bounce {
            0%, 100% {
                transform: scaleY(0.3);
                opacity: 0.5;
            }
            50% {
                transform: scaleY(1.2);
                opacity: 1;
            }
        }

        @keyframes progress-fill {
            0% {
                width: 0%;
            }
            100% {
                width: 100%;
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(60px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        /* Notif Animations */
        @keyframes notif-slide-in {
            0% {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            60% {
                opacity: 1;
                transform: translateY(10px) scale(1.03);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes notif-shake {
            0% { transform: translateX(0);}
            20% { transform: translateX(-6px);}
            40% { transform: translateX(6px);}
            60% { transform: translateX(-4px);}
            80% { transform: translateX(4px);}
            100% { transform: translateX(0);}
        }
        .notif-animate {
            animation: notif-slide-in 0.7s cubic-bezier(.4,2,.6,1);
            box-shadow: 0 8px 32px 0 rgba(16, 112, 202, 0.10), 0 2px 8px 0 rgba(0,0,0,0.10);
        }
        .animate-shake {
            animation: notif-shake 0.7s cubic-bezier(.4,2,.6,1);
        }

        /* ==============================================
           LOADER CLASSES
        ============================================== */
        .loader-bar {
            animation: loader-bounce 1.4s infinite ease-in-out;
        }

        .loader-delay-1 {
            animation-delay: 0.16s;
        }

        .loader-delay-2 {
            animation-delay: 0.32s;
        }

        .progress-bar {
            animation: progress-fill 3s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }

        /* ==============================================
           PAGE ANIMATIONS - Simplified and Clear
        ============================================== */
        .animate-element {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-element.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ==============================================
           CARD EFFECTS - Enhanced
        ============================================== */
        .card-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(126, 187, 26, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .card-enhanced:hover::before {
            left: 100%;
        }

        .card-enhanced:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 107, 161, 0.15);
        }

        /* ==============================================
           BUTTON EFFECTS
        ============================================== */
        .btn-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 107, 161, 0.3);
        }

        /* ==============================================
           INPUT ENHANCEMENTS
        ============================================== */
        .input-enhanced {
            transition: all 0.3s ease;
        }

        .input-enhanced:focus {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(126, 187, 26, 0.2);
            border-color: #7EBB1A;
        }

        /* ==============================================
           RESPONSIVE
        ============================================== */
        @media (max-width: 768px) {
            .notif-animate, #notif-success, #notif-error {
                right: 50% !important;
                left: 50% !important;
                top: 1.5rem !important;
                transform: translateX(-50%) !important;
                width: 90vw !important;
                max-width: 95vw !important;
                min-width: 0 !important;
            }
            .header-responsive {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
                padding: 1rem !important;
            }

            .header-responsive h2 {
                font-size: 1.25rem !important;
            }

            .card-enhanced:hover {
                transform: translateY(-2px);
            }
        }
    </style>

    <script>
        function openDeleteModal() {
            const modal = document.getElementById('deleteAccountModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeDeleteModal() {
            const modal = document.getElementById('deleteAccountModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // SIMPLE PAGE LOAD ANIMATIONS
            // ==============================================
            const animateElements = document.querySelectorAll('.animate-element');

            function showElements() {
                animateElements.forEach((element, index) => {
                    const delay = element.dataset.delay || (index * 200);
                    setTimeout(() => {
                        element.classList.add('visible');
                    }, delay);
                });
            }

            // Start animations after page load
            setTimeout(showElements, 100);

            // ==============================================
            // BACK BUTTON WITH LOADER
            // ==============================================
            const backButton = document.getElementById('back-button');
            const pageLoader = document.getElementById('page-loader');

            if (backButton && pageLoader) {
                backButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Show loader
                    pageLoader.classList.remove('hidden');
                    pageLoader.classList.add('flex');

                    // Update loader text
                    const loaderText = document.getElementById('loader-text');
                    if (loaderText) {
                        loaderText.textContent = 'Returning to Dashboard...';
                    }

                    // Navigate after delay
                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    }, 1200);
                });
            }

            // ==============================================
            // INPUT ENHANCEMENTS
            // ==============================================
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.classList.add('input-enhanced');

                input.addEventListener('focus', function() {
                    this.style.borderColor = '#7EBB1A';
                });

                input.addEventListener('blur', function() {
                    this.style.borderColor = '';
                });
            });

            // ==============================================
            // FORM SUBMIT ENHANCEMENT
            // ==============================================
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            submitBtn.style.transform = 'scale(1)';
                        }, 150);
                    }
                });
            });

            // ==============================================
            // SUCCESS/ERROR MESSAGES
            // ==============================================
            const messages = document.querySelectorAll('.alert, .success, .error, [class*="alert"]');
            messages.forEach(message => {
                message.style.animation = 'fade-in 0.5s ease-out';
            });

            // ==============================================
            // NOTIF AUTO HIDE
            // ==============================================
            setTimeout(() => {
                const notifSuccess = document.getElementById('notif-success');
                const notifError = document.getElementById('notif-error');
                if (notifSuccess) {
                    notifSuccess.style.opacity = '0';
                    notifSuccess.style.transform = 'translateY(-30px) scale(0.95)';
                    setTimeout(() => { notifSuccess.style.display = 'none'; }, 400);
                }
                if (notifError) {
                    notifError.style.opacity = '0';
                    notifError.style.transform = 'translateY(-30px) scale(0.95)';
                    setTimeout(() => { notifError.style.display = 'none'; }, 400);
                }
            }, 3000);
        });
    </script>
</x-app-layout>
