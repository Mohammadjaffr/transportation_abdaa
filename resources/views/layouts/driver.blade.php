<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>بوابة السائق | نظام الإبداع</title>
    
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f3f4f6; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding-bottom: 80px; 
            -webkit-tap-highlight-color: transparent;
        }
        
        .bottom-nav { 
            position: fixed; 
            bottom: 0; left: 0; right: 0; 
            background: #ffffff; 
            box-shadow: 0 -4px 15px rgba(0,0,0,0.06); 
            z-index: 1030; display: flex; justify-content: space-around; 
            padding: 12px 0 calc(12px + env(safe-area-inset-bottom));
            border-top: 1px solid #f1f1f1;
        }
        
        .nav-item { 
            text-align: center; color: #9ca3af; text-decoration: none; 
            font-size: 0.75rem; flex: 1; transition: 0.2s all;
            display: flex; flex-direction: column; align-items: center;
        }
        
        .nav-item i { display: block; font-size: 1.3rem; margin-bottom: 5px; }
        .nav-item.active { color: #2563eb; font-weight: 700; transform: translateY(-2px); }
        .nav-item.active i { filter: drop-shadow(0 2px 4px rgba(37,99,235,0.3)); }
        
        .driver-header { 
            background: #ffffff; padding: 16px 20px; 
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1020; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            margin-bottom: 20px;
        }
        
        .driver-header h5 { font-size: 1.1rem; color: #1f2937; }
        .logout-btn { color: #ef4444; background: #fee2e2; padding: 8px; border-radius: 50%; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; }
        
        /* Mobile specific utility */
        .card { border: none !important; border-radius: 16px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important; }
    </style>
    @livewireStyles
</head>
<body>

    <div class="driver-header">
        <h5 class="mb-0 fw-bold"><i class="fas fa-bus-alt text-primary me-2 ms-1"></i> بوابة الكابتن</h5>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn"><i class="fas fa-power-off"></i></a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

    <!-- Main Content -->
    {{ $slot }}

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="{{ route('driver.dashboard') }}" class="nav-item {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>
        <a href="{{ route('driver.today-trip') }}" class="nav-item {{ request()->routeIs('driver.today-trip', 'driver.attendance') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i>
            <span>التحضير</span>
        </a>
        <a href="{{ route('driver.history') }}" class="nav-item {{ request()->routeIs('driver.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>السجل</span>
        </a>
        <a href="{{ route('driver.profile') }}" class="nav-item {{ request()->routeIs('driver.profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            <span>حسابي</span>
        </a>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-toast', ({ type, message }) => {
                Swal.fire({
                    toast: true, position: 'top', icon: type || 'success',
                    title: message || 'تمت العملية', showConfirmButton: false, timer: 3000
                });
            });
        });
    </script>
</body>
</html>
