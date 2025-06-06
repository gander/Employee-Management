# API Documentation - Employee Management System

## Przegląd API

Employee Management API to RESTful API do zarządzania pracownikami z pełnym systemem adresów, autoryzacją i walidacją. API obsługuje operacje CRUD, filtrowanie, sortowanie, paginację oraz zarządzanie hasłami.

### Charakterystyka

- **Framework**: Laravel 12.x z Laravel Sanctum
- **Baza danych**: MySQL z Eloquent ORM
- **Autoryzacja**: Bearer Token (API tokens)
- **Walidacja**: Comprehensive form request validation
- **Paginacja**: JSON API standard
- **Dokumentacja**: Automatyczna z Scribe

### Base URL

```
http://localhost/api
```

## Autoryzacja

### Typy Endpointów

- **Publiczne**: Nie wymagają autoryzacji
- **Chronione**: Wymagają nagłówka `Authorization: Bearer {token}`

### Uzyskiwanie Tokenu

```bash
POST /api/auth/login
```

**Request:**
```json
{
  "email": "active@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "1|abcdef1234567890...",
  "employee": {
    "id": 1,
    "full_name": "Active Employee",
    "email": "active@example.com",
    "is_active": true
  }
}
```

### Użycie Tokenu

Dodaj nagłówek do wszystkich chronionych żądań:
```
Authorization: Bearer 1|abcdef1234567890...
```

## Endpointy

### 1. Autoryzacja

#### Login
```http
POST /api/auth/login
```

**Parametry:**
- `email` (string, required): Email pracownika
- `password` (string, required): Hasło (min. 6 znaków)

**Odpowiedzi:**
- `200`: Sukces - zwraca token i dane pracownika
- `401`: Nieprawidłowe dane lub konto nieaktywne
- `422`: Błędy walidacji

#### Forgot Password
```http
POST /api/auth/forgot-password
```

**Parametry:**
- `email` (string, required): Email pracownika

**Odpowiedzi:**
- `200`: Token reset został wygenerowany
- `422`: Błędy walidacji (email nie istnieje)

#### Reset Password
```http
POST /api/auth/reset-password
```

**Parametry:**
- `email` (string, required): Email pracownika
- `token` (string, required): Token z forgot-password
- `password` (string, required): Nowe hasło (min. 6 znaków)
- `password_confirmation` (string, required): Potwierdzenie hasła

**Odpowiedzi:**
- `200`: Hasło zostało zresetowane
- `400`: Nieprawidłowy lub wygasły token
- `422`: Błędy walidacji

### 2. Pracownicy - Endpointy Publiczne

#### Lista Pracowników
```http
GET /api/employees
```

**Query Parameters:**

**Filtrowanie:**
- `filter[full_name]`: Filtruj po imieniu i nazwisku
- `filter[email]`: Filtruj po adresie email
- `filter[position]`: Filtruj po pozycji (front-end, back-end, pm, designer, tester)
- `filter[is_active]`: Filtruj po statusie aktywności (true/false)

**Sortowanie:**
- `sort`: Sortuj po polu (full_name, email, position, created_at)
- Użyj `-` dla sortowania malejącego (np. `-created_at`)

**Paginacja:**
- `page[number]`: Numer strony (domyślnie 1)
- `page[size]`: Rozmiar strony (domyślnie 15, max 100)

**Przykłady:**
```bash
# Wszystkie pracownicy
GET /api/employees

# Filtrowanie po pozycji
GET /api/employees?filter[position]=front-end

# Sortowanie po nazwisku malejąco
GET /api/employees?sort=-full_name

# Paginacja
GET /api/employees?page[size]=5&page[number]=2

# Kombinowane
GET /api/employees?filter[is_active]=true&sort=full_name&page[size]=10
```

