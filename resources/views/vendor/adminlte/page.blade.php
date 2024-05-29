@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if ($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if ($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if (!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
    <script>
        $(document).ready(function() {
            function checkSession() {
                $.ajax({
                    url: '{{ url("/check-session") }}',
                    method: 'GET',
                    success: function(response) {
                        if (!response.is_logged_in) {
                            Swal.fire({
                                title: 'Sesión caducada',
                                text: 'Su sesión ha caducado. Por favor, inicie sesión de nuevo.',
                                icon: 'warning',
                                confirmButtonText: 'Iniciar sesión',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: '{{ url('/refresh-csrf') }}',
                                        method: 'GET',
                                        success: function(data) {
                                            $('meta[name="csrf-token"]').attr(
                                                'content', data.csrf_token);
                                            window.location.href =
                                                '{{ route('login') }}';
                                        }
                                    });
                                }
                            });
                        }
                    }
                });
            }

            // Check session every 5 minutes (300000 milliseconds)
            setInterval(checkSession, 7200000);
        });
    </script>
@stop
