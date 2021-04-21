# Тестовое задание PHP

## Требования

  - PHP 7.4
  - MYSQL 5.7

### Установка
 - git clone https://github.com/elcavaliere/format-two-php-api.git
 - cd format-two-php-api

### Запуск проекта
  - Запускаем composer install
    После установки вы увидите сообщение ниже
    
    Script robocopy vendor/swagger-api/swagger-ui/dist  public/swagger-ui-assets /s /e handling the post-install-cmd event returned with error code 1
    
    которое вы можете полностью игнорировать, потому что он связан с выполнением скрипта, написанного в composer.js файле, который копирует только файлы из папки
    vendor / swagger-api / swagger-ui / dist в public / swagger-ui-assets папку.
    
  - создать файл .env на основной папке проекта.
  - скопировать содержимое .env.example в файл .env.
  
  - создать базу данных с именем factor_two_php_api;
  - сконфигурируйте пользователя root вашей базы данных в файле .env
  
  - Запустите php artisan swagger-lume: publish-views для публикации представлений (resources / views / vendor / swagger-lume) для пакета swagger-lume
  - Запустите php artisan serve, чтобы запустить локальный сервер
  - Скопируйте ссылку 127.0.0.1:8000 в свой браузер, и вы будете перенаправлены в документацию по API.
   
