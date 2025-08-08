{{-- Removed the mb-6 from this outermost div --}}
<div>
    <div class="border-b-2 border-gray-200 relative overflow-hidden">
        <nav class="flex overflow-x-auto scroll-smooth snap-x space-x-4 snap-mandatory px-6"> {{-- Added px-6 here --}}
            {{-- Example Tab Button --}}
            <a href="{{ route('account.elements') }}">
                <button type="button"
                    class="tab-button whitespace-nowrap py-2
                    font-medium text-sm focus:outline-none transition-colors
                    duration-200
                    @if ($activeTab === 'elements')
                        text-blue-600 bg-white rounded-t-lg border-b-2 border-blue-500 {{-- Active tab styles --}}
                    @else
                        text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent {{-- Inactive tab styles --}}
                    @endif">                    
                    <span class="w-full block px-4">
                        Elements
                    </span>                    
                </button>
            </a>

            <a href="{{ route('configuration.index') }}">
                <button type="button"
                    class="tab-button whitespace-nowrap py-2
                    font-medium text-sm focus:outline-none transition-colors
                    duration-200
                    @if ($activeTab === 'configuration')
                        text-blue-600 bg-white rounded-t-lg border-b-2 border-blue-500
                    @else
                        text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent
                    @endif">                
                    <span class="w-full block px-4">
                        Configuration
                    </span>
                </button>
            </a>

            @if ($user->name == 'administrator')
            <a href="{{ route('logs.index') }}">
                <button type="button"
                    class="tab-button whitespace-nowrap py-2
                    font-medium text-sm focus:outline-none transition-colors
                    duration-200
                    @if ($activeTab === 'logs')
                        text-blue-600 bg-white rounded-t-lg border-b-2 border-blue-500
                    @else   
                        text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent
                    @endif">
                    
                    <span class="w-full block px-4"> 
                        Logs
                    </span>
                    
                </button>
            </a>
            @endif
        </nav>
        <div class="absolute inset-y-0 left-0 w-8 pointer-events-none tab-scroll-indicator-left"></div>
        <div class="absolute inset-y-0 right-0 w-8 pointer-events-none tab-scroll-indicator-right"></div>
    </div>
</div>