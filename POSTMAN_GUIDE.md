# Przewodnik Testowania z Postman - Employee Management API

## Przegląd

Ten przewodnik pokazuje krok po kroku jak używać kolekcji Postman do testowania Employee Management API. Kolekcja zawiera 33 testy obejmujące wszystkie endpointy z automatycznymi asercjami.

## Wymagania

- **Postman Desktop** (najnowsza wersja) lub **Postman Web**
- **Uruchomiona aplikacja** na `http://localhost`

## Krok 1: Import Plików

### Import Kolekcji Testów

1. Otwórz Postman
2. Kliknij **"Import"** w lewym górnym rogu
3. Przeciągnij i upuść lub wybierz plik:
   ```
   postman/Employee_Management_API.postman_collection.json
   ```
4. Kliknij **"Import"**

### Import Środowiska

1. Kliknij **"Import"** ponownie
2. Wybierz plik:
   ```
   postman/Employee_Management_Environment.postman_environment.json
   ```
3. Kliknij **"Import"**
4. **WAŻNE**: Ustaw środowisko jako aktywne:
   - Kliknij dropdown w prawym górnym rogu
   - Wybierz **"Employee Management Environment"**

## Krok 2: Konfiguracja Środowiska

### Sprawdzenie Zmiennych

Kliknij ikonę oka obok dropdown środowiska, aby sprawdzić zmienne:

| Zmienna                 | Wartość Początkowa     | Opis                                       |
|-------------------------|------------------------|--------------------------------------------|
| `base_url`              | `http://localhost/api` | URL bazowy API                             |
| `token`                 | `""`                   | Token autoryzacji (ustawiony po logowaniu) |
| `created_employee_id`   | `""`                   | ID utworzonego pracownika                  |
| `created_employee_id_2` | `""`                   | ID drugiego pracownika                     |
| `reset_token`           | `""`                   | Token resetowania hasła                    |

### Dostosowanie URL (jeśli potrzebne)

Jeśli aplikacja działa na innym porcie:
1. Kliknij ikonę oka → **"Edit"**
2. Zmień `base_url` na właściwy adres (np. `http://localhost:8000/api`)

## Krok 3: Pierwszą Test - Logowanie

### Uruchomienie Logowania

1. Rozwiń folder **"Authentication"**
2. Kliknij **"Login - Active Employee"**
3. Sprawdź dane w body:
   ```json
   {
     "email": "active@example.com",
     "password": "password123"
   }
   ```
4. Kliknij **"Send"**

### Sprawdzenie Wyniku

**Oczekiwana odpowiedź (Status: 200):**
```json
{
  "message": "Login successful",
  "token": "1|abcdef...",
  "employee": {
    "id": 1,
    "full_name": "Active Employee",
    "email": "active@example.com",
    ...
  }
}
```

### Automatyczne Ustawienie Tokenu

Po udanym logowaniu sprawdź zakładkę **"Test Results"**:
- ✅ Status code is 200
- ✅ Response has token
- ✅ Response has employee data

Token zostanie automatycznie zapisany w zmiennej `token`.

## Krok 4: Testowanie Endpointów Publicznych

### Folder: Employee Listing (Public)

Te testy nie wymagają autoryzacji:

#### 1. Get All Employees
```
GET {{base_url}}/employees
```

#### 2. Filter by Position
```
GET {{base_url}}/employees?filter[position]=front-end
```

#### 3. Filter by Active Status
```
GET {{base_url}}/employees?filter[is_active]=true
```

#### 4. Sort by Name Ascending
```
GET {{base_url}}/employees?sort=full_name
```

#### 5. Pagination Test
```
GET {{base_url}}/employees?page[size]=5&page[number]=1
```

**Uruchomienie:**
1. Kliknij każdy test z kolei
2. Sprawdź zakładkę **"Test Results"**
3. Wszystkie testy powinny przejść (zielone ✅)

## Krok 5: Testowanie Endpointów Chronionych

### Folder: Employee Management (Protected)

Te testy wymagają tokenu autoryzacji (automatycznie dodawanego):

