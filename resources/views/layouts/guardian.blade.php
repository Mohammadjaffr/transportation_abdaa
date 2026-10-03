<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width,
                 initial-scale=1,
                 maximum-scale=1,
                 user-scalable=no,
                 viewport-fit=cover">

    <title>
        بوابة ولي الأمر | نظام الإبداع
    </title>


    {{-- Bootstrap 5 RTL --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">


    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        body {
            background-color: #f3f4f6;
            font-family:
                'Segoe UI',
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            padding-bottom: 85px;

            -webkit-tap-highlight-color:
                transparent;
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .guardian-header {

            background: #ffffff;

            padding: 16px 20px;

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 1020;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.04);

            margin-bottom: 20px;
        }


        .guardian-header h5 {

            font-size: 1.1rem;

            color: #1f2937;
        }


        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        .logout-btn {

            color: #ef4444;

            background: #fee2e2;

            padding: 8px;

            border-radius: 50%;

            width: 38px;

            height: 38px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            text-decoration: none;
        }


        /*
        |--------------------------------------------------------------------------
        | Cards
        |--------------------------------------------------------------------------
        */

        .card {

            border: none !important;

            border-radius:
                16px !important;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.04) !important;
        }


        /*
        |--------------------------------------------------------------------------
        | Bottom Navigation
        |--------------------------------------------------------------------------
        */

        .bottom-nav {

            position: fixed;

            bottom: 0;

            left: 0;

            right: 0;

            background: #ffffff;

            box-shadow:
                0 -4px 15px rgba(0, 0, 0, 0.06);

            z-index: 1030;

            display: flex;

            justify-content:
                space-around;

            padding:
                12px 0 calc(12px + env(safe-area-inset-bottom));

            border-top:
                1px solid #f1f1f1;
        }


        .guardian-nav-item {

            text-align: center;

            color: #9ca3af;

            text-decoration: none;

            font-size: 0.78rem;

            flex: 1;

            transition:
                0.2s all;

            display: flex;

            flex-direction: column;

            align-items: center;
        }


        .guardian-nav-item i {

            display: block;

            font-size: 1.3rem;

            margin-bottom: 5px;
        }


        .guardian-nav-item.active {

            color: #198754;

            font-weight: 700;

            transform:
                translateY(-2px);
        }
    </style>


    @livewireStyles

</head>


<body>


    {{-- Header --}}
    <div class="guardian-header">

        <h5 class="mb-0 fw-bold">

            <i class="fas fa-users
                       text-success
                       me-2"></i>

            بوابة ولي الأمر

        </h5>


        {{-- Logout --}}

        <a href="{{ route('logout') }}" class="logout-btn"
            onclick="
                event.preventDefault();
                document
                    .getElementById(
                        'guardian-logout-form'
                    )
                    .submit();
            ">

            <i class="fas fa-power-off"></i>

        </a>


        <form id="guardian-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">

            @csrf

        </form>

    </div>



    {{-- محتوى الصفحة --}}
    {{ $slot }}



    {{-- Bottom Navigation --}}
    <div class="bottom-nav">


        {{-- الرئيسية --}}
        <a href="{{ route('guardian.dashboard') }}"
            class="
            guardian-nav-item
            {{ request()->routeIs('guardian.dashboard') ? 'active' : '' }}
        ">

            <i class="fas fa-home"></i>

            <span>
                الرئيسية
            </span>

        </a>



        {{-- سجل الحضور --}}
        <a href="{{ route('guardian.history') }}"
            class="
            guardian-nav-item
            {{ request()->routeIs('guardian.history') ? 'active' : '' }}
        ">

            <i class="fas fa-calendar-check"></i>

            <span>
                الحضور
            </span>

        </a>


    </div>



    @livewireScripts


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        document.addEventListener(
            'livewire:initialized',
            () => {

                Livewire.on(
                    'show-toast',
                    ({
                        type,
                        message
                    }) => {

                        Swal.fire({

                            toast: true,

                            position: 'top',

                            icon: type ||
                                'success',

                            title: message ||
                                'تمت العملية',

                            showConfirmButton: false,

                            timer: 3000,

                        });

                    }
                );

            }
        );
    </script>


</body>

</html>
