<!-- Download Reports Section -->
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-file-download mr-3 text-emerald-600"></i>
            Pakua Ripoti za Makampuni (PDF)
        </h3>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.reports.download-companies') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Period Selection -->
                <div>
                    <label for="period" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-emerald-600 text-sm"></i>
                        <span>Chagua Kipindi</span>
                    </label>
                    <select name="period" id="period" 
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white">
                        <option value="today">📅 Leo</option>
                        <option value="yesterday">📅 Jana</option>
                        <option value="this_week">📅 Wiki Hii</option>
                        <option value="last_week">📅 Wiki Iliyopita</option>
                        <option value="this_month">📅 Mwezi Huu</option>
                        <option value="last_month">📅 Mwezi Ulipita</option>
                        <option value="all">📊 Yote</option>
                    </select>
                </div>

                <!-- Download Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2 font-medium">
                        <i class="fas fa-file-pdf text-sm"></i>
                        <span>Pakua PDF</span>
                    </button>
                </div>
            </div>

            <!-- Quick Download Buttons -->
            <div class="border-t border-gray-200 pt-6">
                <p class="text-sm font-semibold text-gray-700 mb-4">Pakua Haraka:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'today']) }}" 
                       class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="font-medium">Leo</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'this_week']) }}" 
                       class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="font-medium">Wiki Hii</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'this_month']) }}" 
                       class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="font-medium">Mwezi Huu</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.download-companies', ['period' => 'all']) }}" 
                       class="bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl transition-all duration-200 text-center group">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="font-medium">Yote</span>
                        </div>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>