## Задание №1

- ### 1. **Подключение к БД нужно вынести в отдельный класс**
Вынесение подключения к базе данных в отдельный класс улучшает читаемость, повторное использование и централизует управление подключением.

- ### 2. **Уязвимость к SQL-инъекциям**

Ключевая проблема — пользовательский ввод (`$_GET['id']`) напрямую используется в SQL-запросе. Это делает приложение уязвимым к SQL-инъекциям. Например, если в URL передать `?id=1; DROP TABLE users;`, злоумышленник может разрушить данные.

#### **Решение:**

Использовать подготовленные выражения (prepared statements), которые экранируют данные, предотвращая SQL-инъекции.

- ### 3. **Отсутствие проверки и валидации входных данных**

Ввод пользователя напрямую передаётся в запрос без проверки его корректности. `$_GET['id']` может быть строкой, null или другим неожиданным значением.

#### **Решение:**

Добавить валидацию:

- Проверить наличие параметра.
- Убедиться, что `id` — это целое число.

 - ### 4. **Ошибка типов данных**

Даже если пользователь передал корректный ID, база данных может ожидать `INT`, а строковое значение вызовет ошибку или неожиданное поведение.

#### **Решение:**

Приведение типов

- ### 5. **Отсутствие обработки ошибок базы данных**

Если база данных недоступна или произошла ошибка при выполнении запроса, приложение продолжит работать без явного уведомления, что может привести к непредсказуемому поведению или утечке информации.

#### **Решение:**

Добавить обработку ошибок подключения и запросов

- ### 6. **Отсутствие закрытия соединения**

Объект `$mysqli` остаётся открытым до завершения скрипта, что может привести к утечке ресурсов, особенно если скрипт используется в длительных процессах.

#### **Решение:**

Закрывать соединение после выполнения всех операций:

- ### 7. **Потенциальный риск XSS**

Если данные из базы данных содержат вредоносный код (например, `<script>alert('XSS');</script>`), он может быть передан в браузер пользователя без фильтрации, открывая уязвимость к XSS.

#### **Решение:**

Экранировать выводимые данные

- ### 8. **Отсутствие проверки на пустые результаты**

Если пользователь с переданным `id` не найден, код попытается работать с `$user`, который будет равен `null`. Это вызовет предупреждения или ошибки.

#### **Решение:**

Проверить пользователя на пустое значение

## Задание №2
Ошибки, которые повторяются, я не писал здесь
- ### 1. Производительность:

    - На каждую строку из таблицы `questions` выполняется отдельный запрос к таблице `users`.
    - Это приводит к большому количеству запросов (проблема **N+1**).
- ### 2. Читаемость и поддерживаемость:
    - Логика обработки данных разбросана, что делает код менее читаемым.
- ### 3. Управление ресурсами:
    - Нет общего механизма обработки ошибок для запросов.
    - Ручное освобождение результатов (`free`) увеличивает сложность.

## Задание №3
```sql
SELECT 
    u.name AS user_name,
    u.phone AS user_phone,
    COALESCE(SUM(o.subtotal), 0) AS total_orders_sum,
    COALESCE(AVG(o.subtotal), 0) AS average_order_sum,
    MAX(o.created) AS last_order_date
FROM 
    users u
LEFT JOIN 
    orders o ON u.id = o.user_id
GROUP BY 
    u.id, u.name, u.phone
ORDER BY 
    total_orders_sum DESC;
```

## Задание №4
```sql
SELECT 
    e.departament_id,
    MAX(e.salary) AS max_salary
FROM 
    employees e
GROUP BY 
    e.departament_id;
```

```sql
SELECT 
    e.id,
    e.name,
    e.lastname,
    e.departament_id,
    e.salary
FROM 
    employees e
WHERE 
    departament_id = 3 AND salary > 90000;
```

```sql
CREATE INDEX idx_departament_id ON employees (departament_id);
CREATE INDEX idx_salary ON employees (salary);
CREATE INDEX idx_departament_salary ON employees (departament_id, salary);
```

## Задание №5
- ### 1. Ошибка в проверке `item.price`:**
  - Используется одиночный оператор присваивания `=` вместо сравнения `===`.
  - Это приведёт к изменению значения, а не к проверке.
- ### 2. Неинициализированная переменная `orderSubtotal`:
  - Переменная `orderSubtotal` нигде не объявлена и не инициализирована.
- ### 3. Логическая ошибка в тернарном операторе:
  - `total > 0 ? 'Бесплатно' : total + ' руб.'` неправильно использует переменную `total`, которая не определена.
- ### 4. Логика условия:
  - Если `orderSubtotal` больше 0, надпись "Бесплатно" не имеет смысла. Условие нужно изменить.

```JS
function printOrderTotal(responseString) {
    try {
        const responseJSON = JSON.parse(responseString);
        
        if (!Array.isArray(responseJSON)) {
            console.error('Некорректный формат данных. Ожидается массив объектов.');
            
            return;
        }
        
        const orderSubtotal = responseJSON.reduce((subtotal, item) => {
            const price = item.price !== undefined ? item.price : 0;
            
            return subtotal + price;
        }, 0);
        
        console.log(
            orderSubtotal > 0
                ? `Стоимость заказа: ${orderSubtotal} руб.`
                : 'Стоимость заказа: Бесплатно'
        );
    } catch (error) {
        console.error('Ошибка обработки ответа: ', error.message);
    }
}
```