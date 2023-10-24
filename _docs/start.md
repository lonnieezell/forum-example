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
-   [ParcelJS](https://parceljs.org/)
-   [htmx](https://htmx.org/)
-   [AlpineJS](https://alpinejs.dev/)
-   [Tailwind](https://tailwindcss.com/)
-   [DaisyUI](https://daisyui.com/)

## Initial Setup

Once the reposoitory has been installed on your local machine you need to get a few things setup:

```cli
composer install  (only needed if you directly downloaded the files)
npm install
php spark migrate --all
php spark db:seed SampleDataSeeder  (only if you want sample forums, users, etc created)
```

## Frontend Development

We use [ParcelJS]() to handle compiling the frontend assets, including SASS and Javascript. When working on frontend code, ensure you have parcel running:

```cli
npm run dev
```

## Sending emails

Setup some Email Sandbox. Provide credentials - preferably using the `.env` file. 
Don't forget to set up `fromEmail` and `fromName` variables too, because without them emails won't be sent.
