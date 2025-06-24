@echo off

echo Iniciando Servidores...

START "Provisys-Frontend" cmd /c "cd Provisys-Frontend && npm run dev"
echo.
START "Provisys-Backend" cmd /c "cd Provisys-Backend && php -S localhost:3000"
echo.

echo Presiona cualquier tecla para salir...

PAUSE