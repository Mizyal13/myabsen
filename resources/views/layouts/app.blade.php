<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ Session::token() }}"> 
        <title>MyAbsen</title>
        {{-- favicon --}}
        <link rel="icon" href="/img/partner.png">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- Font Awesome -->
        <link
            rel="stylesheet"
            href="/plugins/fontawesome-free/css/all.min.css"
        />
        <!-- Ionicons -->
        <link
            rel="stylesheet"
            href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"
        />
        {{-- Font awesome 4.7 --}}
        <link rel="stylesheet" href="/dist/css/font-awesome.min.css">
        <!-- Tempusdominus Bbootstrap 4 -->
        
        <!-- Theme style -->
        <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
        <!-- overlayScrollbars -->
        <link
            rel="stylesheet"
            href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"
        />
        
        <!-- summernote -->
        <link rel="stylesheet" href="/plugins/summernote/summernote-bs4.css" />
        <!-- Google Font: Source Sans Pro -->
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet"
        />
    <!-- DataTables -->
    <link
    rel="stylesheet"
    href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css"
    />
    <link
    rel="stylesheet"
    href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css"
    />
    <link
    rel="stylesheet"
    href="/DataTables/Buttons-1.6.3/css/buttons.dataTables.min.css"
    />
    <!-- daterange picker -->
    <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
    {{-- <link rel="stylesheet" href="/css/daterangepicker.css"> --}}
    <link rel="stylesheet" href="/css/myabsen-theme.css">
    </head>
    @php
        $myabsenBodyClasses = trim($__env->yieldContent('body-classes'));
    @endphp
        @guest

        <body class="{{ $myabsenBodyClasses ?: 'hold-transition login-page theme-dark' }}">
            {{-- If user is not logged in --}}
            @yield('content')

        @else
        @if (Route::currentRouteName() == 'password.request' || Route::currentRouteName() == 'password.reset' || Route::currentRouteName() == 'password.confirm')
        <body class="{{ $myabsenBodyClasses ?: 'hold-transition login-page theme-dark' }}">
            {{-- If user is not logged in --}}
            @yield('content')
        @else
        <body class="{{ $myabsenBodyClasses ?: 'hold-transition sidebar-mini layout-fixed theme-dark' }}">

            <div class="wrapper">
                {{-- navbar include --}}
                @include('includes.navbar')
                @include('includes.main_sidebar')
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                @yield('content')
                </div>
                <footer class="main-footer">
                    <strong>
                        &copy; @php echo date('Y') @endphp MyAbsen. Semua hak dilindungi.
                    </strong>
                    <div class="float-right d-none d-sm-inline-block">
                        <b>Versi</b> 1.0.0
                    </div>
                </footer>
                <!-- Control Sidebar -->
                <aside class="control-sidebar control-sidebar-dark">
                    <!-- Control sidebar content goes here -->
                </aside>
                <!-- /.control-sidebar -->
            </div>
            <!-- ./wrapper -->
        @endif
        
        @endguest
        

        <!-- jQuery -->
        <script src="/plugins/jquery/jquery.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="/plugins/jquery-ui/jquery-ui.min.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge("uibutton", $.ui.button);
        </script>
        <!-- Bootstrap 4 -->
        <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        
        <!-- Summernote -->
        <script src="/plugins/summernote/summernote-bs4.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="/dist/js/adminlte.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        {{-- <script src="/dist/js/pages/dashboard.js"></script> --}}
        {{-- font awesome --}}
        {{-- <script src="https://use.fontawesome.com/2d4c4e3d51.js"></script> --}}
        <!-- AdminLTE for demo purposes -->
        <script src="/dist/js/demo.js"></script>
        @auth
            <!-- DataTables -->
            <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
            <script src="/plugins/datatables-colreorder/js/dataTables.colReorder.min.js"></script>
            <script src="/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
            <script src="/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
            <script src="/plugins/jszip/jszip.min.js"></script>
            <script src="/plugins/pdfmake/pdfmake.min.js"></script>
            <script src="/plugins/pdfmake/vfs_fonts.js"></script>
            <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
            <script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
            <script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
            <!-- InputMask -->
            <script src="/plugins/moment/moment.min.js"></script>
            <script src="/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
            <!-- date-range-picker -->
            <script src="/plugins/daterangepicker/daterangepicker.js"></script>
            {{-- <script src="/js/daterangepicker.js"></script> --}}
            {{-- <script src="/js/moment.min.js"></script> --}}
        @endauth
        <script>
            (function () {
                const storageKey = 'myabsen-theme';
                const prefersDark = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
                const body = document.body;

                function storedTheme() {
                    const value = window.localStorage ? localStorage.getItem(storageKey) : null;
                    if (value === 'light' || value === 'dark') {
                        return value;
                    }
                    return prefersDark && prefersDark.matches ? 'dark' : 'light';
                }

                function currentTheme() {
                    return body.classList.contains('theme-light') ? 'light' : 'dark';
                }

                function applyTheme(theme) {
                    body.classList.remove('theme-dark', 'theme-light');
                    body.classList.add(theme === 'light' ? 'theme-light' : 'theme-dark');
                }

                function persist(theme) {
                    if (window.localStorage) {
                        localStorage.setItem(storageKey, theme);
                    }
                }

                applyTheme(storedTheme());

                document.addEventListener('DOMContentLoaded', function () {
                    const toggleBtn = document.getElementById('themeToggle');
                    const toggleIcon = document.getElementById('themeToggleIcon');
                    const toggleLabel = document.getElementById('themeToggleLabel');

                    function syncUI(theme) {
                        if (!toggleIcon || !toggleLabel) {
                            return;
                        }
                        if (theme === 'light') {
                            toggleIcon.classList.remove('fa-moon');
                            toggleIcon.classList.add('fa-sun');
                            toggleLabel.textContent = 'Mode Terang';
                        } else {
                            toggleIcon.classList.remove('fa-sun');
                            toggleIcon.classList.add('fa-moon');
                            toggleLabel.textContent = 'Mode Gelap';
                        }
                    }

                    syncUI(currentTheme());

                    if (toggleBtn) {
                        toggleBtn.addEventListener('click', function () {
                            const nextTheme = currentTheme() === 'dark' ? 'light' : 'dark';
                            applyTheme(nextTheme);
                            persist(nextTheme);
                            syncUI(nextTheme);
                        });
                    }

                    if (window.jQuery) {
                        window.jQuery('.modal').each(function () {
                            const $modal = window.jQuery(this);
                            if (!$modal.parent().is('body')) {
                                $modal.appendTo('body');
                            }
                        });
                    }

                    const handlePrefersChange = function (event) {
                        const nextTheme = event.matches ? 'dark' : 'light';
                        const stored = window.localStorage ? localStorage.getItem(storageKey) : null;
                        if (!stored) {
                            applyTheme(nextTheme);
                            syncUI(nextTheme);
                        }
                    };

                    if (prefersDark && prefersDark.addEventListener) {
                        prefersDark.addEventListener('change', handlePrefersChange);
                    } else if (prefersDark && prefersDark.addListener) {
                        prefersDark.addListener(handlePrefersChange);
                    }
                });
            })();
        </script>
        @yield('extra-js')
    </body>
</html>
