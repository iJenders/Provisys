<?php

$ENV = [
    /// Configuración de la aplicación
    'APP_NAME' => 'MyApp',
    'APP_ENV' => 'local',
    'APP_DEBUG' => true,

    // Configuración de la API
    'JWT_SECRET' => "NWZiNWNjZGEtZWIxOC00ZjgxLTg3YTgtNTNiOTE2MjEyN2I2NjgxZmZkMmNjNDdhODMuOTE5NzY5Mjc",

    // Configuración de la base de datos
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASSWORD' => '',
    'DB_NAME' => 'provisys',
    'DB_PORT' => 3306,

    // Configuración de encriptación de contraseñas
    'PASSWORD_HASH_SECRET' => 'WWw5zKYv0Ykwu8UYCcPiwR2KGNjXCkCmErf0iGV9M9oLyJJ3P2D6WTGC05liact06HIVCZVx1pbY30Z',
    'PASSWORD_HASH_ALGO' => 'sha256',

    // Carpeta para almacenar los archivos
    'UPLOAD_FOLDER' => 'C:\\productImages\\',
];
