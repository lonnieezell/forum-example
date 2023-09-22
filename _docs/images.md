# Images

## Overview

Images are available for threads and posts. We can attach them by drag and drop or by pasting from the clipboard.

Main rules:

- Every uploaded image is publicly visible.
- We can upload PNG and JPEG files up to 2MB in size.
- Files are stored in: `public/uploads/{userId}/{randomFileName}.{ext}`.
- We track whether each uploaded file is actually used in the message body to prevent abuse.
- We should set up `.htaccess` file with proper rules to block hotlinking.

## Database

The design of the `images` table makes it easy to delete files from disk when we introduce the option to permanently delete threads/posts in the future.

## Cron

We need to set up a cron job to delete orphaned files. Such a task should be executed once an hour.

```cli
0 * * * * php spark cleanup:images 3
```

The above task will delete files that have not been assigned to any thread/post for three hours.
