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
    '/products/image' => [
        'action' => ['ProductsController', 'getImage'],
    ],
    '/reports/1' => [
        'action' => ['ReportsController', 'actualInventoryValue'],
    ],
    '/reports/2' => [
        'action' => ['ReportsController', 'providersRanking'],
    ],
    '/reports/3' => [
        'action' => ['ReportsController', 'ingressesEgresses'],
    ],
    '/reports/4' => [
        'action' => ['ReportsController', 'waitingOrders'],
    ],
    '/reports/5' => [
        'action' => ['ReportsController', 'inventoryEntry'],
    ],
    '/reports/6' => [
        'action' => ['ReportsController', 'inventoryExit'],
    ]
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
    '/profile/update' => [
        'action' => ['ProfileController', 'update'],
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
    '/manufacturers' => [
        'action' => ['ManufacturersController', 'getManufacturers'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_manufacturer']]
        ]
    ],
    '/manufacturers/create' => [
        'action' => ['ManufacturersController', 'createManufacturer'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_manufacturer']]
        ]
    ],
    '/manufacturers/update' => [
        'action' => ['ManufacturersController', 'updateManufacturer'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_manufacturer']]
        ]
    ],
    '/manufacturers/delete' => [
        'action' => ['ManufacturersController', 'deleteManufacturer'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_manufacturer']]
        ]
    ],
    '/ivas' => [
        'action' => ['IVAsController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_iva']]
        ]
    ],
    '/ivas/create' => [
        'action' => ['IVAsController', 'createIVA'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_iva']]
        ]
    ],
    '/ivas/update' => [
        'action' => ['IVAsController', 'updateIVA'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_iva']]
        ]
    ],
    '/ivas/delete' => [
        'action' => ['IVAsController', 'deleteIVA'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_iva']]
        ]
    ],
    '/storages' => [
        'action' => ['StoragesController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_storage']]
        ]
    ],
    '/storages/create' => [
        'action' => ['StoragesController', 'createStorage'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_storage']]
        ]
    ],
    '/storages/update' => [
        'action' => ['StoragesController', 'updateStorage'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_storage']]
        ]
    ],
    '/storages/delete' => [
        'action' => ['StoragesController', 'deleteStorage'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_storage']]
        ]
    ],
    '/products' => [
        'action' => ['ProductsController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_product']]
        ]
    ],
    '/products/shop' => [
        'action' => ['ProductsController', 'getShopProducts'],
        'middlewares' => [
            ['JsonMiddleware'],
        ]
    ],
    '/products/create' => [
        'action' => ['ProductsController', 'createProduct'],
        'middlewares' => [
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_product']]
        ]
    ],
    '/products/update' => [
        'action' => ['ProductsController', 'updateProduct'],
        'middlewares' => [
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_product']]
        ]
    ],
    '/products/delete' => [
        'action' => ['ProductsController', 'deleteProduct'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_product']]
        ]
    ],
    '/products/getCompatibleStorages' => [
        'action' => ['ProductsController', 'getCompatibleStorages'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_product']]
        ]
    ],
    '/products/setCompatibleStorages' => [
        'action' => ['ProductsController', 'setCompatibleStorages'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_product']]
        ]
    ],
    '/products/disableCompatibleStorage' => [
        'action' => ['ProductsController', 'disableCompatibleStorage'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_product']]
        ]
    ],
    '/paymentMethods' => [
        'action' => ['PaymentMethodsController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment_method']]
        ]
    ],
    '/paymentMethods/create' => [
        'action' => ['PaymentMethodsController', 'createPaymentMethod'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_payment_method']]
        ]
    ],
    '/paymentMethods/update' => [
        'action' => ['PaymentMethodsController', 'updatePaymentMethod'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_payment_method']]
        ]
    ],
    '/paymentMethods/delete' => [
        'action' => ['PaymentMethodsController', 'deletePaymentMethod'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_payment_method']]
        ]
    ],
    '/payments' => [
        'action' => ['PaymentsController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/payments/details' => [
        'action' => ['PaymentsController', 'getPaymentDetails'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/payments/verify-installment' => [
        'action' => ['PaymentsController', 'verifyInstallment'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/payments/delete-installment' => [
        'action' => ['PaymentsController', 'deleteInstallment'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/payments/create-installment' => [
        'action' => ['PaymentsController', 'createInstallment'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/roles' => [
        'action' => ['RolesController', 'getRoles'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_role']]
        ]
    ],
    '/roles/all' => [
        'action' => ['RolesController', 'getAllActive'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_role']]
        ]
    ],
    '/roles/get' => [
        'action' => ['RolesController', 'getRole'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_role']]
        ]
    ],
    '/roles/create' => [
        'action' => ['RolesController', 'createRole'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_role']]
        ]
    ],
    '/roles/edit' => [
        'action' => ['RolesController', 'editRole'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['edit_role']]
        ]
    ],
    '/roles/delete' => [
        'action' => ['RolesController', 'deleteRole'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_role']]
        ]
    ],
    '/roles/permissions' => [
        'action' => ['RolesController', 'getRolePermissions'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_role']]
        ]
    ],
    '/roles/permissions/update' => [
        'action' => ['RolesController', 'updateRolePermissions'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['edit_role']]
        ]
    ],
    '/permissions/tree' => [
        'action' => ['PermissionsController', 'getPermissionsTree'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_role']]
        ]
    ],
    '/payments/history' => [
        'action' => ['PaymentsController', 'getPaymentHistory'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_payment']]
        ]
    ],
    '/providers' => [
        'action' => ['ProvidersController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_provider']]
        ]
    ],
    '/providers/create' => [
        'action' => ['ProvidersController', 'createProvider'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_provider']]
        ]
    ],
    '/providers/update' => [
        'action' => ['ProvidersController', 'updateProvider'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_provider']]
        ]
    ],
    '/providers/delete' => [
        'action' => ['ProvidersController', 'deleteProvider'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_provider']]
        ]
    ],
    '/purchases' => [
        'action' => ['PurchasesController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_purchase']]
        ]
    ],
    '/purchases/details' => [
        'action' => ['PurchasesController', 'getPurchaseDetails'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_purchase']]
        ]
    ],
    '/purchases/create' => [
        'action' => ['PurchasesController', 'createPurchase'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_purchase']]
        ]
    ],
    '/purchases/delete' => [
        'action' => ['PurchasesController', 'deletePurchase'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_purchase']]
        ]
    ],
    '/purchases/add-payment' => [
        'action' => ['PurchasesController', 'addPayment'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_purchase']]
        ]
    ],
    '/orders' => [
        'action' => ['OrdersController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_order']]
        ]
    ],
    '/orders/user' => [
        'action' => ['OrdersController', 'getUserOrders'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/products' => [
        'action' => ['OrdersController', 'getProducts'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/create' => [
        'action' => ['OrdersController', 'createOrder'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/bill' => [
        'action' => ['OrdersController', 'billOrder'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/deliver' => [
        'action' => ['OrdersController', 'deliverOrder'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/cancel' => [
        'action' => ['OrdersController', 'cancelOrder'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/orders/registerPayment' => [
        'action' => ['OrdersController', 'registerPayment'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware']
        ]
    ],
    '/wastes' => [
        'action' => ['WastesController', 'getAll'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_waste']]
        ]
    ],
    '/wastes/create' => [
        'action' => ['WastesController', 'createWaste'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_waste']]
        ]
    ],
    '/wastes/delete' => [
        'action' => ['WastesController', 'deleteWaste'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_waste']]
        ]
    ],
    '/employees' => [
        'action' => ['EmployeeController', 'getAllEmployees'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_employee']]
        ]
    ],
    '/employees/create' => [
        'action' => ['EmployeeController', 'createEmployee'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['create_employee']]
        ]
    ],
    '/employees/update' => [
        'action' => ['EmployeeController', 'updateEmployee'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_employee']]
        ]
    ],
    '/employees/delete' => [
        'action' => ['EmployeeController', 'deleteEmployee'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['delete_employee']]
        ]
    ],
    '/clients' => [
        'action' => ['UsersController', 'getAllClients'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['get_user']]
        ]
    ],
    '/clients/update' => [
        'action' => ['UsersController', 'editClient'],
        'middlewares' => [
            ['JsonMiddleware'],
            ['AuthMiddleware'],
            ['PermissionMiddleware', ['update_user']]
        ]
    ]
];