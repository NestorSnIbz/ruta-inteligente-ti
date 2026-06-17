# Ruta Inteligente TI

## Estudiantes
- Nestor Serrano Ibañez
- Junior Mamani Estaña

## Tecnologías

### Backend
- PHP 8.1+
- Arquitectura MVC
- Integración con Supabase mediante cliente propio

### Frontend
- HTML5
- Tailwind CSS
- JavaScript

### Base de Datos
- PostgreSQL
- Scripts de estructura y carga en `base-datos.sql`

### Backend as a Service (BaaS)
Supabase se utiliza para:
- Autenticación de usuarios
- Persistencia sobre PostgreSQL
- Exposición de endpoints REST vía PostgREST
- Gestión de datos de proyectos, análisis y resultados

---

## Arquitectura del Sistema

La aplicación está construida sobre una arquitectura **MVC** en PHP y utiliza **Supabase** como capa de autenticación y acceso a datos.

El sistema se enfoca en la construcción y seguimiento de un **plan estratégico empresarial**, organizado por proyectos. Cada proyecto concentra información base de la empresa y varios módulos de análisis que se relacionan entre sí.

Actualmente la aplicación permite:
- Iniciar sesión con usuarios autenticados en Supabase
- Gestionar proyectos estratégicos
- Registrar misión, visión, valores y objetivos
- Ejecutar análisis de Cadena de valor, BCG, Perfil competitivo y P.E.S.T.
- Consolidar factores FODA por procedencia
- Construir la matriz FODA cruzada de estrategias
- Elaborar acciones en la matriz CAME
- Visualizar un overview ejecutivo y exportarlo a PDF
- Gestionar miembros del proyecto

---

## Arquitectura en Capas

### Capa de Presentación
Encargada de las vistas del dashboard, autenticación, configuración y detalle del proyecto.

Tecnologías:
- HTML
- Tailwind CSS
- JavaScript

### Capa de Aplicación
Implementada en controladores PHP que coordinan validaciones, flujo de navegación, render parcial de paneles y respuestas AJAX.

Componentes principales:
- `AuthController`
- `ProyectoController`
- `BcgController`
- `ConfiguracionController`
- `RegisterController`

### Capa de Servicios
Encapsula la comunicación con Supabase.

Componentes principales:
- `SupabaseClient`
- Repositorios y servicios específicos del módulo BCG

### Capa de Datos
Gestiona la persistencia de proyectos, factores, respuestas, resultados y relaciones entre usuarios y proyectos.

Modelos relevantes:
- `Proyecto`, `ProyectoMiembro`, `Persona`
- `Mision`, `Vision`, `Valor`
- `ObjetivoEstrategico`, `ObjetivoEspecifico`
- `CadenaValor`, `PerfilCompetitivo`, `Pest`
- `Foda`, `FodaCruzada`, `Came`

---

## Notas
- El sistema usa paneles desacoplados y carga parcial para mejorar la experiencia dentro del detalle del proyecto.
- Los módulos de análisis se alimentan entre sí; por ejemplo, `FODA`, `Estrategias` y `CAME` reutilizan factores generados en módulos previos.
- El archivo `base-datos.sql` contiene la estructura necesaria para ejecutar la aplicación con sus módulos actuales.
- El archivo `base-datos.md` resume el modelo entidad-relación base del sistema.

---

## Despliegue en local

### Requisitos
- PHP 8.1 o superior
- Node.js 18 o superior
- Un proyecto Supabase con autenticación habilitada
- Archivo `.env` en la raíz, basado en `.env.example`

Variables mínimas:
- `SUPABASE_URL`
- `SUPABASE_ANON_KEY`

Variable recomendada para operaciones de backend ampliadas:
- `SUPABASE_SERVICE_ROLE_KEY`

### Opción A: desarrollo local

1. Instalar dependencias del frontend:

```bash
npm install
```

2. Compilar Tailwind en modo desarrollo:

```bash
npm run dev
```

