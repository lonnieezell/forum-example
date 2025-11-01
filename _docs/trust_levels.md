# Trust Levels

Trust levels are a way of granting different capabilities to different users based on their activity and engagement with the community. These are inspired by the [trust level system](https://blog.discourse.org/2018/06/understanding-discourse-trust-levels/) used by Discourse.

## Overview

The forum has four trust levels that users progress through as they engage with the community:

| Level | Name | Description |
|-------|------|-------------|
| 0 | New Member | Initial level, limited posting ability |
| 1 | Member | Standard community member with most permissions |
| 2 | Regular | Engaged member with additional privileges |
| 3 | Leader | Highly trusted member with all permissions |

---

## Trust Level Features & Permissions

### Level 0: New Member
**Allowed Actions:**
- None (base restrictions apply)

**Restrictions:**
- Can create up to 3 threads
- Can create up to 10 posts before advancing
- Cannot report content
- Cannot attach files
- Cannot include links in signature
- Cannot send private messages

### Level 1: Member
**Allowed Actions:**
- Report threads/posts
- Attach files to posts
- Include links in user signature
- Create more than 3 discussions
- Post more than 10 replies
- Edit own content after 24 hours
- Send private messages

**Requirements to reach:**
- 5+ new threads created

### Level 2: Regular
**Allowed Actions:**
- All Level 1 actions

**Requirements to reach:**
- 15+ daily visits
- 1+ likes given
- 1+ likes received
- 3+ replies/posts
- 20+ new threads created

### Level 3: Leader
**Allowed Actions:**
- All previous levels' actions

**Requirements to reach:**
- 50+ daily visits
- 20+ likes given
- 30+ likes received
- 10+ replies/posts

---

## Requirements & Thresholds

The system uses the following metrics to determine trust level eligibility:

### Metric Definitions

- **daily-visits**: Number of unique days the user has visited the forum
- **likes-given**: Total likes/reactions the user has given to posts/threads
- **likes-received**: Total likes/reactions the user has received on their posts/threads
- **replies-given**: Total posts/replies created by the user
- **new-threads**: Total discussion threads created by the user

### Threshold Values

**Post Creation Restrictions:**
- `POST_THRESHOLD = 10` - Users at Level 0 limited to 10 posts before advancing
- `THREAD_THRESHOLD = 3` - Users at Level 0 limited to 3 threads before advancing

---

## Available Actions Reference

Actions that can be restricted by trust level:

| Action | Description | Default Level |
|--------|-------------|----------------|
| `report` | Report a thread or post for moderation | 1 |
| `attach` | Attach files to threads/posts | 1 |
| `link-signature` | Include links in user signature | 1 |
| `start-discussion` | Create more than 3 discussions | 1 |
| `reply` | Post more than 10 replies | 1 |
| `edit-own` | Edit own content after 24 hours | 1 |
| `send-pm` | Send private messages to other users | 1 |

These can all be customized by administrators through the trust level settings panel.

---

## The Assignment Algorithm

Trust levels are automatically assigned using the `Assigner` class based on user activity metrics. The assignment process works as follows:

### Assignment Steps

1. **Current Level Check**: Verifies if the user still meets requirements for their current trust level
   - If requirements are met, proceed to upgrade check
   - If not met, proceed to downgrade check

2. **Upgrade Check**: If user meets current level requirements, check if they qualify for the next higher level
   - If yes, upgrade them
   - Continue checking for higher levels until requirements aren't met
   - User retains highest level they qualify for

3. **Downgrade Check**: If user doesn't meet current level requirements
   - Iterate downward through levels until finding one they meet requirements for
   - Assign that level
   - If no level is met, assign Level 0 (New Member)

4. **Persistence**: If trust level changed, update the user record in database

### Eligibility

The assignment process only runs on:
- Active users (account status is active)
- Recently active users (last active within 4 months)

### All Requirements Must Be Met

For a user to advance to a trust level, they must meet **ALL** requirements for that level. For example, to reach Level 2, a user must have:
- **AND** 15+ daily visits
- **AND** 1+ likes given
- **AND** 1+ likes received
- **AND** 3+ replies/posts
- **AND** 20+ new threads

---

## Running Trust Level Updates

Trust levels are automatically assigned during a scheduled task (can be configured via cron job). The task can also be run manually from the CLI:

```bash
php spark trust-levels:set
```

This command:
- Scans all active users (active=true and active within last 4 months)
- Calculates appropriate trust level for each user
- Updates trust levels if they've changed
- Displays progress during execution

---

## Checking Trust Level in Code

You can check whether a user can be trusted to perform a certain action by using the `canTrustTo()` method on the `User` entity. This method accepts an action string and returns a boolean.

**Example:**
```php
if ($user->canTrustTo('report')) {
    // User can report content
}

if ($user->canTrustTo('attach')) {
    // User can attach files
}

if ($user->canTrustTo('send-pm')) {
    // User can send private messages
}
```

**Superadmins**: Automatically return `true` for any `canTrustTo()` check, bypassing all restrictions.

**Invalid Levels**: Returns `false` if the user has no trust level set or an invalid level.

---

## Use in Policies

Trust levels are enforced throughout the application via policies:

### Thread Policy (`ThreadPolicy`)

**Thread Creation:**
```php
// Users at Level 0 can only create up to THREAD_THRESHOLD (3) threads
if (!$user->canTrustTo('start-discussion') && $user->thread_count >= TrustLevels::THREAD_THRESHOLD) {
    return false; // Denied
}
```

**Thread Editing:**
- Level 0: Can only edit own threads within 24 hours of creation
- Level 1+: Can edit own threads via `canTrustTo('edit-own')` check

### Post Policy (`PostPolicy`)

**Post Creation:**
```php
// Users at Level 0 can only create up to POST_THRESHOLD (10) posts
if (!$user->canTrustTo('reply') && $user->post_count >= TrustLevels::POST_THRESHOLD) {
    return false; // Denied
}
```

**Post Editing:**
- Level 0: Can only edit own posts within 24 hours
- Level 1+: Can edit own posts via `canTrustTo('edit-own')` check

### Content Policy (`ContentPolicy`)

**Reporting:**
```php
// Users cannot report their own content
if ($user->id === $record->author_id) {
    return false;
}

// Base on trust level
return $user->canTrustTo('report');
```

---

## Administration

Admins can customize trust level settings at `/admin/settings/trust-levels` (superadmin access required).

### Configurable Settings

**Allowed Actions per Level:**
- Enable/disable each action for specific trust levels via checkboxes
- Changes take effect immediately

**Requirements per Level:**
- Modify numeric thresholds for each metric
- Examples:
  - Change Level 1 requirement from 5 to 10 threads
  - Adjust Level 2 daily visits requirement from 15 to 20
  - Increase Level 3 likes-received requirement

### Configuration Flow

1. Navigate to `/admin/settings/trust-levels`
2. For each trust level:
   - **Requirements Section**: Set numeric values for each metric
   - **Allowed Actions Section**: Check boxes for actions permitted at that level
3. Submit form
4. Settings validated and saved to database
5. New settings apply to next scheduled trust level assignment

### Settings Storage

All customizations are stored in the CodeIgniter settings database, **not** in the `Config/TrustLevels.php` file. This allows admins to modify settings without code deployments.

**Original Config File:** `app/Config/TrustLevels.php` - Contains defaults
**Runtime Storage:** CodeIgniter `settings` library - Contains current admin customizations

---

## Checking User Trust Level

To check a user's current trust level directly:

```php
// Get the numeric trust level (0-3)
$level = $user->trust_level;

// Get the human-readable name
$levelName = setting('TrustLevels.levels')[$user->trust_level];
// Example: "New Member", "Member", "Regular", "Leader"

// Check if user can perform a specific action
if ($user->canTrustTo('attach')) {
    // User's trust level allows file attachments
}
```

---

## Grace Period for Editing

Users at all trust levels have a **24-hour grace period** to edit their own posts and threads after creation, regardless of trust level. This is enforced in the policies:

```php
// Can edit own post if created within last 24 hours
if (time_since_creation < 24 hours) {
    return true; // Allow edit
}

// After 24 hours, check trust level
return $user->canTrustTo('edit-own');
```

This allows even new members to fix typos and mistakes in their early posts.
