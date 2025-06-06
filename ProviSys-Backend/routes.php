<?php



// Cada ruta debe tener asociada una acción (controlador y método) y opcionalmente un middleware

/*
    SOBRE LAS ACCIONES:
    - La acción debe ser un array con dos strings: el nombre del controlador y el nombre del método a ejecutar
    - El controlador debe estar definido en la carpeta "controllers" y el método debe ser estático

    SOBRE LOS MIDDLEWARES:
    - Los middlewares están definidos en un arreglo
    - Los middlewares deben estar definidos en la carpeta "middlewares"
    - El middleware se especifica como un arrego con dos elementos:
        * El nombre del middleware, que debe ser el mismo que el nombre del archivo (sin la extensión .php)
        * Un arreglo con los parámetros que se le pasan al middleware (opcional, esto depende del middleware)
    - Los middlewares se ejecutan en el orden en que están definidos en el arreglo
*/



/*
    EJEMPLOS DE RUTA:

    $GET_ROUTES = [
        '/' => [
            'action' => ['HomeController', 'index'],
            'middlewares' => [
                ['JsonMiddleware', []]
            ]
        ],
        '/create-user' => [
            'action' => ['UserController', 'create'],
            'middlewares' => [
                ['JsonMiddleware', []],
                ['AuthMiddleware', []],
                ['PermissionMiddleware', ['create_user', 'admin', 'superadmin', 'verified']]
            ]
        ],
    ];

*/

// RUTAS GET.

$GET_ROUTES = [
    '/' => [
        'action' => ['HomeController', 'index'],
    ],
];

// RUTAS POST

$POST_ROUTES = [
    '/login' => [
        'action' => ['AuthController', 'login'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['GuestMiddleware']
        ]
    ],
    '/register' => [
        'action' => ['AuthController', 'register'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['GuestMiddleware']
        ]
    ],
    '/auth/user' => [
        'action' => ['AuthController', 'user'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/categories' => [
        'action' => ['CategoriesController', 'getCategories'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_category']]
        ]
    ],
    '/categories/edit' => [
        'action' => ['CategoriesController', 'editCategory'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['edit_category']]
        ]
    ],
    '/categories/delete' => [
        'action' => ['CategoriesController', 'deleteCategory'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_category']]
        ]
    ],
    '/categories/create' => [
        'action' => ['CategoriesController', 'createCategory'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_category']]
        ]
    ],
];
?>