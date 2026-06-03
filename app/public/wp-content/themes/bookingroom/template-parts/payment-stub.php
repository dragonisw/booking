<div class="max-w-md w-full mx-auto p-1 relative group">
    <!-- Animated Gradient Border -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
    
    <!-- Main Card -->
    <div class="relative bg-white/90 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/50">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Payment Stub</h3>
                <p class="text-sm text-slate-500 font-medium mt-1">Select your preferred method</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Payment Options Form -->
        <form class="space-y-4">
            
            <!-- Credit Card -->
            <label class="relative flex items-center p-4 cursor-pointer rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-500 hover:shadow-lg transition-all duration-300 group/item">
                <input type="radio" name="payment_method" value="credit_card" class="peer sr-only" checked>
                
                <div class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:border-[6px] transition-all duration-300 mr-4"></div>
                
                <div class="flex-1">
                    <h4 class="text-base font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">Credit Card</h4>
                    <p class="text-xs text-slate-500 font-medium">Visa, Mastercard, JCB</p>
                </div>
                
                <div class="flex gap-1">
                    <!-- Visa Icon -->
                    <div class="w-8 h-6 bg-white rounded shadow-sm flex items-center justify-center p-1 border border-slate-100">
                        <svg viewBox="0 0 38 24" class="w-full h-full text-blue-800" fill="currentColor">
                            <path d="M14.9 22H11.5L13.6 2.4H17.1L14.9 22ZM37.9 2.5C37.3 2.3 36.1 2.1 34.6 2.1C31.3 2.1 28.9 3.9 28.8 6.9C28.8 9 30.8 10.2 32.3 10.9C33.9 11.7 34.4 12.2 34.4 12.9C34.4 14 33.1 14.5 31.7 14.5C29.7 14.5 28.6 13.9 27.7 13.5L27 16.9C27.9 17.3 29.6 17.7 31.3 17.7C34.9 17.7 37.2 15.9 37.3 12.8C37.3 10.2 33.7 8.7 33.7 8C33.7 7.4 34.3 6.7 35.5 6.7C36.4 6.7 37.4 7 38 7.3L37.9 2.5ZM26 22H22.9C22.2 22 21.6 21.6 21.3 20.9L18.2 2.4H21.7L22.4 6.1H26.7L27.1 2.4H30.3L26 22ZM23.3 10.6L24.5 4.6L25.9 10.6H23.3ZM12 2.4L8.7 15.7L8.4 14C7.7 11 5.9 8.2 3.1 6.8L3.9 10.7C3.9 10.7 6.4 12.5 7.6 18.6L8.4 22H11.9L12 2.4ZM3 2.4H0.2L0 3.3C1.6 3.7 3.4 4.5 4.5 5.5L2.8 21.9H6.3L10.3 2.4H6.8C6.3 2.4 5.9 2.7 5.7 3.3L3 2.4Z"/>
                        </svg>
                    </div>
                </div>
            </label>

            <!-- Bank Transfer -->
            <label class="relative flex items-center p-4 cursor-pointer rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-500 hover:shadow-lg transition-all duration-300 group/item">
                <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only">
                
                <div class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:border-[6px] transition-all duration-300 mr-4"></div>
                
                <div class="flex-1">
                    <h4 class="text-base font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">Bank Transfer</h4>
                    <p class="text-xs text-slate-500 font-medium">Domestic banks & NAPAS</p>
                </div>
                
                <div class="flex gap-1">
                    <!-- Bank Icon -->
                    <div class="w-10 h-8 bg-white rounded shadow-sm flex items-center justify-center p-1 border border-slate-100">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                    </div>
                </div>
            </label>

            <!-- VNPay/Momo -->
            <label class="relative flex items-center p-4 cursor-pointer rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-500 hover:shadow-lg transition-all duration-300 group/item">
                <input type="radio" name="payment_method" value="ewallet" class="peer sr-only">
                
                <div class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-blue-600 peer-checked:border-[6px] transition-all duration-300 mr-4"></div>
                
                <div class="flex-1">
                    <h4 class="text-base font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">VNPay / Momo</h4>
                    <p class="text-xs text-slate-500 font-medium">Scan QR to pay securely</p>
                </div>
                
                <div class="flex gap-1">
                    <!-- QR/Wallet Icon -->
                    <div class="w-10 h-8 bg-white rounded shadow-sm flex items-center justify-center p-1 border border-slate-100">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </label>

            <!-- Continue Button -->
            <div class="pt-4 mt-4 border-t border-slate-100">
                <button type="button" class="w-full relative group overflow-hidden bg-slate-900 text-white font-bold py-4 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)] transition-all duration-300 flex items-center justify-center gap-2">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative flex items-center gap-2">
                        Proceed to Payment
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </span>
                </button>
            </div>
            
            <!-- Security Note -->
            <div class="flex items-center justify-center gap-2 mt-4 text-xs text-slate-400 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Secured by 256-bit SSL Encryption
            </div>
        </form>
    </div>
</div>
