Para poder trabajar con este proyecto necesitan php versión 8+
Necesitan las herramientas de debug de visual studio
Preferiblemente usa el server intergrado de php, pero si no puedes usar xampp o wamp o cualquier otra cosa
Si quieres coloca la siguiente prompt a chatGPT o a otra IA para que te ayude con todo el proceso:

"Ayúdame a instalar y configurar php en su versión más reciente en windows, quiero poder trabajar en un proyecto que usa php nativo (no frameworks), está usando la librería de mysqli, quiero poder usar las herramientas de debug en VSCdode. De preferencia quiero trabajar con el servidor integrado de php y no con un servidor a parte como XAMPP o WAMP. Quiero que el server se inicie en el puerto 8000"

Puedes enviarle también este README.md para que entienda un poco mejor el proyecto

# Reverie Backend
Bakcend con API Restful del sistema de inventarios Reverie, enfocado en una panadería de nombre Spiral.

## Hecho
- Base de datos en mysql con tablas, vistas, procedimientos y triggers apropiados
- API restful con PHP funcionando con casi completa plenitud
- POST, PUT, GET implementados para Empleados, Productos, Ventas y Reportes
- Serivicio de ruteo apropiado con Service.PHP
- Amplio manejo de errores y validación de datos con retroalimentación positiva para el usuario
- Sistema de inicio de sesión correctamente implementado y funcional
- Revisado y probado en postman, incluyendo casos de prueba
## Documentación
Todo el proyecto está propiamente documentado, sin embargo, por error mío, todos los comentarios fueron hechos en inglés, espero que no sea una dificultad muy grande, siempre pueden preguntarle a algún chatbot por explicación de los códigos o por traducirles los comentarios.

Aún así, como pequeña ayuda, dejo aquí todas los endpoint que se implementaron.

Sin importar el método, todas las llamadas a la API han de ser implementadas a través de la ruta 
> /api/endpoint/service.php?service= 
>
colocar el servicio requerido después de "service="

Antes de empezar a trabajar con la API, es necesario crear la base de datos y posteriormente colocar tu username y tu password en el archivo api/config/connectiontemp.php
Posteriormente cambia el nombre del archivo a connection.php, si no, no va a funcionar nada.

Nótese que para poder llamar a los servicios es necesario iniciar sesión previamente, algunos endpoints sólo pueden ser usados por un administrador indicado por el (sólo administrador), mientras que otros pueden ser usados tanto por administradores como por empleados.
La base de datos incluye una lista de empleados ya definidos con su usario y contraseña, úsenlos para iniciar sesión, preferiblemente como administrador para evitar problemas de acceso. 
### GET
#### service=employee
(sólo administrador)
Obtener todos los empleados en la base de datos

#### service=employee&id=
(sólo administrador)
Obtener la información de un empleado con su id, la id es un número entero

#### service=currentemployee
Obtener los datos del empleado actual que haya iniciado sesión

#### service=product
Obtener todos los productos en la base de datos

#### service=product&id=
Obtener la información del producto con su código, el código es una cadena de texto (ejemplo: dnch1)

#### service=sale
(sólo administrador)
Obtener las últimas 10 ventas registradas

#### service=salesbyemployee&id=
(sólo administrador)
Obtener las últimas ventas hechas por un empleado específico a través de su id (un número entero)

#### service=salesbycurrentemployee
Obtener las últimas ventas realizadas por el empleado que tenga sesión activa en ese momento

#### service=dailyreport
Obtener el reporte diario del día de hoy

#### service=dailyreport&interval=
Obtener el reporte diario dado un intervalo en días, (1, 2, 5, 10, etc)

### POST
#### service=login
Iniciar sesión en el sistema, un body es necesario al hacer la solicitud.
>ejemplo de body: 
{

    "username": "nombre de usuario",
    "password": "contraseña"
}

#### service=employee
(sólo administrador)
Agregar un empleado a la base de datos, todos los campos salvo la foto de perfil son obligatorios de enviar en el body.
>ejemplo de body:
{

    "Name": "Fernando",
    "Surname": "Fernández",
    "Username": "dobleF",
    "Password": "ffffff",
    "Shift": "Lunes-Miércoles",
    "Phone": "7711111111",
    "Photo": ""
}

#### service=product
(sólo administrador)
Agregar un producto nuevo a la base de datos, todos los datos son obligatorios.
>ejemplo de body:
{

    "Code": "cpsm1",
    "Name": "Strawberry Cupcake Medium size",
    "Description": "Medium size Cupcake strawberry flavored with strawberry pieces on top",
    "Amount": 50,
    "Price": 24,
    "Category": 3
}

