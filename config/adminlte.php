<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Sistema de Quejas y Sugerencias',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b style="font-size:17px;white-space:normal;line-height:1.4;display:inline-block;max-width:160px;">Quejas y Sugerencias de Mejora</b>',
    'logo_img' => 'vendor/adminlte/dist/img/logoeje.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logoeje.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logoeje.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => true,
    'usermenu_profile_url' => 'user/profile',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-danger',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => null,
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => 'user/profile',
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text'   => 'Inicio',
            'url'    => 'dashboard',
            'icon'   => 'fas fa-fw fa-tachometer-alt',
        ],

        // — Mi Cuenta —
        [
            'header' => 'MI CUENTA',
        ],
        [
            'text'   => 'Editar Perfil',
            'url'    => 'user/profile',
            'icon'   => 'fas fa-fw fa-user-edit',
            'active' => ['user/profile'],
        ],

        // — Administración —
        [
            'header' => 'ADMINISTRACIÓN',
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Usuarios',
            'url'    => 'admin/usuarios',
            'icon'   => 'fas fa-fw fa-users-cog',
            'active' => ['admin/usuarios*'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Visitantes',
            'url'    => 'admin/visitantes',
            'icon'   => 'fas fa-fw fa-eye',
            'active' => ['admin/visitantes*'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Buzón de Quejas Derivadas',
            'url'    => 'admin/buzon-quejas',
            'icon'   => 'fas fa-fw fa-envelope',
            'active' => ['admin/buzon-quejas*'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Gestión de Solicitudes',
            'url'    => 'admin/gestion-quejas',
            'icon'   => 'fas fa-fw fa-clipboard-list',
            'active' => ['admin/gestion-quejas*'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Histórico de Flujo',
            'url'    => 'admin/historico-flujo',
            'icon'   => 'fas fa-fw fa-stream',
            'active' => ['admin/historico-flujo*'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Historial General',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'administrar-usuarios',
        ],
        [
            'text'   => 'Registro de Actividad',
            'url'    => 'admin/registro-actividad',
            'icon'   => 'fas fa-fw fa-shield-alt',
            'active' => ['admin/registro-actividad*'],
            'can'    => 'administrar-usuarios',
        ],
        // — Jefe de Unidad —
        [
            'header' => 'JEFATURA',
            'can'    => 'menu-jefatura',
        ],
        [
            'text'   => 'Buzón de Quejas Pendientes',
            'url'    => 'quejas/jefe',
            'icon'   => 'fas fa-fw fa-envelope',
            'active' => ['quejas/jefe'],
            'can'    => 'menu-jefatura',
        ],
        [
            'text'   => 'Control de Flujo',
            'url'    => 'quejas/control-flujo',
            'icon'   => 'fas fa-fw fa-stream',
            'active' => ['quejas/control-flujo'],
            'can'    => 'menu-jefatura',
        ],
        [
            'text'   => 'Historial de Mi Unidad',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-jefatura',
        ],

        // — Coordinador —
        [
            'header' => 'COORDINACIÓN',
            'can'    => 'menu-coordinacion',
        ],
        [
            'text'   => 'Buzón de Quejas Pendientes',
            'url'    => 'quejas/coordinador',
            'icon'   => 'fas fa-fw fa-envelope',
            'active' => ['quejas/coordinador'],
            'can'    => 'menu-coordinacion',
        ],
        [
            'text'   => 'Historial de Solicitudes',
            'url'    => 'quejas/coordinador-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/coordinador-historial'],
            'can'    => 'menu-coordinacion',
        ],
        [
            'text'   => 'Historial Completo',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-stream',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-coordinacion',
        ],

        // — Revisores —
        [
            'header' => 'REVISIÓN',
            'can'    => 'menu-revision',
        ],
        [
            'text'   => 'Buzón de Quejas Pendientes',
            'url'    => 'quejas/revision',
            'icon'   => 'fas fa-fw fa-search',
            'active' => ['quejas/revision'],
            'can'    => 'menu-revision',
        ],
        [
            'text'   => 'Control de Flujo',
            'url'    => 'quejas/control-flujo-revision',
            'icon'   => 'fas fa-fw fa-stream',
            'active' => ['quejas/control-flujo-revision'],
            'can'    => 'menu-revision',
        ],
        [
            'text'   => 'Mi Historial',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-revision',
        ],
        [
            'text'   => 'Buzón Revista',
            'url'    => 'quejas/buzon-revista',
            'icon'   => 'fas fa-fw fa-book-open',
            'active' => ['quejas/buzon-revista*'],
            'can'    => 'menu-buzon-revista',
        ],

        // — Usuario EJE con comisión —
        [
            'header' => 'COMISIÓN',
            'can'    => 'menu-comision-eje',
        ],
        [
            'text'   => 'Buzón de Quejas Pendientes',
            'url'    => 'quejas/revision',
            'icon'   => 'fas fa-fw fa-envelope',
            'active' => ['quejas/revision'],
            'can'    => 'menu-comision-eje',
        ],
        [
            'text'   => 'Buzón Revista',
            'url'    => 'quejas/buzon-revista',
            'icon'   => 'fas fa-fw fa-book-open',
            'active' => ['quejas/buzon-revista*'],
            'can'    => 'menu-comision-eje',
        ],
        [
            'text'   => 'Mi Historial',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-comision-eje',
        ],

        // — Reportes —
        [
            'header' => 'REPORTES',
            'can'    => 'ver-reportes',
        ],
        [
            'text'   => 'Reporte General',
            'url'    => 'admin/reporte-general',
            'icon'   => 'fas fa-fw fa-chart-bar',
            'active' => ['admin/reporte-general*'],
            'can'    => 'ver-reportes',
        ],
        [
            'text'   => 'Reporte Estadístico',
            'url'    => 'admin/reporte-estadistico',
            'icon'   => 'fas fa-fw fa-chart-pie',
            'active' => ['admin/reporte-estadistico*'],
            'can'    => 'ver-reportes',
        ],
        [
            'text'   => 'Tiempos de Atención',
            'url'    => 'admin/reporte-tiempos',
            'icon'   => 'fas fa-fw fa-stopwatch',
            'active' => ['admin/reporte-tiempos*'],
            'can'    => 'ver-reportes',
        ],
        [
            'text'   => 'Atención Individual',
            'url'    => 'admin/reporte-atencion-individual',
            'icon'   => 'fas fa-fw fa-user-clock',
            'active' => ['admin/reporte-atencion-individual*'],
            'can'    => 'ver-reportes',
        ],

        // — Visitante: enviar y ver solicitudes —
        [
            'header' => 'SOLICITUDES',
            'can'    => 'menu-solicitudes',
        ],
        [
            'text'   => 'Nueva Solicitud',
            'url'    => 'quejas/nueva',
            'icon'   => 'fas fa-fw fa-plus-circle',
            'active' => ['quejas/nueva'],
            'can'    => 'menu-solicitudes',
        ],
        [
            'text'   => 'Ver mis Solicitudes',
            'url'    => 'quejas/mis-solicitudes',
            'icon'   => 'fas fa-fw fa-list-alt',
            'active' => ['quejas/mis-solicitudes'],
            'can'    => 'menu-solicitudes',
        ],
        [
            'text'   => 'Mi Historial',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-solicitudes',
        ],

        // — Docente: buzón de quejas derivadas —
        [
            'header' => 'SOLICITUDES',
            'can'    => 'menu-docente',
        ],
        [
            'text'   => 'Buzón de Quejas Pendientes',
            'url'    => 'quejas/mis-solicitudes',
            'icon'   => 'fas fa-fw fa-envelope',
            'active' => ['quejas/mis-solicitudes'],
            'can'    => 'menu-docente',
        ],
        [
            'text'   => 'Mi Historial',
            'url'    => 'quejas/mi-historial',
            'icon'   => 'fas fa-fw fa-history',
            'active' => ['quejas/mi-historial'],
            'can'    => 'menu-docente',
        ],

        // — Usuario EJE (sin comisión) —
        [
            'header' => 'MI CUENTA',
            'can'    => 'menu-usuario-eje',
        ],
        [
            'text'   => 'Inicio',
            'url'    => 'dashboard',
            'icon'   => 'fas fa-fw fa-home',
            'active' => ['dashboard'],
            'can'    => 'menu-usuario-eje',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