**Odpowiedź:**
```json
{
  "data": [
    {
      "id": 1,
      "full_name": "Jan Kowalski",
      "email": "jan.kowalski@example.com",
      "phone": "+48123456789",
      "position": "front-end",
      "average_annual_salary": "75000.00",
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/employees?page=1",
    "last": "http://localhost/api/employees?page=5",
    "prev": null,
    "next": "http://localhost/api/employees?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

### 3. Pracownicy - Endpointy Chronione

#### Tworzenie Pracownika
```http
POST /api/employees
```
*Wymaga autoryzacji*

**Parametry wymagane:**
- `full_name` (string): Imię i nazwisko
- `email` (string): Unikalny adres email
- `position` (string): Pozycja (front-end, back-end, pm, designer, tester)
- `password` (string): Hasło (min. 6 znaków)
- `residential_address_country` (string): Kraj zamieszkania
- `residential_address_postal_code` (string): Kod pocztowy
- `residential_address_city` (string): Miasto
- `residential_address_house_number` (string): Numer domu
- `different_correspondence_address` (boolean): Czy adres korespondencyjny jest inny

**Parametry opcjonalne:**
- `phone` (string): Numer telefonu
- `average_annual_salary` (number): Roczne wynagrodzenie
- `residential_address_apartment_number` (string): Numer mieszkania
- `is_active` (boolean): Status aktywności (domyślnie false)

**Parametry warunkowe (gdy different_correspondence_address = true):**
- `correspondence_address_country` (string): Kraj korespondencyjny
- `correspondence_address_postal_code` (string): Kod pocztowy
- `correspondence_address_city` (string): Miasto
- `correspondence_address_house_number` (string): Numer domu
- `correspondence_address_apartment_number` (string): Numer mieszkania

**Przykład - Te same adresy:**
```json
{
  "full_name": "Anna Nowak",
  "email": "anna.nowak@example.com",
  "phone": "+48123456789",
  "position": "designer",
  "password": "password123",
  "residential_address_country": "Polska",
  "residential_address_postal_code": "00-001",
  "residential_address_city": "Warszawa",
  "residential_address_house_number": "15A",
  "residential_address_apartment_number": "5",
  "different_correspondence_address": false,
  "is_active": true
}
```

**Przykład - Różne adresy:**
```json
{
  "full_name": "Piotr Wiśniewski",
  "email": "piotr.wisniewski@example.com",
  "position": "back-end",
  "password": "password123",
  "average_annual_salary": 85000.50,
  "residential_address_country": "Polska",
  "residential_address_postal_code": "30-001",
  "residential_address_city": "Kraków",
  "residential_address_house_number": "10",
  "different_correspondence_address": true,
  "correspondence_address_country": "Niemcy",
  "correspondence_address_postal_code": "10115",
  "correspondence_address_city": "Berlin",
  "correspondence_address_house_number": "22",
  "correspondence_address_apartment_number": "15",
  "is_active": true
}
```

**Odpowiedzi:**
- `201`: Pracownik utworzony pomyślnie
- `401`: Brak autoryzacji
- `422`: Błędy walidacji

#### Szczegóły Pracownika
```http
GET /api/employees/{id}
```
*Wymaga autoryzacji*

**Parametry URL:**
- `id` (integer): ID pracownika

**Odpowiedź - pełne dane z adresami:**
```json
{
  "employee": {
    "id": 1,
    "full_name": "Jan Kowalski",
    "email": "jan.kowalski@example.com",
    "phone": "+48123456789",
    "position": "front-end",
    "average_annual_salary": "75000.50",
    "residential_address_country": "Polska",
    "residential_address_postal_code": "00-123",
    "residential_address_city": "Warszawa",
    "residential_address_house_number": "15A",
    "residential_address_apartment_number": "5",
    "different_correspondence_address": true,
    "correspondence_address_country": "Niemcy",
    "correspondence_address_postal_code": "10115",
    "correspondence_address_city": "Berlin",
    "correspondence_address_house_number": "22",
    "correspondence_address_apartment_number": "10",
    "is_active": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**Odpowiedzi:**
- `200`: Sukces - zwraca dane pracownika
- `401`: Brak autoryzacji
- `404`: Pracownik nie znaleziony

#### Aktualizacja Pracownika
```http
PUT /api/employees/{id}
```
*Wymaga autoryzacji*

**Parametry URL:**
- `id` (integer): ID pracownika

**Parametry (wszystkie opcjonalne):**
Wszystkie pola z tworzenia pracownika, ale każde jest opcjonalne.

**Przykład - częściowa aktualizacja:**
```json
{
  "position": "pm",
  "average_annual_salary": 90000.00
}
```

**Przykład - zmiana adresów:**
```json
{
  "residential_address_city": "Gdańsk",
  "different_correspondence_address": false
}
```

**Uwagi:**
- Hasło jest hashowane automatycznie jeśli podane
- Ustawienie `different_correspondence_address: false` czyści wszystkie pola adresu korespondencyjnego
- Email musi być unikalny (z wyłączeniem aktualnego pracownika)

**Odpowiedzi:**
- `200`: Pracownik zaktualizowany pomyślnie
- `401`: Brak autoryzacji
- `404`: Pracownik nie znaleziony
- `422`: Błędy walidacji

#### Usuwanie Pracownika
```http
DELETE /api/employees/{id}
```
*Wymaga autoryzacji*

**Parametry URL:**
- `id` (integer): ID pracownika

**Odpowiedzi:**
- `200`: Pracownik usunięty pomyślnie
- `401`: Brak autoryzacji
- `404`: Pracownik nie znaleziony

#### Masowe Usuwanie Pracowników
```http
DELETE /api/employees/bulk
```
*Wymaga autoryzacji*

**Parametry:**
- `employee_ids` (array): Tablica ID pracowników do usunięcia (1-100 elementów)

**Przykład:**
```json
{
  "employee_ids": [1, 2, 3, 15, 20]
}
```

**Odpowiedź:**
```json
{
  "message": "5 employees deleted successfully",
  "deleted_count": 5,
  "deleted_ids": [1, 2, 3, 15, 20]
}
```

**Odpowiedzi:**
- `200`: Pracownicy usunięci pomyślnie
- `401`: Brak autoryzacji
- `422`: Błędy walidacji (nieprawidłowe ID)

### 4. Informacje o Użytkowniku

#### Aktualny Użytkownik
```http
GET /api/me
```
*Wymaga autoryzacji*

**Odpowiedź:**
```json
{
  "id": 1,
  "full_name": "Jan Kowalski",
  "email": "jan.kowalski@example.com",
  "phone": "+48123456789",
  "position": "front-end",
  "average_annual_salary": "75000.00",
  "is_active": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## Kody Statusów

### Sukces (2xx)
- `200 OK`: Żądanie wykonane pomyślnie
- `201 Created`: Zasób utworzony pomyślnie

### Błędy Klienta (4xx)
- `400 Bad Request`: Nieprawidłowe żądanie (np. wygasły token reset)
- `401 Unauthorized`: Brak autoryzacji lub nieprawidłowe dane logowania
- `404 Not Found`: Zasób nie znaleziony
- `422 Unprocessable Entity`: Błędy walidacji

### Błędy Serwera (5xx)
- `500 Internal Server Error`: Błąd serwera

## Formaty Błędów

### Błąd Autoryzacji (401)
```json
{
  "message": "Unauthenticated."
}
```

### Błąd Walidacji (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["This email address is already registered."],
    "position": ["Position must be one of: front-end, back-end, pm, designer, tester."],
    "residential_address_country": ["Residential address country is required."]
  }
}
```

### Błąd Nie Znaleziono (404)
```json
{
  "message": "Employee not found"
}
```

## Walidacja

### Pola Pracownika

#### Podstawowe dane
- `full_name`: string, max 255 znaków
- `email`: email, unikalny w tabeli employees
- `phone`: string, max 20 znaków, opcjonalne
- `average_annual_salary`: liczba z max 2 miejscami po przecinku, opcjonalne
- `position`: jeden z: front-end, back-end, pm, designer, tester
- `password`: string, min 6 znaków
- `is_active`: boolean, opcjonalne (domyślnie false)

#### Adres zamieszkania (wymagany)
- `residential_address_country`: string, max 255 znaków
- `residential_address_postal_code`: string, max 20 znaków
- `residential_address_city`: string, max 255 znaków
- `residential_address_house_number`: string, max 20 znaków
- `residential_address_apartment_number`: string, max 20 znaków, opcjonalne

#### Adres korespondencyjny (warunkowy)
- `different_correspondence_address`: boolean, wymagane
- `correspondence_address_*`: wymagane tylko gdy `different_correspondence_address` = true

### Komunikaty Błędów (w języku angielskim)

- Email address is required.
- Please provide a valid email address.
- This email address is already registered.
- Position must be one of: front-end, back-end, pm, designer, tester.
- Password must be at least 6 characters.
- Residential address country is required.
- Correspondence address is required when different address is specified.

## Przykłady Użycia

### Scenariusz 1: Rejestracja i zarządzanie pracownikiem

```bash
# 1. Logowanie
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "active@example.com", "password": "password123"}'

# Odpowiedź zawiera token - użyj go w kolejnych żądaniach
export TOKEN="1|abcdef..."

# 2. Tworzenie pracownika
curl -X POST http://localhost/api/employees \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Maria Kowalska",
    "email": "maria.kowalska@example.com",
    "position": "designer",
    "password": "password123",
    "residential_address_country": "Polska",
    "residential_address_postal_code": "00-001",
    "residential_address_city": "Warszawa",
    "residential_address_house_number": "10",
    "different_correspondence_address": false,
    "is_active": true
  }'

# 3. Pobranie szczegółów (ID z poprzedniej odpowiedzi)
curl -X GET http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN"

# 4. Aktualizacja pozycji
curl -X PUT http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"position": "pm", "average_annual_salary": 85000}'

# 5. Usunięcie
curl -X DELETE http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN"
```

### Scenariusz 2: Wyszukiwanie i filtrowanie

```bash
# Wszyscy front-end developerzy
curl "http://localhost/api/employees?filter[position]=front-end"

# Aktywni pracownicy, sortowani po nazwisku
curl "http://localhost/api/employees?filter[is_active]=true&sort=full_name"

# Druga strona wyników (5 na stronę)
curl "http://localhost/api/employees?page[size]=5&page[number]=2"

# Kombinowane filtrowanie
curl "http://localhost/api/employees?filter[position]=designer&filter[is_active]=true&sort=-created_at"
```

### Scenariusz 3: Reset hasła

```bash
# 1. Żądanie reset tokenu
curl -X POST http://localhost/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "active@example.com"}'

# Odpowiedź zawiera token (w rzeczywistej aplikacji byłby wysłany emailem)
export RESET_TOKEN="abcdef123456..."

# 2. Reset hasła
curl -X POST http://localhost/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "active@example.com",
    "token": "'$RESET_TOKEN'",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

## Limity i Ograniczenia

### Rate Limiting
- Brak ograniczeń w wersji deweloperskiej
- W produkcji zalecane jest wprowadzenie rate limiting

### Paginacja
- Maksymalny rozmiar strony: 100 elementów
- Domyślny rozmiar strony: 15 elementów

### Upload plików
- API nie obsługuje uploadu plików w obecnej wersji

### Bulk operacje
- Masowe usuwanie: maksymalnie 100 pracowników jednocześnie

## Automatyczna Dokumentacja

API posiada automatyczną dokumentację generowaną przez Scribe:

```bash
# Generowanie dokumentacji
./vendor/bin/sail artisan scribe:generate

# Dostęp do dokumentacji
http://localhost/docs
```

Dokumentacja zawiera:
- Interaktywną dokumentację API
- Możliwość testowania endpointów
- Automatyczne przykłady żądań
- Export do formatu OpenAPI

## Bezpieczeństwo

### Autoryzacja
- Używa Laravel Sanctum z tokenami API
- Tokeny nie wygasają automatycznie
- Tylko aktywni pracownicy mogą się logować

### Walidacja
- Wszystkie dane wejściowe są walidowane
- Hasła są hashowane z bcrypt
- Email musi być unikalny

### HTTPS
- W produkcji wymagane jest używanie HTTPS
- Tokeny przesyłane w nagłówkach Authorization

## Wsparcie i Rozwój

### Wersje API
- Obecna wersja: v1 (domyślna)
- Planowane: wprowadzenie wersjonowania w przyszłości

### Kompatybilność
- API jest zgodne ze standardem REST
- Odpowiedzi w formacie JSON
- Paginacja zgodna z JSON API specification

### Rozszerzenia
API zaprojektowane z myślą o rozszerzeniach:
- Dodawanie nowych pól pracownika
- Implementacja ról i uprawnień
- Historia zmian
- Integracje z systemami HR
