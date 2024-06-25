<nav
    class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        @if (auth()->user()->hasRole('ContratacionL'))
            <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
            <script>
                function subscribeToPusher() {
                    var pusher = new Pusher('52c212ce563c5534e98c', { // Usa tu clave real aquí
                        cluster: 'us2' // Usa tu cluster real aquí
                    });
                    var channel = pusher.subscribe('my-channel');
                    channel.bind('my-event', function(data) {
                        new Notification('Nuevo Contrato', {
                            body: `La empresa ${data.data.empresa} acaba de publicar un contrato.`,
                            icon: '{{ asset('vendor/adminlte/dist/img/maddi-go.png') }}' // Asegúrate de poner la ruta correcta del icono
                        });
                    });
                }

                if (Notification.permission === "granted") {
                    subscribeToPusher();
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            subscribeToPusher();
                        }
                    });
                }
            </script>
        @endif


        {{-- User menu link --}}
        @if (Auth::user())
            @if (config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
