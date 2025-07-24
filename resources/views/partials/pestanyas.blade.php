                         
    
    <!-- Tabs -->
    <div class="px-6">
        <h3 class="text-md font-medium text-gray-900 mb-4 py-1"></h3>
    <!-- Navigation tabs -->
    <div class="border-b border-gray-200 relative overflow-hidden">
        <nav class="flex overflow-x-auto scroll-smooth snap-x space-x-4 snap-mandatory pb-2" aria-label="Tabs">                    
                <button type="button" 
                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 
                    font-medium text-sm focus:outline-none transition-colors 
                    duration-200 
                    @if ($activeTab === 'elements')
                    border-blue-500 text-blue-600
                    @else
                    border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300
                    @endif">
                    <a href="{{ route('account.elements') }}" class="w-full block">
                        Elements
                    </a>
                </button>




            </nav>
            <!-- Scroll indicators -->
            <div class="absolute inset-y-0 left-0 w-8  pointer-events-none tab-scroll-indicator-left"></div>
            <div class="absolute inset-y-0 right-0 w-8  pointer-events-none tab-scroll-indicator-right"></div>

        </div>
    
