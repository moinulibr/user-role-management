@props(['menuItems'])
{{-- 
    Blade Component (x-sidebar)
    এই কম্পোনেন্টটি admin-layout.blade.php থেকে $menuItems ডেটা গ্রহণ করে সাইডবার তৈরি করে।
    এটি resources/views/components/sidebar.blade.php ফাইলে সংরক্ষণ করতে হবে।
--}}
<ul class="nav sidebar-inner" id="sidebar-menu">
    
    {{-- মেনু আইটেমগুলির উপর লুপ --}}
    @foreach ($menuItems as $item)

        {{-- চেক করুন এটি সাবমেনু নাকি সিঙ্গেল মেনু --}}
        @if (isset($item['submenu']))
            
            {{-- ১. সাবমেনু আইটেম (has-sub) --}}
            
            @php
                // সক্রিয় ক্লাস নির্ণয়ের জন্য লজিক: যদি সাবমেনুর কোনো একটি রুটও বর্তমান রুট হয়, তবে গ্রুপটিকে active করুন 
                $isActive = false;
                foreach ($item['submenu'] as $sub) {
                    if (isset($sub['route']) && request()->routeIs($sub['route'] . '*')) {
                        $isActive = true;
                        break;
                    }
                }
                $menuId = \Illuminate\Support\Str::slug($item['title']);
            @endphp
            
            <li class="has-sub {{ $isActive ? 'active' : '' }}">
                
                {{-- প্রধান ড্রপডাউন লিংক --}}
                <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#menu-{{ $menuId }}" 
                   aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="menu-{{ $menuId }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="nav-text">{{ __($item['title']) }}</span> 
                    <b class="caret"></b>
                </a>
                
                {{-- সাবমেনু কন্টেন্ট --}}
                <ul class="collapse {{ $isActive ? 'show' : '' }} submenu" id="menu-{{ $menuId }}" data-parent="#sidebar-menu">
                    <div class="sub-menu">
                        @foreach ($item['submenu'] as $subItem)
                            {{-- প্রতিটি সাব-আইটেম: বর্তমান রুট হলে active ক্লাস যোগ করা হলো --}}
                            <li class="{{ isset($subItem['route']) && request()->routeIs($subItem['route']) ? 'active' : '' }}">
                                <a class="sidenav-item-link" href="{{ isset($subItem['route']) ? route($subItem['route']) : '#' }}">
                                    <span class="nav-text">{{ __($subItem['title']) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </div>
                </ul>
            </li>

        @else
            
            {{-- ২. সিঙ্গেল মেনু আইটেম --}}
            <li class="{{ isset($item['route']) && request()->routeIs($item['route'] . '*') ? 'active' : '' }}">
                <a class="sidenav-item-link" href="{{ isset($item['route']) ? route($item['route']) : '#' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="nav-text">{{ __($item['title']) }}</span>
                </a>
            </li>
        @endif

    @endforeach

</ul>
