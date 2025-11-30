# Getting Started

## System Requirements

-   PHP 8.4+
-   Node 18.12.1+
-   MySQL 8.0+

## Packages Used

-   [CodeIgniter 4.6+](https://codeigniter.com/)
-   [CodeIgniter Shield](https://github.com/codeigniter4/shield)
-   [CodeIgniter Settings](https://github.com/codeigniter4/settings)
-   [CodeIgniter Tasks](https://github.com/codeigniter4/tasks)
-   [CodeIgniter Queue](https://github.com/codeigniter4/queue)
-   [Commonmark](https://github.com/thephpleague/commonmark)
-   [TextFormatter](https://github.com/s9e/TextFormatter)
-   [ParcelJS](https://parceljs.org/)
-   [htmx](https://htmx.org/)
-   [AlpineJS](https://alpinejs.dev/)
-   [Tailwind](https://tailwindcss.com/)
-   [DaisyUI](https://daisyui.com/)
-   [HeroIcons](https://heroicons.com/)
-   [EasyMDE](https://github.com/Ionaru/easy-markdown-editor)

## Initial Setup

Once the repository has been installed on your local machine you need to get a few things setup:

```cli
composer install  (only needed if you directly downloaded the files)
npm install
php spark migrate --all
php spark db:seed SampleDataSeeder  (only if you want sample forums, users, etc created)
```

Setup a task for [cron](https://en.wikipedia.org/wiki/Cron) scheduler:

```cli
* * * * * cd /path-to-your-project && php spark tasks:run >> /dev/null 2>&1
```

## Running the Application

For local development, you can use CodeIgniter's built-in development server. You also need to run the Vite development server for frontend assets.

```cli
php spark serve
npm run dev
```

You can now access the application at `http://localhost:8080`.

## Sending emails

Setup some Email Sandbox. Provide credentials - preferably using the `.env` file.
Don't forget to set up `fromEmail` and `fromName` variables too, because without them emails won't be sent.
