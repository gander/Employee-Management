# Przewodnik Testowania - Employee Management API

## Przegląd Testowania

Ten projekt zawiera kompletną suite testów w trzech kategoriach:
- **Testy automatyczne PHPUnit** - testy jednostkowe i funkcjonalne
- **Kolekcja Postman** - testy API z automatycznymi asercjami
- **Testy manualne** - przykłady żądań cURL

## 1. Testy Automatyczne (PHPUnit)

### Uruchomienie Wszystkich Testów

```bash
# Uruchom wszystkie testy
./vendor/bin/sail artisan test

# Uruchom z verbose output
./vendor/bin/sail artisan test -v

# Uruchom z pełnym pokryciem kodu (wymaga Xdebug)
./vendor/bin/sail artisan test --coverage
```

### Uruchomienie Konkretnych Testów

```bash
# Uruchom tylko testy Feature (endpointy API)
./vendor/bin/sail artisan test --testsuite=Feature

# Uruchom konkretny plik testowy
./vendor/bin/sail artisan test tests/Feature/AuthLoginTest.php

# Uruchom konkretną metodę testową
./vendor/bin/sail artisan test --filter="it_can_login_with_valid_credentials"

# Uruchom testy z konkretną grupą
./vendor/bin/sail artisan test --group=authentication
```

### Struktura Testów

#### AuthLoginTest.php (11 testów)
```bash
./vendor/bin/sail artisan test tests/Feature/AuthLoginTest.php
```

**Testowane scenariusze:**
- ✅ Poprawne logowanie z aktywnymi danymi
- ❌ Logowanie z nieaktywnymi danymi (powinno się nie udać)
- ❌ Logowanie z nieprawidłowymi danymi
- ❌ Walidacja pustych pól
- ❌ Walidacja nieprawidłowego formatu email
- ✅ Generowanie tokenu API
- ✅ Struktura odpowiedzi JSON

#### EmployeeListTest.php (11 testów)
```bash
./vendor/bin/sail artisan test tests/Feature/EmployeeListTest.php
```

**Testowane scenariusze:**
- ✅ Listowanie wszystkich pracowników
- ✅ Filtrowanie po imieniu i nazwisku
- ✅ Filtrowanie po email
- ✅ Filtrowanie po pozycji
- ✅ Filtrowanie po statusie aktywności
- ✅ Sortowanie po imieniu
- ✅ Sortowanie po dacie utworzenia (malejąco)
- ✅ Paginacja
- ✅ Pusta lista gdy brak pracowników
- ✅ Obsługa nieprawidłowych filtrów
- ✅ Obsługa nieprawidłowego sortowania

#### EmployeeStoreTest.php (12 testów)
```bash
./vendor/bin/sail artisan test tests/Feature/EmployeeStoreTest.php
```

**Testowane scenariusze:**
- ✅ Tworzenie pracownika z tymi samymi adresami
- ✅ Tworzenie pracownika z różnymi adresami
- ✅ Hashowanie hasła
- ❌ Walidacja wymaganych pól
- ❌ Walidacja unikalności email
- ❌ Walidacja pozycji (tylko dozwolone wartości)
- ❌ Walidacja adresów korespondencyjnych
- ❌ Próba dostępu bez autoryzacji
- ✅ Struktura odpowiedzi JSON
- ✅ Kod statusu 201 przy tworzeniu

#### EmployeeUpdateTest.php (16 testów)
```bash
./vendor/bin/sail artisan test tests/Feature/EmployeeUpdateTest.php
```

**Testowane scenariusze:**
- ✅ Częściowa aktualizacja pracownika
- ✅ Aktualizacja adresów
- ✅ Zmiana z różnych adresów na te same
- ✅ Zmiana z tych samych adresów na różne
- ✅ Hashowanie nowego hasła
- ❌ Aktualizacja nieistniejącego pracownika
- ❌ Walidacja unikalności email przy aktualizacji
- ❌ Walidacja pozycji
- ❌ Próba dostępu bez autoryzacji
- ✅ Struktura odpowiedzi JSON

### Resetowanie Bazy Danych dla Testów

```bash
# Resetuj bazę danych testową z danymi seed
./vendor/bin/sail artisan migrate:fresh --seed --env=testing

# Uruchom konkretny seeder
./vendor/bin/sail artisan db:seed --class=DatabaseSeeder --env=testing
```

## 2. Testy Postman

### Import Kolekcji

1. **Import kolekcji testów:**
```bash
# Plik kolekcji
postman/Employee_Management_API.postman_collection.json
```

2. **Import środowiska:**
```bash
# Plik środowiska
postman/Employee_Management_Environment.postman_environment.json
```

