<?php

class HTMLTemplate
{
    public static function getTemplate($title, $subtitle, $content)
    {
        return '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>' . $title . '</title>
                <style>
                    body { font-family: sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .title { font-size: 24px; font-weight: bold; }
                    .subtitle { font-size: 18px; }
                    .content { margin-top: 30px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">' . $title . '</div>
                    <div class="subtitle">' . $subtitle . '</div>
                </div>
                <div class="content">
                    ' . $content . '
                </div>
            </body>
            </html>
        ';
    }
}
