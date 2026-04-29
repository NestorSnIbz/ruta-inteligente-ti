# 🚀 Ruta Inteligente TI

## Estudiantes
- Nestor Serrano Ibañez
- Junior Mamani Estaña

## 🧰 Tecnologías


### 🔙 Backend
- PHP 8.1  
- Patrón MVC (Modelo - Vista - Controlador)

### 🎨 Frontend
- HTML5  
- CSS3  
- Tailwind CSS  
- JavaScript  


### 🗄️ Base de Datos
- PostgreSQL  

### ☁️ Backend as a Service (BaaS)
Se utiliza Supabase para:
- Gestión de base de datos  
- Exposición automática de APIs  
- Autenticación y gestión de usuarios  
- Almacenamiento de archivos  

---

## 🏗️ Arquitectura del Sistema

El sistema está basado en el patrón **MVC**, complementado con un enfoque de **Backend as a a Service (BaaS)** mediante Supabase.

Este enfoque permite:
- Separación clara de responsabilidades  
- Mayor escalabilidad  
- Reducción de complejidad en el backend  

---

## 🧩 Arquitectura en Capas

### 🎯 Capa de Presentación
Encargada de la interfaz de usuario y la interacción con el usuario.  
**Tecnologías:** HTML, CSS, Tailwind CSS, JavaScript  

### ⚙️ Capa de Aplicación
Gestiona la lógica de negocio, validaciones y flujo de datos.  
Actúa como intermediaria entre la presentación y los datos.  

### 🔌 Capa de Servicios
Encapsula la comunicación con servicios externos.  
Se encarga de la integración con Supabase.  

### 💾 Capa de Datos
Responsable de la persistencia de la información.  
Gestiona el acceso a la base de datos PostgreSQL.  

---

## 📌 Notas
- Se sigue una arquitectura modular y escalable.  
- Supabase reduce la necesidad de implementar servicios backend complejos desde cero.  
- El uso de MVC facilita el mantenimiento y la organización del código.  