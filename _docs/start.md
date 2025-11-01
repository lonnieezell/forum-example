# Getting Started

## System Requirements

-   PHP 8.1+
-   Node 18.12.1+
-   MySQL 8.0+

## Packages Used

-   [CodeIgniter 4.4+](https://codeigniter.com/)
-   [CodeIgniter Shield](https://github.com/codeigniter4/shield)
-   [CodeIgniter Settings](https://github.com/codeigniter4/settings)
-   [Commonmark](https://github.com/thephpleague/commonmark)
-   [TextFormatter](https://github.com/s9e/TextFormatter)
-   [htmx](https://htmx.org/)
-   [AlpineJS](https://alpinejs.dev/)
-   [Tailwind](https://tailwindcss.com/)
-   [DaisyUI](https://daisyui.com/)

## Initial Setup

Once the repository has been installed on your local machine you need to get a few things setup:

```cli
composer install  (only needed if you directly downloaded the files)
npm install
php spark migrate --all
php spark db:seed SampleDataSeeder  (only if you want sample forums, users, etc created)
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
