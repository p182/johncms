# [JohnCMS](http://johncms.com)

Система управления сайтом JohnCMS предназначена для построения сайтов, которые будут просматриваться с мобильных телефонов.
Соответствует спецификации XHTML Mobile Profile и имеет небольшой размер генерируемых страниц.

## Основные возможности системы:
- высокий уровень безопасности
- быстрая в работе, построена на MySQL
- продвинутая система разграничений прав для админов/модеров
- форум с возможностью закрепления/закрытия тем, возможность скачать
  тему в .txt формате, возможность прикрепления файлов в теме и многое другое...
- гостевая книга
- Админ - клуб
- чат с викториной, и интимом.
- продвинутая библиотека с неограниченной вложенностью разделов и
  возможностью для посетителей сайта публиковать свои статьи.
  Есть модерация статей, которые опубликовали посетители.
  Автоматическая компиляция Java книг.
- продвинутая фотогаллерея с возможностью для пользователей
  создавать свои личные фотоальбомы.
- загруз центр с неограниченной вложенностью разделов, счетчиком,
  рейтингом и камментами.
- приват (личная почта) с возможностью прикрепления файлов
- удобная работа со смайлами
- смена стилей
- и многое другое...

## Установка
1. Распаковываем архив
2. Заливаем все распакованные файлы на хостинг
3. Выставляем права доступа 777:  
   /incfiles  
   /gallery/foto  
   /gallery/temp  
   /library/files/  
   /library/java/  
   /library/java/META-INF/  
   /library/temp/  
   /pratt/  
   /forum/files/  
   /forum/temtemp/  
   /download/arctemp/  
   /download/files/  
   /download/graftemp/  
   /download/screen/  
   /download/mp3temp/  
   /download/upl/  
4. Выставляем права доступа 666:  
   /flood.dat
5. Запускаем инсталлятор по адресу http://ваш_сайт/install
6. Запустится процедура проверки.  
   Если какие-то пункты выделены красным цветом, то нормальная работа системы не гарантируется. Чаще всего проблемы возникают при неправильных настройках PHP вашего хостинга, или из-за слишком сильных ограничений. Например, на некоторых хостингах не работает .htaccess  
   Если проверка прошла нормально, жмем ссылку "Продолжить"
7. Вводим параметры Вашей базы данных MySQL  
   Обратите внимание, что когда указываете адрес сервера MySQL, то на некоторых хостингах это может быть "localhost", а на других нужно указывать полный адрес, например "mysql.myhost.com"  
   После жмем ссылку "Продолжить"
8. Вводим данные Администратора системы и жмем ссылку "Продолжить"
9. Если все было введено правильно, то запустится процедура установки базы данных MySQL, после завершения которой, установка завершена.