#### service=sale
Añadir una venta nueva, requiere recibir un json con todos los productos que se vendieron y cuántos se vendieron, no se puede duplicar el producto dentro de una misma venta, sin un sólo producto es incorrecto, la venta no se completa.
>ejemplo de body:
{

    "Products": [
        {
            "Code": "ckch1",
            "Amount": 1
        },
        {
            "Code": "cpsm1",
            "Amount": 7
        },
        {
            "Code": "dnch1",
            "Amount": 4
        }
    ]
}

### PUT
#### service=employee&id=
(sólo administrador)
Editar la información de un empleado en específico con su id. Los datos a enviar en el body no son obligatorios, pero se debe de enviar al menos uno para que sea válida la solicitud. Los parámetros que se pueden editar son:
>['Name', 'Surname', 'Username', 'Password', 'Shift', 'Phone', 'Photo']
>ejemplo de body:
{

    "Username": "Jennyyy",
    "Password": "Liferules"
}

#### service=currentemployee
Permite editar la información del usuario que se encuentre iniciando sesión en ese momento específico. Funciona igual que el endpoint anterior, pero no requiere ninguna id.
>['Name', 'Surname', 'Username', 'Password', 'Shift', 'Phone', 'Photo']
>ejemplo de body:
{

    "Username": "Jennyyy",
    "Password": "Liferules"
}

#### service=employee&id=
(sólo administrador)
Editar la información de un producto en específico con su id. Los datos a enviar en el body no son obligatorios, pero se debe de enviar al menos uno para que sea válida la solicitud. Los parámetros que se pueden editar son:
>['Name', 'Description', 'Price', 'Category', 'Photo', 'Discontinued']

>ejemplo de body:
{

    "Price": 85
}

#### service=incproduct&id=
(sólo administrador)
Permite incrementar el stock de un producto en exactamente una unidad, sólo requiere el código del producto.

#### service=decproduct&id={}&amount={}
(sólo administrador)
Permite disminuir el stock de un producto en una cantidad determinada, se deben de pasar como parámetros el código del producto y la cantidad (número entero) a disminuir. La cantidad (amount) debe de ser menor o igual al stock existente.

### DELETE
No implementado aún

## Por hacer
El backend ya está hecho, pueden probarlo en postaman o en su navegador, para ello cuentan con una copia de la base de datos usada. Sin embargo, aún no existe implementación alguna del frontend (salvo por el inicio de sesión), por lo que les dejo eso a ustedes. Cada ventana dentro del sitio web deberá llamar a su API o APIS correspondientes dependiendo del contexto en el que se encuentren para así poder mostrar datos interactivos y poder hacer consultas y actualizaciones a través de UI.

>
Por ejemplo, la ventana dashboard.php está hecha para los empleados, ahí se llevan a cabo las ventas, podría implementar el endpoint GET para obtener productos, el endpoint POST sale para llevar a cabo ventas y que estas se vean realmente reflejadas en la base de datos. Podrían implementar herramientas visuales para mostrar productos con estado "agotado" con su foto en blanco y negro, etcétera.
>
Otro ejemplo sería el perfil, que podría usar el endpoint para mostrar datos del usuario actual (GET api/endpoint/service.php?service=currentemployee) para mostrar su información, el endponit PUT currentemployee para actualizarla, etc.
>
En general es eso, hacer una implementación entre el frontend y el backend que sea consistente y precisa, nada más.
>
También hace falta implementar un DELETE, este deberá ser un soft delete (pregunten a chatGPT qué significa eso). No lo implementé yo porque quería que fuera implementado en sprigboot como lo pidió el profesor de la clase, por eso podrían intentar hacerlo ustedes y probarlo.

Tienen todo este proyecto para jugar con él.

# Indicaciones especiales
## NO MODIFICAR EL BACKEND 
Déjenlo como está, ya está funcionando bien, ya fue probado, si encuentran algún bug me avisan y si piensan que algo no está funcionando bien primero revisen que no sea un error del frontend. Si mueven el backend sin saber, van a romper la aplicación.
Y si van a modificar o añadir algo al backend
## NO HAGAN COMMIT A LA RAMA PRINCIPAL
Creen sus propias ramas, no vayan a romper todo el trabajo sin darse cuenta.
## MANTENER TODO EL CÓDIGO FRAMEWORK FREE
Nada de node.js, react ni cosas por el estilo, exceptuando springboot que sólo será usado para el softdelete.

Eso es todo. Cualquier duda, escríbanme.

