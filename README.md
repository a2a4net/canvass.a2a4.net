## Розгортання
1) Попередньо створити БД, додати облікові дані в файл оточення .ENV<br/>
   `DB_CONNECTION=mariadb`<br/>
   `DB_HOST=`<br/>
   `DB_PORT=`<br/>
   `DB_DATABASE=`<br/>
   `DB_USERNAME=`<br/>
   `DB_PASSWORD=`<br/>
2) Застосувати міграції `php artisan migrate`
3) Встановити залежності командою `npm install`
4) Зібрати фронтенд з Vite `npm run build`
5) Додати планувальник до CRON
   `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`

## Команди
`app:CalculateAnalyticsProgress {from} {to?}` Розрахунок прогресу працівників<br/>
`app:MakeSchedule {year} {month} {run?}` Планування завдань для Працівників на місяць. Опційно - симуляція обходу<br/>
`app:OverpassImport` Імпорт тестових координат з overpass-api<br/>
`app:RunSchedule` Симуляція обходу. Працює в будні