### Konfiguracja Środowiska Postman

**Zmienne środowiska:**
- `base_url`: `http://localhost/api`
- `token`: (ustawiane automatycznie po logowaniu)
- `created_employee_id`: (ustawiane automatycznie po utworzeniu)
- `created_employee_id_2`: (ustawiane automatycznie)
- `reset_token`: (ustawiane automatycznie przy reset hasła)

### Uruchomienie Testów Postman

#### Option A: Testy Interaktywne
1. Otwórz Postman
2. Ustaw środowisko "Employee Management Environment"
3. Rozpocznij od "Authentication" → "Login - Active Employee"
4. Token zostanie automatycznie ustawiony
5. Uruchamiaj kolejne testy

#### Option B: Uruchomienie Całej Kolekcji
1. Kliknij prawym na kolekcję → "Run collection"
2. Wybierz wszystkie foldery
3. Kliknij "Run Employee Management API"
4. Obserwuj wyniki testów


### Scenariusze Testów Postman

#### Folder 1: Authentication (4 testy)
- ✅ **Login - Active Employee**: Poprawne logowanie
- ❌ **Login - Inactive Employee**: Logowanie zablokowane
- ❌ **Login - Invalid Credentials**: Nieprawidłowe dane
- ❌ **Login - Validation Error**: Błędy walidacji

#### Folder 2: Employee Listing (Public) (5 testów)
- ✅ **Get All Employees**: Lista wszystkich
- ✅ **Filter by Position**: Filtrowanie po pozycji
- ✅ **Filter by Active Status**: Filtrowanie po statusie
- ✅ **Sort by Name Ascending**: Sortowanie po imieniu
- ✅ **Pagination Test**: Test paginacji

#### Folder 3: Employee Management (Protected) (7 testów)
- ✅ **Create Employee - Same Addresses**: Tworzenie z tym samym adresem
- ✅ **Create Employee - Different Addresses**: Tworzenie z różnymi adresami
- ❌ **Create Employee - Validation Error**: Błędy walidacji
- ✅ **Update Employee - Partial**: Częściowa aktualizacja
- ✅ **Update Employee - Change Addresses**: Zmiana adresów
- ❌ **Update Employee - Not Found**: Nieistniejący pracownik

#### Folder 4: Employee Details (Protected) (3 testy)
- ✅ **Show Employee - Success**: Pobranie danych pracownika
- ❌ **Show Employee - Not Found**: Nieistniejący pracownik
- ❌ **Show Employee - Unauthorized**: Brak autoryzacji

#### Folder 5: Employee Deletion (Protected) (6 testów)
- ✅ **Delete Employee - Success**: Usunięcie pracownika
- ❌ **Delete Employee - Not Found**: Nieistniejący pracownik
- ❌ **Delete Employee - Unauthorized**: Brak autoryzacji
- ✅ **Bulk Delete - Success**: Masowe usuwanie
- ❌ **Bulk Delete - Validation Error**: Błędy walidacji
- ❌ **Bulk Delete - Unauthorized**: Brak autoryzacji

#### Folder 6: Password Management (6 testów)
- ✅ **Forgot Password - Success**: Generowanie tokenu reset
- ❌ **Forgot Password - User Not Found**: Nieistniejący użytkownik
- ❌ **Forgot Password - Validation Error**: Błędy walidacji
- ✅ **Reset Password - Success**: Reset hasła
- ❌ **Reset Password - Invalid Token**: Nieprawidłowy token
- ❌ **Reset Password - Validation Error**: Błędy walidacji

#### Folder 7: Authentication Tests (2 testy)
- ✅ **Get Current User Info**: Informacje o użytkowniku
- ❌ **Access Protected Route Without Token**: Brak autoryzacji

**Łącznie: 33 testy z automatycznymi asercjami**

## 3. Testy Manualne (cURL)

### Przygotowanie Środowiska

```bash
# Uruchom aplikację
./vendor/bin/sail up -d

# Zresetuj bazę danych z danymi testowymi
./vendor/bin/sail artisan migrate:fresh --seed
```

### Krok 1: Uzyskanie Tokenu

```bash
# Logowanie aktywnego pracownika
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "active@example.com", 
    "password": "password123"
  }'

# Zapisz token z odpowiedzi do zmiennej
export TOKEN="1|your-token-here"
```

### Krok 2: Test Publicznych Endpointów

```bash
# Lista wszystkich pracowników
curl -X GET http://localhost/api/employees

# Filtrowanie po pozycji
curl -X GET "http://localhost/api/employees?filter[position]=front-end"

# Sortowanie i paginacja
curl -X GET "http://localhost/api/employees?sort=-created_at&page[size]=5&page[number]=1"
```