3. Crear o completar el archivo `.env` en la raíz del proyecto.

4. Ejecutar el script `base-datos.sql` en la base PostgreSQL asociada a Supabase.

5. Levantar el servidor PHP apuntando a `public/`:

```bash
php -S localhost:8000 -t public
```

6. Abrir en el navegador:
- `http://localhost:8000/`

### Opción B: Docker

```bash
docker build -t ruta-inteligente-ti .
docker run --rm -p 10000:10000 -e PORT=10000 ruta-inteligente-ti
```

Abrir:
- `http://localhost:10000/`

---

## Módulos Principales

### Acceso y configuración
- Inicio de sesión con credenciales de Supabase
- Registro de usuarios
- Configuración de nombre y contraseña del usuario autenticado

### Gestión de proyectos
- Creación de proyectos estratégicos
- Listado de proyectos desde dashboard
- Gestión de miembros por proyecto
- Control de acceso por sesión y token de proyecto

### Planeamiento base
- Misión
- Visión
- Valores
- Objetivos estratégicos
- Objetivos específicos asociados a cada objetivo estratégico

### Análisis estratégicos
- Cadena de valor
- Autodiagnóstico BCG
- Perfil competitivo
- Autodiagnóstico Entorno Global P.E.S.T.

### Consolidación estratégica
- FODA por procedencia
  - Fortalezas y debilidades desde Cadena de valor y BCG
  - Oportunidades y amenazas desde Perfil competitivo y P.E.S.T.
- Matriz FODA cruzada
  - FO, FA, DO y DA
  - síntesis de resultados
  - estrategia predominante
- Matriz CAME
  - acciones para corregir, afrontar, mantener y explotar

### Reportes
- Overview ejecutivo del proyecto
- Exportación del overview a PDF

---

## Requerimientos del Sistema

### Requerimientos Funcionales

- **RF01** – Permitir el registro e inicio de sesión de usuarios
- **RF02** – Mantener sesiones autenticadas para acceder al sistema
- **RF03** – Mostrar un dashboard con los proyectos asociados al usuario
- **RF04** – Crear y consultar proyectos estratégicos
- **RF05** – Gestionar misión, visión y valores del proyecto
- **RF06** – Registrar objetivos estratégicos y objetivos específicos
- **RF07** – Evaluar la Cadena de valor de la empresa
- **RF08** – Registrar productos y evaluación del autodiagnóstico BCG
- **RF09** – Evaluar el Perfil competitivo
- **RF10** – Evaluar el entorno global mediante P.E.S.T.
- **RF11** – Registrar factores FODA derivados de los análisis realizados
- **RF12** – Construir la matriz FODA cruzada para identificar estrategias
- **RF13** – Registrar acciones en la matriz CAME
- **RF14** – Consolidar un overview ejecutivo del plan estratégico
- **RF15** – Exportar el overview en formato PDF
- **RF16** – Gestionar miembros del proyecto por parte del creador
- **RF17** – Guardar, editar y eliminar información dentro de los módulos disponibles

---

### Requerimientos No Funcionales

- **RNF01 – Seguridad**  
  La aplicación debe restringir el acceso a usuarios autenticados y validar la identidad mediante Supabase y sesiones en servidor.

- **RNF02 – Mantenibilidad**  
  El sistema debe conservar una organización modular basada en MVC y paneles desacoplados para facilitar cambios y ampliaciones.

- **RNF03 – Compatibilidad**  
  La interfaz debe funcionar correctamente en navegadores modernos como Chrome, Edge y Firefox.

- **RNF04 – Persistencia**  
  La información capturada en los módulos debe almacenarse en PostgreSQL a través de Supabase.

- **RNF05 – Usabilidad**  
  El sistema debe ofrecer formularios, tablas y mensajes de validación claros para completar el plan estratégico paso a paso.

- **RNF06 – Escalabilidad funcional**  
  La arquitectura debe permitir añadir nuevos módulos estratégicos sin rehacer la estructura principal del proyecto.

---
