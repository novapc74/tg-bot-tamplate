```php
$jsonString = '{\"name\": \"John\", \"age\": 30}';
$jsonArray = json_decode($jsonString);

if ($jsonArray === null && json_last_error() !== JSON_ERROR_NONE) {
    echo 'Invalid JSON format';
    } else {
    echo 'Valid JSON format';
}
```

```php
$jsonString = '{"name": "John", "age: 30}'; // некорректный JSON
$jsonArray = json_decode($jsonString);

if ($jsonArray === null && json_last_error() !== JSON_ERROR_NONE) {
    echo 'Invalid JSON format';
    } else {
    echo 'Valid JSON format';
}
```