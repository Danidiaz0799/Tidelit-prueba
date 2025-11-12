# 📚 Sistema de Reseñas de Libros (Tide Lit)

**Objetivo:** Prueba técnica Symfony 6 (Backend) + Vue 3 (Frontend) para la gestión de libros y sus reseñas.

## 1. Requisitos

| Componente | Versión Mínima | Notas |
| :--- | :--- | :--- |
| **PHP** | 8.2 | Recomendado el servidor web de Symfony. |
| **Composer** | 2.x | Gestor de dependencias de PHP. |
| **Node.js** | 16+ | Necesario para ejecutar el frontend con Vite. |
| **npm/yarn** | 8+ | Gestor de paquetes de Node. |
| **Base de Datos** | SQLite | Configuración por defecto usando Doctrine ORM. |

---

## 2. Instrucciones de Ejecución

### 2.1. Instalación y Configuración del Backend (Symfony)

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/Danidiaz0799/Tidelit-prueba.git
    cd Tidelit-prueba
    ```

2.  **Configurar la Base de Datos:**
    ```bash
    cp .env.example .env
    # La configuración inicial usa SQLite (var/data.db). No se requiere configuración adicional de credenciales.
    ```

3.  **Instalar dependencias y preparar DB:**
    ```bash
    composer install
    php bin/console doctrine:database:create   # Crea el archivo data.db
    php bin/console doctrine:migrations:migrate # Ejecuta la migración inicial (tablas book y review)
    ```

4.  **Cargar datos iniciales (Fixtures / Seeders):**
    ```bash
    php bin/console doctrine:fixtures:load --no-interaction # Carga 3 libros y 7+ reseñas
    ```

5.  **Iniciar el servidor backend:**
    ```bash
    symfony server:start
    # O usar el servidor de PHP: php -S localhost:8000 -t public
    ```
    El backend de la API estará disponible en **`http://localhost:8000`**.

### 2.2. Cómo correr el Frontend (Vue 3)

1.  **Navegar a la carpeta `frontend`:**
    ```bash
    cd frontend
    ```

2.  **Instalar dependencias:**
    ```bash
    npm install
    ```

3.  **Ejecutar el servidor de desarrollo:**
    ```bash
    npm run dev
    ```
    El frontend estará disponible en **`http://localhost:5173/`**.

---

## 3. Endpoints Implementados

### GET /api/books

Devuelve la lista de libros con su calificación promedio.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `title` | `string` | Título del libro. |
| `author` | `string` | Autor del libro. |
| `published_year` | `integer` | Año de publicación. |
| `average_rating` | `float \| null` | **Promedio calculado eficientemente en DB.** |

**Ejemplo de Respuesta (200 OK):**
```json
[
  {
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "published_year": 2008,
    "average_rating": 4.0
  },
  {
    "title": "El Arte de Programar",
    "author": "Donald Knuth",
    "published_year": 1968,
    "average_rating": 4.75
  }
]
```

---

### POST /api/reviews

Registra una reseña para un libro existente. Implementa validaciones en el backend.

**Ejemplo de Request (Creación de Reseña):**
```json
{
  "book_id": 1,
  "rating": 5,
  "comment": "Un libro excelente, fundamental para mi carrera."
}
```

**Ejemplo de Respuesta Exitosa (201 Created):**
```json
{
  "id": 8,
  "book_id": 1,
  "rating": 5,
  "comment": "Un libro excelente, fundamental para mi carrera.",
  "created_at": "2025-11-12T17:11:23+01:00"
}
```

---

## 4. Validación y Lógica Técnica

### 4.1. Captura/Ejemplo del Endpoint Funcionando

Comando curl para verificar la respuesta del endpoint `GET /api/books`:

```bash
curl http://localhost:8000/api/books
```

**Respuesta:**
```json
[{"title":"Clean Code","author":"Robert C. Martin","published_year":2008,"average_rating":4},{"title":"El Arte de Programar","author":"Donald Knuth","published_year":1968,"average_rating":4.75},{"title":"Refactoring","author":"Martin Fowler","published_year":1999,"average_rating":4.5}]
```

---

### 4.2. Respuestas Esperadas ante Errores de Validación (Status 400)

| Caso de Error | Regla Rota | Respuesta JSON |
| :--- | :--- | :--- |
| **Libro no encontrado** | `book_id` no existe. | `{"error": "Libro no encontrado", "message": "El libro con ID \"999\" no fue encontrado."}` |
| **Rating inválido** | `rating` fuera del rango 1-5. | `{"error": "Validación fallida", "errors": {"rating": "La calificación debe estar entre 1 y 5"}}` |
| **Comentario no válido** | `comment` es demasiado corto. | `{"error": "Validación fallida", "errors": {"comment": "El comentario debe tener al menos 5 caracteres"}}` |

---

### 4.3. Nota sobre Eficiencia (average_rating)

El cálculo del `average_rating` se realiza con **una sola consulta DQL** utilizando el `QueryBuilder` de Doctrine, empleando `LEFT JOIN` y la función SQL `AVG()` (cumpliendo con el requisito de eficiencia).

**Libros sin reseñas:** Si un libro no tiene reseñas, el valor devuelto para `average_rating` será `null`.

**Implementación:** `src/Repository/BookRepository.php` → método `findAllWithAverageRating()`

---

## 5. Pregunta Opcional: Estrategia de Escalabilidad

Para escalar esta aplicación a **cientos de miles de libros y usuarios**, mi estrategia se centraría en:

### 5.1. Capa de Caché HTTP
Implementar **Redis** o **Varnish** para cachear el endpoint `GET /api/books`. Dado que la lista de libros y sus promedios no cambian constantemente, esta capa reduciría drásticamente la carga de la base de datos y la latencia para la mayoría de las peticiones de lectura.

### 5.2. Cálculo Asíncrono de Promedios
Desacoplar el cálculo del promedio de rating. En lugar de usar `AVG()` dinámicamente, se crearía un campo `average_rating_cached` en la tabla `Book`. Este campo se actualizaría **asíncronamente** mediante un **Message Queue** (ej. Symfony Messenger) cada vez que se registra una nueva reseña (`POST /api/reviews`). Esto elimina la pesada operación de agregación de las consultas de lectura principales.

### 5.3. Optimización de la DB
Asegurar **índices óptimos**, especialmente en `review.book_id` y potencialmente un índice compuesto en la tabla `review` sobre `(book_id, created_at)`.

### 5.4. Paginación y Rate Limiting
- Implementar paginación en `GET /api/books` (ej. `?page=1&limit=20`)
- API Gateway para rate limiting y throttling
- Búsqueda con Elasticsearch para consultas complejas

---

## 6. Entregable Final

- **Branch a Evaluar:** `main`
- **Commit Final:** `[INSERTAR HASH DEL COMMIT FINAL AQUÍ]`

---

## 7. Tecnologías Utilizadas

### Backend:
- Symfony 6.4.27
- Doctrine ORM 2.20.8
- Doctrine Migrations 3.9.4
- Doctrine Fixtures 3.7.2
- SQLite
- Symfony Validator

### Frontend:
- Vue 3.5.13
- Vite 7.2.2
- Axios 1.7.9
- Composition API

---

**✅ Prueba técnica completada y lista para evaluación.**