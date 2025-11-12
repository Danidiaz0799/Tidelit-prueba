# Frontend Vue 3 - Sistema de Reseñas de Libros

Aplicación Vue 3 desarrollada con Vite y Composition API para consumir la API REST de Symfony.

## 📋 Requisitos

- Node.js v16 o superior
- npm v8 o superior
- Servidor Symfony ejecutándose en http://localhost:8000

## 🚀 Instalación y Ejecución

### Instalar dependencias:

```sh
npm install
```

### Servidor de desarrollo (con hot-reload):

```sh
npm run dev
```

La aplicación estará disponible en: **http://localhost:5173/**

### Build para producción:

```sh
npm run build
```

## 🎨 Componente BookList.vue

### Características:

✅ **Composition API con `<script setup>`**: Sintaxis moderna de Vue 3
✅ **Estados reactivos**: Manejo de `books`, `isLoading` y `error`
✅ **Consumo de API**: Usa Axios para peticiones HTTP
✅ **Lifecycle hooks**: `onMounted` para carga automática
✅ **UI Responsive**: Grid adaptable con diseño mobile-first
✅ **Botón de recarga**: Actualiza la lista manualmente
✅ **Manejo de errores**: Mensajes claros al usuario

### Configuración de la API:

La URL de la API está configurada en `src/components/BookList.vue`:

```javascript
const API_URL = 'http://localhost:8000/api/books'
```

Si necesitas cambiar la URL del backend, modifica esta constante.

## 📡 Endpoint Consumido

### GET /api/books
Obtiene la lista de libros con su calificación promedio.

**Respuesta:**
```json
[
  {
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "published_year": 2008,
    "average_rating": 4.5
  }
]
```

## 🐛 Troubleshooting

### Backend no disponible
Si aparece el mensaje de error sobre el servidor Symfony:

**Solución:**
1. Abre otra terminal en la raíz del proyecto Symfony
2. Ejecuta: `php -S localhost:8000 -t public`
3. Refresca la aplicación Vue

### Puerto en uso
Si el puerto 5173 está ocupado, Vite usará automáticamente el siguiente disponible (5174, 5175, etc.)

## 🔧 Tecnologías

- **Vue 3.5.13**: Framework progresivo
- **Vite 7.2.2**: Build tool ultrarrápido
- **Axios 1.7.9**: Cliente HTTP
- **Composition API**: Sintaxis moderna con `<script setup>`
