
# CREDENCIALES

### Usuarios:

    Usuario Admin:
		User: PaquitoAdmin 
		Pass: paquito
		id: 1
	
	Usuario Normal:
		User: JoseNoAdmin
		Pass: josete
		id: 2
        
	El usuario y contraseña se utilizan para un supuesto login.


Para acceder con las credenciales se utiliza un objeto usuario que contiene el id del usuario con los permisos:

```json
{
    "usuario": {
        "id": "1"
    },
    "hotel": {
        "nombre": "Only YOU 2",
        "ubicacion": "Valencia"
    }
}
```
Si el usuario no tiene permisos, algunas acciones no podrán ser realizadas y se mostrará un mensaje de error.

# EJEMPLOS DE ENTRADA
## Clientes
**GET:**
```json
{
    "usuario": {
        "id": "1"
    }
}
```
Requiere Usuario Administrador.  
Devuelve todos los clientes de la aplicación.  

**PUT:**
```json
{
    "usuario": {
        "id": 1
    },
    "cliente": {
        "id": 1,
        "nombre": "nombreEjemplo",
        "apellidos": "apellidosEjemplo",
        "telefono": "111111111",
        "email": "emailejemplo@gmail.com"
    }
}
```
Requiere Usuario Administrador.  
Nodifica la información de un cliente existente.

*El metodo insert se accede desde la API de Usuarios.*  
*El metodo delete no se puede acceder actualmente, está pensado para ser accedido desde el front y no mediante una petición.*

## Usuarios
*No tiene metodo GET*  
**POST**
```json
{
    "usuario": {
        "nombre": "ejemplo",
        "contrasena": "ejemplo",
        "admin": 0
    },
    "cliente": {
        "nombre": "nombreEjemplo",
        "apellidos": "apellidosEjemplo",
        "telefono": "111111111",
        "email": "emailejemplo@gmail.com"
    }
}
```
No requiere Usuario Administrador.
Crea una nueva cuenta con las credenciales pasadas y crea un cliente con la información pasada.  

**PUT**
```json
{
    "usuario": {
        "id": 1
    },
    "update": {
        "id": 17,
        "nombre": "ejemploModificado",
        "contrasena": "cambiada"
    }
}
```
Requiere Usuario Administrador.  
Modifica las credenciales de un usuario, sin modificar el resto de su información.

**DELETE**  
*Se accede desde la API de clientes*

## Hoteles
**GET**  
No tiene parametros de entrada.  
No requiere Administrador.  
Devuelve todos los hoteles inscritos en la aplicación.  

**POST**
```json
{
    "usuario": {
        "id": "1"
    },
    "hotel": {
        "nombre": "Only YOU 2",
        "ubicacion": "Valencia"
    }
}
```
Requiere Administrador.  
Inscribe un hotel a la aplicación.  

**PUT**
```json
{
    "usuario": {
        "id": 1
    },
    "hotel": {
        "nombre": "Only You 3",
        "ubicacion": "Valencia",
        "valoracion": "10",
        "id": 3
    }
}
```
Requiere Administrador.  
Actualiza los datos de un hotel existente.  

**DELETE**
```json
{
    "usuario": {
        "id": 1
    },
    "hotel": {
        "id": 3
    }
}
```
Requiere Administrador.  
Elimina un hotel de la aplicación, por id.  

## Habitaciones
**GET**  
No tiene parametros de entrada.  
No requiere Administrador.  
Devuelve TODAS las Habitaciones, TODOS los hoteles.

**POST**  
```json
{
    "usuario": {
        "id": 1
    },
    "habitacion": {
        "hotel": 1,
        "tipo": "Individual",
        "camas": 1
    }
}
```
Requiere Administrador.  
Añade una nueva habitación a la aplicación.  

**PUT**
```json
{
    "usuario": {
        "id": 1
    },
    "habitacion": {
        "id": 4,
        "tipo": "Individual",
        "camas": 1
    }
}
```
Requiere Administrador.  
Actualiza los datos de una habitacion existente.  

**DELETE**
```json
{
    "usuario": {
        "id": 1
    },
    "habitacion": {
        "id": 2
    }
}
```
Requiere Administrador.  
Borra una habitacion, por id.  

## RESERVAS
**GET**
```json
{
    "usuario": {
        "id": 1
    }
}
```
Requiere Administrador.  
Obtienes todas las reservas realizadas en la aplicación.  

**POST**
```json
{
    "reserva": {
        "cliente": 1,
        "habitacion": 2,
        "fDesde": "2023-12-22",
        "fHasta": "2023-12-24"
    }
}
```
No requiere Administrador.  
Reserva una habitacion.  

**PUT**
```json
{
    "reserva": {
        "id": 1,
        "fDesde": "2023-12-22",
        "fHasta": "2023-12-23"
    }
}
```
No requiere Administrador.  
Actualiza la entrada de una reserva ya realizada.  

**DELETE**
```json
{
    "reserva": {
        "id": 2
    }
}
```
No requiere Administrador.  
Elimina una reserva.  

## Opiniones
**GET**
```json
{
    "opinion": {
        "habitacion": 2
    }
}
```
No requiere Administrador.  
Obtienes las opiniones por habitacion.  


**POST**
```json
{
    "opinion": {
        "cliente": 3,
        "habitacion": 2,
        "nota": 2,
        "texto": "meh"
    }
}
```
No requiere Administrador.  
Añade una nueva opinion. El usuario tiene que haber estado en esa habitacion.  

**PUT**
```json
{
    "opinion": {
        "id": 1,
        "nota": 5,
        "texto": "meh"
    }
}
```
No requiere Administrador.  
Actualiza una opinion existente.  

**DELETE**
```json
{
    "opinion": {
        "id": 2
    }
}
```
No requiere Administrador.  
Borra una opinion existente.  