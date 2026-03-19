<?php
/**
 * КРАСИВАЯ ВЕРСИЯ phpMyAdmin
 * Со стандартной страницей входа
 */

// Секретный ключ для шифрования cookie (обязателен для красивой версии)
$cfg['blowfish_secret'] = '7040DF61B592E6F8CEBE4CAE84F9D7FD';

// Настройка сервера MySQL
$i = 1;

// ВКЛЮЧАЕМ КРАСИВУЮ АУТЕНТИФИКАЦИЮ (со страницей входа)
$cfg['Servers'][$i]['auth_type'] = 'cookie';

// Хост MySQL - для Open Server 6 используйте ИМЯ МОДУЛЯ!
$cfg['Servers'][$i]['host'] = 'MySQL-8.0';  // или 'localhost' если MySQL-8.0 не работает

$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = true;  // Разрешаем вход без пароля

// ОТКЛЮЧАЕМ ВСЕ SSL/HTTPS (чтобы работало по HTTP)
$cfg['ForceSSL'] = false;
$cfg['LoginCookieSecure'] = false;
$cfg['LoginCookieSameSite'] = 'Lax';  // Для современных браузеров

// URL вашего phpMyAdmin (ОБЯЗАТЕЛЬНО ИЗМЕНИТЕ НА ВАШ!)
$cfg['PmaAbsoluteUri'] = 'http://pam/';  // Или 'http://localhost/phpmyadmin/'

// Настройки внешнего вида
$cfg['ThemeDefault'] = 'pmahomme';  // Красивая тема по умолчанию
$cfg['ThemeManager'] = true;        // Разрешаем менять темы

// Настройки навигации
$cfg['NavigationTreeEnableGrouping'] = false;  // Отключаем группировку БД
$cfg['FirstLevelNavigationItems'] = 100;       // Показывать больше БД
$cfg['MaxNavigationItems'] = 100;               // Показывать больше таблиц

// Настройки отображения
$cfg['ShowAll'] = true;              // Показывать кнопку "показать все"
$cfg['MaxRows'] = 50;                 // Записей на странице
$cfg['RowActionType'] = 'both';       // Иконки и текст в действиях
$cfg['RepeatCells'] = 100;            // Повторять заголовки каждые 100 строк
$cfg['ShowDbStructureCreation'] = true;  // Показывать дату создания
$cfg['ShowDbStructureLastUpdate'] = true; // Показывать дату обновления
$cfg['ShowDbStructureLastCheck'] = true;  // Показывать дату проверки

// Настройки запросов
$cfg['QueryHistoryDB'] = false;       // Хранить историю в браузере
$cfg['QueryHistoryMax'] = 25;          // Максимум запросов в истории
$cfg['ShowSQL'] = true;                // Показывать SQL-запросы
$cfg['Confirm'] = true;                // Спрашивать подтверждение на опасные действия

// Настройки экспорта/импорта
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['TempDir'] = '';                  // Временная папка

// Настройки редактора
$cfg['TextareaCols'] = 40;             // Ширина текстового поля
$cfg['TextareaRows'] = 7;               // Высота текстового поля
$cfg['LongtextDoubleTextarea'] = true;  // Двойной размер для длинных текстов
$cfg['CharEditing'] = 'textarea';       // Редактирование char полей
$cfg['CharTextareaCols'] = 40;          // Ширина для char полей
$cfg['CharTextareaRows'] = 2;           // Высота для char полей

// Настройки по умолчанию для новых таблиц
$cfg['DefaultTabDatabase'] = 'structure';  // Вкладка по умолчанию для БД
$cfg['DefaultTabTable'] = 'browse';        // Вкладка по умолчанию для таблиц
$cfg['DefaultOrderBy'] = 'ASC';             // Сортировка по умолчанию

// Включаем поддержку всех языков
$cfg['DefaultLang'] = 'ru';                 // Язык по умолчанию - русский
$cfg['Lang'] = 'ru';
?>