### Krok 3: Test Chronionych Endpointów

```bash
# Informacje o aktualnym użytkowniku
curl -X GET http://localhost/api/me \
  -H "Authorization: Bearer $TOKEN"

# Tworzenie nowego pracownika
curl -X POST http://localhost/api/employees \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Jan Kowalski",
    "email": "jan.kowalski@example.com",
    "position": "front-end",
    "password": "password123",
    "residential_address_country": "Polska",
    "residential_address_postal_code": "00-123",
    "residential_address_city": "Warszawa",
    "residential_address_house_number": "15A",
    "different_correspondence_address": false,
    "is_active": true
  }'

# Aktualizacja pracownika (ID=1)
curl -X PUT http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "position": "back-end",
    "average_annual_salary": 85000
  }'

# Pobranie szczegółów pracownika
curl -X GET http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN"

# Usunięcie pracownika
curl -X DELETE http://localhost/api/employees/1 \
  -H "Authorization: Bearer $TOKEN"
```

### Krok 4: Test Walidacji

```bash
# Test błędów walidacji przy tworzeniu
curl -X POST http://localhost/api/employees \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "nieprawidłowy-email",
    "position": "nieprawidłowa-pozycja"
  }'

# Test dostępu bez autoryzacji
curl -X POST http://localhost/api/employees \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Test User"
  }'
```

## 4. Zarządzanie Danymi Testowymi

### Reset Bazy Danych

```bash
# Kompletny reset z danymi seed
./vendor/bin/sail artisan migrate:fresh --seed

# Reset tylko migracji
./vendor/bin/sail artisan migrate:fresh

# Uruchomienie tylko seedera
./vendor/bin/sail artisan db:seed
```

### Konta Testowe

**Aktywny pracownik (może się logować):**
- Email: `active@example.com`
- Hasło: `password123`
- Status: aktywny

**Nieaktywny pracownik (logowanie zablokowane):**
- Email: `inactive@example.com`
- Hasło: `password123`
- Status: nieaktywny

**Dodatkowi pracownicy:**
- 15 losowych pracowników wygenerowanych przez factory
- Różne pozycje, adresy, statusy aktywności

## 5. Monitoring i Debugowanie

### Logi Aplikacji

```bash
# Podgląd logów na żywo
./vendor/bin/sail artisan tail

# Logi testów
tail -f storage/logs/laravel.log
```

### Debug Testów

```bash
# Uruchom testy z debugiem
./vendor/bin/sail artisan test --debug

# Sprawdź status bazy danych
./vendor/bin/sail artisan migrate:status

# Sprawdź połączenie z bazą
./vendor/bin/sail artisan tinker
# W tinkerze: DB::connection()->getPdo()
```

### Rozwiązywanie Problemów

**Problem: Błędy połączenia z bazą danych**
```bash
# Sprawdź status kontenerów
./vendor/bin/sail ps

# Restart kontenerów
./vendor/bin/sail down && ./vendor/bin/sail up -d
```

**Problem: Wygaśnięcie tokenu w Postman**
```bash
# Uruchom ponownie test logowania w Postman
# Token zostanie automatycznie zaktualizowany
```

**Problem: Konflikty portów**
```bash
# Sprawdź zajęte porty
netstat -tulpn | grep :80

# Zmień port w .env lub zatrzymaj inne serwisy
```

## 6. Automatyzacja Testów

### GitHub Actions / CI

```yaml
# Przykład workflow dla GitHub Actions
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test
```

### Lokalna Automatyzacja

```bash
# Skrypt do uruchomienia wszystkich testów
#!/bin/bash
echo "Uruchamianie testów PHPUnit..."
./vendor/bin/sail artisan test

echo "Sprawdzanie stylu kodu..."
./vendor/bin/sail composer ecs

echo "Wszystkie testy zakończone!"
```

## 7. Raporty Testów

### Raport PHPUnit

```bash
# Generuj raport HTML
./vendor/bin/sail artisan test --coverage-html=coverage

# Otwórz raport
open coverage/index.html
```

## Podsumowanie

Ten przewodnik obejmuje wszystkie aspekty testowania aplikacji Employee Management API:

- **50+ testów automatycznych PHPUnit** z pełnym pokryciem funkcjonalności
- **33 testy Postman** z automatycznymi asercjami 
- **Przykłady testów manualnych** z żądaniami cURL
- **Zarządzanie danymi testowymi** z seederami i fabrykami
- **Narzędzia debugowania** i rozwiązywania problemów

Aplikacja jest gotowa do testowania w każdym środowisku - od rozwoju lokalnego po środowiska CI/CD.
