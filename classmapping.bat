echo off
set moduleName=%1
set modulePath=./module/%moduleName%

if exist %modulePath%/Module.php (
    echo Generate classmap for module: %moduleName%
    cd %modulePath%
    call ../../vendor/bin/classmap_generator.php.bat
    cd ../../
    echo completed
) else (
    ::echo module '%moduleName%' not exists
    cd module
    for /D %%i in (*) do (
        echo %%i
        cd %%i
        call ../../vendor/bin/classmap_generator.php.bat
        cd ..
    )
    cd ..
)