==============================================================================================
==============================================================================================
Code made by Francisco Emmanuel Luna Hidalgo Last checked 25/04/2026 
==============================================================================================
==============================================================================================
Instituto Tecnológico de Pachuca, Ingeniería en Sistemas Computacionales, Programación Web, proyecto final
==============================================================================================
==============================================================================================
                                               .......................                        
                                      ..:-==++=========+=====+=====+=++=-::....               
                                 .:=+===+=====================================+==:....        
                            .    .=+=================================================+:.      
                         ..:..    .++====+==========================================+=.       
                      ..-+===.     .+==-   .:==+=====================================..       
                   ..:++======.     .+=-      ..:+==+=============================+=..        
                 ..-===+=====+:.     .--          .:=+=============================-.         
               ..=============+..      .             .:=======+====================.          
             ..=================.                      ..:+==+===================+.           
            .:===================.                        ..-=+=================+..           
          ..===+++===============:.                          ..=+===============:.            
         .:======================+..                           ..-=======+======.             
        .-=======================+=.                              .:+==========.              
       .=++======+================+=.                               ..=+=+===+..              
      .==================+==========:.           ......::::::......   ..:#%%.=.               
     .===========================+=.==%%%:. ...-=+==+++=========+===-..*%%%+%%%.              
    .-============================.%%%:%%%*.==========================.#%%#+@%%.              
   .:=====+=======================.%%%%:%%#.=+========================-.#%-%%%.               
   .+===========================-:..%%%%:#.-============================-....:-..             
  .-=+====================+=....  .::....-=++========+==+===============+=+=+====..           
  .+=+=================-...     .:+==============+====================+++======+++-.          
 .-==+==============...        .#%+==================================++=#%%%%%%%%%%%.         
 .===============..           .%%%%%%*==+=========+====+==%%%+=++=++=====++%%%%%@%@@%.        
..+=========+=.              .%%%@%@%%@%%#++====+===========%%%@%%%%%%%%%%%%%%%%%%%%%%.       
.:=========.                .#%%%@@%%%%%%%%%%%%#*+============%%%%%%%%%%%%%%*%%%%#.*%%%.      
.:+===+=-.                 .:%@@@%@%%%%*.*%%%%%%%%%%%%%%%%%%%%%%%%@@@%%@%%%%.  .#%%%=%%*.     
.-====++++=++-.            .%%@@@@@@%%=#%%%%%=   *%%%%@@@@@@@@@@@@@@@@@@%%    %%  %%%@@%.     
.-======++-..              .%@@@@@@@%%*%@@+    #%%* .%@@@@@@@@@@@@@@@@@@=     :%%: %%@%%=.    
.==--:...                 .=%%@@@@@%%@%%%.      %@%%% +%@@@@@@@@@@@@@@@+      .%@% +%@@%#.    
                          .+%@@%@@@@@@@%=       *%@%@%..%@@@@@@@@@@@@%%    #%%.%@%.:%@@%%.    
                          .=%@@@@@@@@@@%     =%%+%@%@%% =@@@@@@%@@@@@@ =%@#   -%%%-:%@@@%.    
                           :%@%@@@@@@@@#  #%%.  *%%@@@%% #%%@@@@@@@@@% .      %%%@::%@@@%.    
                           .%%@@@@@@@@@+.+      %%@@@@%@-.%%@@@@@@@@@=       =%@@%.+%%%%*.    
                           .*%@@@@@@%@@=        %@@@@@%%% %%@@@@@@@@%.       %@@@% #@@%%:.    
                            .%@@@@@@@@@*       #%@@@@@%@%:=%@@@@@@@%%       %@@@@% %%%@%.     
                            .-%%%%@@@@@%      .%@@@@@@@@@#.%@@@@@@@@@     :%@@@@@+%%%%%=.     
                             .*%%%@@@@@%     :%@@@@%@@@@@% %@@@@@@@@%    %%@@@@@%.@@%@%.      
                              .+%%@@@@@%+   #%%@@@@@@@@@@% %@@@@@@@@%#@%%%%%%%%%=%%%%%......  
                               .-%%%@%@%%.%%%@%%%%%%%%#*++ %%@@@@@%@%-=+**##%@%%@@%%%%%@#.    
                                 .%%@%@%@%@%%%%%%@%@%%%@%%%@@@@%@@@@%@@%@@@%@@@@@@@%%%%:.     
                                  ..%%%@@@@@%@@@%#.:+%%%%%%%%%%%%%%%%%%@@@@@@@@@@@@@%+.       
                                    .:%%%%%@@@@@%%%.#%%*-:.......:-+:%%%@@@%%@%%%%%+.         
                                      ..#%%@@@@@%%%%%.=%%%%%%%%%%#:*%@%@@@@@@@%%%:.           
                                         ..#%%%%%%@%%%%=..*%%*:.-%%%%%@@@@@%%%:.              
                                            ...*%%%%%%%%%%%%%%%%%%%%%%%%%%=..                 
                                                 ...:-*#%%%%%%%%%%#+-...                      
                                                        .=.......                             
==============================================================================================
==============================================================================================