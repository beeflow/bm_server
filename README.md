# Zadanie rekrutacyjne Blue Services - część REST API

Uwagi własne:
1. Testy jednostkowe są, ale pokrycie testami jest marne. 
2. Powielony kod w kontrolerze to działanie celowe.
3. Brak automatyzacji opartej o symfony/flex 

## Instalacja i konfiguracja
 
```bash
$ composer require "blue-recruitment/bm-server-bundle @dev"
```

Potem do pliku `config/bundles.php` należy dodać kod:
```php
return [
    ...
    BMServerBundle\Server\BMServerBundle::class => ['all' => true],
];
```

zaś do `config/routes/annotations.yaml`:
```yaml
bm-server-bundle:
    prefix: /api/v1/items
    resource: "@BMServerBundle/Controller/ItemController.php"
    type: annotation
```

Potem, jeżeli potrzbujemy migracji bazy, należy 
```bash
$ ./bin/console make:migration
$ ./bin/console doctrine:migrations:migrate
```

## Testy

Statyczna analiza kodu opiera się o PHPMess detector i PHP Code Sniffer (dla PSR12).

```bash
$ composer test
```

Aby naprawić kod za pomocą `phpcbf` (tam gdzie się da) wystarczy uruchomić

```bash
$ composer fix
```
