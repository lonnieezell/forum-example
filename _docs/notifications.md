# Notifications System

## Overview

The forum's notification system manages email alerts for users about activity in threads and posts they're interested in. It provides granular control over what users are notified about, with support for global settings and per-thread muting.

## Architecture

### Models

#### `NotificationSettingModel` and `NotificationSetting` Entity
Stores user-level notification preferences in the `notification_settings` table.

**Fields:**
- `user_id` - Primary key (foreign key to users table)
- `email_thread` - Notify when new posts are added to threads the user created
- `email_post` - Notify on every new post in threads the user has participated in
- `email_post_reply` - Notify only when someone replies to the user's posts
- `moderation_daily_summary` - Receive daily moderation activity summary email

**Key Methods:**
- `withThreadNotification()` - Get users with thread notification enabled
- `withPostNotification()` - Get users with post or post-reply notification enabled
- `withAnyNotification()` - Get users with any notification type enabled

---

#### `NotificationMutedModel` and `NotificationMuted` Entity
Tracks per-thread mute status in the `notification_muted` table.

**Fields:**
- `user_id` - User who muted the thread
- `thread_id` - Thread being muted
- `created_at` - When the mute was created

**Key Methods:**
- `insert(userId, threadId)` - Mute notifications for a user/thread pair
- `delete(userId, threadId)` - Unmute notifications
- `findBy(field, value)` - Find all mutes for a user or thread
- `find(userId, threadId)` - Check if a specific user/thread is muted

---

### Events

#### `NewPostEvent`
Triggered whenever a new post is created in a thread. Automatically sends email notifications to relevant users based on their settings.

**Notification Types:**

1. **Thread Notifications** - Sent to thread author when someone (not them) posts a reply
   - Only if `email_thread` is enabled
   - Only if the poster is not the thread author
   - Respects thread-level muting

2. **Post Notifications** - Sent to users who have participated in the thread
   - Only if `email_post` OR `email_post_reply` is enabled
   - Respects thread-level muting
   - Prevents duplicate notifications to same user

3. **Post Reply Notifications** - More restrictive than post notifications
   - Only if `email_post_reply` is enabled
   - Sent only if:
     - The new post is a direct reply to the user's post, OR
     - The user previously replied to the same parent post

**Muting Logic:**
- Checks the `notification_muted` table to exclude users who have muted the thread
- Uses `NewPostEvent::mutedUsers` property to cache muted user IDs
- Prevents notification even if global settings are enabled

**Duplicate Prevention:**
- Tracks notified user IDs in `NewPostEvent::notifiedUsers` property
- Prevents sending multiple emails to same user for single post
- Ensures logical notification priority (thread > post > post-reply)

**Implementation:**
```php
// Example: Triggering the event
$event = new NewPostEvent($category, $thread, $post);
$event->process();
$notificationCount = $event->getCount(); // Number of emails sent
```

---

### Commands

#### `moderation:summary` Command
Daily command that sends moderation summary emails to moderators.

**What it does:**
1. Finds all users with `moderation_daily_summary` enabled
2. Gathers moderation stats for the past day (reports approved/denied, logs)
3. Queues an email for each moderator

**Usage:**
```bash
php spark moderation:summary
```

**Schedule:** Should be run daily via cron/scheduler at specific time

---

## User Interface

### Notification Settings Page (`themes/default/account/_notifications.php`)

**Sections:**

**Threads:**
- "Send a notification for every new post in the thread I created"
- Enabled independently

**Posts:**
- "Send a notification for every new post in a thread in which I participated"
- "Send a notification only for posts that are replies to my post"
- Mutually dependent via Alpine.js (selecting one disables the other)

**Moderation (Moderators Only):**
- "Send a daily moderation summary with activity stats"

**Per-Thread Muting:**
- Available on each thread page via `MuteThreadCell`
- Shows "Mute notifications" or "Unmute notifications" button
- Only displays if user has global notifications enabled for that thread type
- Uses HTMX for instant feedback

---

## Email Templates

### Post Notification Email (`_emails/email_post_notification.php`)

**Contents:**
- Greeting with username
- Thread title
- Post author name and timestamp
- First 50 characters of post content (stripped HTML)
- Link to view thread
- Footer with unsubscribe options:
  - Permanent unsubscribe via account settings
  - One-click thread mute via signed URL

---

### Daily Moderation Summary Email (`_emails/daily_moderation_summary.php`)

**Contents:**
- Greeting
- Summary table with:
  - Reports created/reviewed
  - Posts approved/denied
  - Log entries
- Thank you message
- Footer with link to account settings

---

## Queue System Integration

Notifications are sent asynchronously via the queue service:

```php
service('queue')->push('emails', 'email-simple-message', [
    'to'      => $user->email,
    'subject' => 'New post notification',
    'message' => view('_emails/email_post_notification', [...]),
])
```

**Benefits:**
- Prevents blocking user requests during email sending
- Handles large batches of notifications efficiently
- Provides retry mechanism for failed sends

---

## Future Considerations

- **In-app Notifications** - Currently email-only, could add database-backed in-app alerts
- **Notification Digest** - Bundle multiple posts into single email digest
- **Notification Channels** - Support Slack, Discord, webhooks beyond email
