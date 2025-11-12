<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Configuración de la URL base de la API de Symfony
const API_URL = 'http://localhost:8000/api/books'

// Estados reactivos
const books = ref([])
const isLoading = ref(false)
const error = ref(null)

/**
 * Función para obtener la lista de libros desde la API
 */
const fetchBooks = async () => {
  isLoading.value = true
  error.value = null
  
  try {
    const response = await axios.get(API_URL)
    books.value = response.data
  } catch (err) {
    error.value = 'Error al cargar los libros. Verifica que el servidor Symfony esté ejecutándose en http://localhost:8000'
  } finally {
    isLoading.value = false
  }
}

// Cargar los libros automáticamente al montar el componente
onMounted(() => {
  fetchBooks()
})
</script>

<template>
  <div class="book-list-container">
    <!-- Encabezado -->
    <header class="header">
      <h1>📚 Lista de Libros</h1>
      <button 
        @click="fetchBooks" 
        class="refresh-button"
        :disabled="isLoading"
      >
        {{ isLoading ? '⏳ Cargando...' : '🔄 Refrescar Lista' }}
      </button>
    </header>

    <!-- Mensaje de carga -->
    <div v-if="isLoading" class="loading-message">
      <div class="spinner"></div>
      <p>Cargando libros...</p>
    </div>

    <!-- Mensaje de error -->
    <div v-else-if="error" class="error-message">
      <p>⚠️ {{ error }}</p>
    </div>

    <!-- Lista de libros -->
    <div v-else-if="books.length > 0" class="books-grid">
      <div 
        v-for="book in books" 
        :key="book.title" 
        class="book-card"
      >
        <h2 class="book-title">{{ book.title }}</h2>
        <p class="book-author">
          <strong>Autor:</strong> {{ book.author }}
        </p>
        <p class="book-year">
          <strong>Año:</strong> {{ book.published_year }}
        </p>
        <div class="book-rating">
          <span class="rating-label">Calificación Promedio:</span>
          <span class="rating-value">
            {{ book.average_rating ? book.average_rating.toFixed(2) : 'Sin calificaciones' }}
            <span v-if="book.average_rating" class="rating-stars">⭐</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Mensaje cuando no hay libros -->
    <div v-else class="empty-message">
      <p>📭 No hay libros disponibles</p>
    </div>
  </div>
</template>

<style scoped>
.book-list-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header h1 {
  color: #2c3e50;
  font-size: 2.5rem;
  margin: 0;
}

.refresh-button {
  background-color: #42b983;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.refresh-button:hover:not(:disabled) {
  background-color: #359268;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.refresh-button:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.loading-message {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #ecf0f1;
  border-top: 4px solid #42b983;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message {
  background-color: #fee;
  border: 1px solid #fcc;
  border-radius: 8px;
  padding: 1.5rem;
  color: #c33;
  text-align: center;
  font-weight: 500;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.book-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #e0e0e0;
}

.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.book-title {
  color: #2c3e50;
  font-size: 1.5rem;
  margin: 0 0 1rem 0;
  border-bottom: 2px solid #42b983;
  padding-bottom: 0.5rem;
}

.book-author,
.book-year {
  color: #555;
  margin: 0.5rem 0;
  font-size: 1rem;
}

.book-rating {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.rating-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.rating-value {
  font-size: 1.3rem;
  font-weight: bold;
  color: #42b983;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.rating-stars {
  font-size: 1.5rem;
}

.empty-message {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
  font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    flex-direction: column;
    text-align: center;
  }
  
  .header h1 {
    font-size: 2rem;
  }
  
  .books-grid {
    grid-template-columns: 1fr;
  }
}
</style>