#### 1. Create Employee - Same Addresses
Tworzy pracownika z tym samym adresem korespondencyjnym:
```json
{
  "full_name": "Jane Smith",
  "email": "jane.smith@example.com",
  "position": "designer",
  "password": "password123",
  "residential_address_country": "Poland",
  "residential_address_postal_code": "00-001",
  "residential_address_city": "Krakow",
  "residential_address_house_number": "10",
  "residential_address_apartment_number": "5",
  "different_correspondence_address": false,
  "is_active": true
}
```

Po utworzeniu ID pracownika zostanie zapisane w `created_employee_id`.

#### 2. Create Employee - Different Addresses
Tworzy pracownika z różnymi adresami:
```json
{
  "full_name": "John Wilson",
  "email": "john.wilson@example.com",
  "position": "back-end",
  "password": "password123",
  "average_annual_salary": 85000.50,
  "residential_address_country": "Poland",
  "residential_address_postal_code": "00-123",
  "residential_address_city": "Warsaw",
  "residential_address_house_number": "15A",
  "different_correspondence_address": true,
  "correspondence_address_country": "Germany",
  "correspondence_address_postal_code": "10115",
  "correspondence_address_city": "Berlin",
  "correspondence_address_house_number": "22",
  "correspondence_address_apartment_number": "10",
  "is_active": true
}
```

#### 3. Update Employee - Partial
Częściowa aktualizacja pierwszego pracownika:
```json
{
  "position": "pm",
  "average_annual_salary": 90000.00
}
```

URL używa zmiennej: `{{base_url}}/employees/{{created_employee_id}}`

## Krok 6: Testowanie Szczegółów Pracownika

### Folder: Employee Details (Protected)

#### 1. Show Employee - Success
```
GET {{base_url}}/employees/{{created_employee_id}}
```

#### 2. Show Employee - Not Found
```
GET {{base_url}}/employees/99999
```
Oczekiwany status: 404

#### 3. Show Employee - Unauthorized
```
GET {{base_url}}/employees/1
```
Bez nagłówka Authorization - oczekiwany status: 401

## Krok 7: Testowanie Usuwania

### Folder: Employee Deletion (Protected)

#### 1. Delete Employee - Success
```
DELETE {{base_url}}/employees/{{created_employee_id}}
```

#### 2. Bulk Delete - Success
```json
{
  "employee_ids": [{{created_employee_id_2}}]
}
```

## Krok 8: Testowanie Zarządzania Hasłami

### Folder: Password Management

#### 1. Forgot Password - Success
```json
{
  "email": "active@example.com"
}
```

Token reset zostanie zapisany w `reset_token`.

#### 2. Reset Password - Success
```json
{
  "email": "active@example.com",
  "token": "{{reset_token}}",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

## Krok 9: Uruchomienie Całej Kolekcji

### Opcja A: Runner w Postman

1. Kliknij prawym przyciskiem na kolekcję
2. Wybierz **"Run collection"**
3. **Ustaw kolejność folderów:**
   ```
   1. Authentication
   2. Employee Listing (Public)
   3. Employee Management (Protected)
   4. Employee Details (Protected)
   5. Employee Deletion (Protected)
   6. Password Management
   7. Authentication Tests
   ```
4. Kliknij **"Run Employee Management API"**

## Krok 10: Interpretacja Wyników

### Sukces Testów

**W Postman Runner:**
- Zielony pasek: wszystkie testy przeszły
- Licznik: "33/33 tests passed"


### Błędy Testów

**Najczęstsze problemy:**

1. **Aplikacja nie działa:**
   ```
   Error: connect ECONNREFUSED 127.0.0.1:80
   ```
   **Rozwiązanie:** Uruchom `./vendor/bin/sail up -d`

2. **Brak danych testowych:**
   ```
   Error: Response status was 404, but expected 200
   ```
   **Rozwiązanie:** `./vendor/bin/sail artisan migrate:fresh --seed`

3. **Wygasły token:**
   ```
   Error: Unauthenticated
   ```
   **Rozwiązanie:** Uruchom ponownie test logowania

## Krok 11: Dostosowywanie Testów

### Edycja Żądań

1. Kliknij na dowolny test
2. Zakładka **"Headers"** - sprawdź nagłówki
3. Zakładka **"Body"** - edytuj dane
4. Zakładka **"Tests"** - sprawdź asercje

### Dodawanie Własnych Testów

**Przykład nowego testu:**
```javascript
pm.test("Custom test - Response time", function () {
    pm.expect(pm.response.responseTime).to.be.below(2000);
});

