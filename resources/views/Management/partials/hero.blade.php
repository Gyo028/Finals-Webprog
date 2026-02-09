{{-- 
    Acts as the primary dashboard header, providing a high-level overview 
    and the main navigation toggle for the management area.
--}}

<div class="mgmt-hero-card" style="background: #111; color: white; padding: 40px; border-radius: 15px;">
    <h1>Management Panel</h1>
    <p>Switch between managing your active bookings and your service pricing.</p>
    
    <div class="hero-tabs" style="margin-top: 20px; display: flex; gap: 20px;">
        {{-- BOOKINGS TAB --}}
        {{-- Route Parameterization: Passes a query string (['tab' => '...']) 
        to the dashboard route to filter content without needing separate pages--}}
        <a href="{{ route('management.dashboard', ['tab' => 'bookings']) }}" 
           style="color: {{ request('tab', 'bookings') == 'bookings' ? '#fff' : '#888' }}; 
                  text-decoration: none; 
                  border-bottom: {{ request('tab', 'bookings') == 'bookings' ? '2px solid #fff' : 'none' }}; 
                  padding-bottom: 5px;
                  font-weight: bold;
                  transition: all 0.3s ease;">
           <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i>Bookings
        </a>

        {{-- OFFERINGS TAB --}}
        <a href="{{ route('management.dashboard', ['tab' => 'offerings']) }}" 
           style="color: {{ request('tab') == 'offerings' ? '#fff' : '#888' }}; 
                  text-decoration: none; 
                  border-bottom: {{ request('tab') == 'offerings' ? '2px solid #fff' : 'none' }}; 
                  padding-bottom: 5px;
                  font-weight: bold;
                  transition: all 0.3s ease;">
           <i class="fa-solid fa-tags" style="margin-right: 8px;"></i>Offerings (Events, Pax, Services)
        </a>
    </div>
</div>