pm.test("Custom test - Email format", function () {
    var jsonData = pm.response.json();
    if (jsonData.employee) {
        pm.expect(jsonData.employee.email).to.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
    }
});
```

### Modyfikacja Środowiska

Dodawanie nowych zmiennych:
1. Kliknij ikonę oka → **"Edit"**
2. Dodaj nową zmienną, np:
   ```
   custom_endpoint: /api/custom
   timeout: 5000
   ```

## Krok 12: Zaawansowane Scenariusze

### Test Workflow - Pełny Cykl Życia Pracownika

1. **Logowanie** → ustaw token
2. **Tworzenie pracownika** → zapisz ID
3. **Pobranie szczegółów** → sprawdź dane
4. **Aktualizacja** → zmień pozycję
5. **Ponowne pobranie** → zweryfikuj zmiany
6. **Usunięcie** → usuń pracownika
7. **Próba pobrania** → sprawdź 404

### Test Błędów Walidacji

Wszystkie testy z oczekiwanymi błędami (status 4xx):
- Login z nieaktywnymi danymi (401)
- Tworzenie z nieprawidłowymi danymi (422)
- Dostęp bez autoryzacji (401)
- Nieistniejący zasób (404)

### Test Performance

```javascript
pm.test("Response time is acceptable", function () {
    pm.expect(pm.response.responseTime).to.be.below(1000);
});
```

## Krok 13: Rozwiązywanie Problemów

### Problem: Nie można zaimportować plików

**Rozwiązanie:**
1. Sprawdź czy pliki istnieją w folderze `postman/`
2. Upewnij się że masz uprawnienia do odczytu
3. Spróbuj skopiować zawartość JSON i wkleić bezpośrednio

### Problem: Testy nie przechodzą

**Kroki debugowania:**
1. Sprawdź Console w Postman (View → Show Postman Console)
2. Sprawdź Response w zakładce
3. Porównaj z oczekiwaną strukturą w teście

### Problem: Zmienne nie są ustawiane

**Rozwiązanie:**
1. Sprawdź zakładkę **"Tests"** w żądaniu logowania
2. Upewnij się że kod JavaScript wykonuje się bez błędów:
   ```javascript
   pm.environment.set('token', jsonData.token);
   ```


## Wskazówki i Najlepsze Praktyki

### 1. Kolejność Testów
Zawsze uruchamiaj testy w określonej kolejności:
1. Authentication (ustaw token)
2. Public endpoints
3. Protected endpoints
4. Cleanup (deletion)

### 2. Zarządzanie Tokenami
- Token jest automatycznie ustawiany po logowaniu
- Token nie wygasa podczas sesji testowej
- Przy problemach uruchom ponownie logowanie

### 3. Dane Testowe
- Baza jest resetowana przy każdym `db:seed`
- Email musi być unikalny przy tworzeniu pracowników
- Użyj zmiennych Postman dla ID zamiast hardcodowania

### 4. Monitoring
- Sprawdzaj zakładkę "Test Results" po każdym żądaniu
- Console Postman pokazuje szczegóły błędów

## Podsumowanie

Ten przewodnik obejmuje kompletny proces testowania API z Postman:

- ✅ **33 gotowe testy** z automatycznymi asercjami
- ✅ **Automatyczne zarządzanie tokenami** i zmiennymi
- ✅ **Kompletne pokrycie API** - wszystkie endpointy
- ✅ **Testy błędów i walidacji** dla robustności
- ✅ **Szczegółowe raporty** i monitoring

Kolekcja jest gotowa do użycia w każdym środowisku - od testów lokalnych po CI/CD pipelines.
