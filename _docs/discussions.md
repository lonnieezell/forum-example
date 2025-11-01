# Discussion Architecture

Unlike many forums, all posts are grouped by Categories, not Forums.  The application uses a hierarchical structure: Categories → Threads → Posts.

## Core Models

### CategoryModel

Manages forum categories with support for nested hierarchies (parent/child relationships).

#### Key Features:

- Parent-child category relationships
- Activity tracking (thread_count, post_count, last_thread_id)
- Public/private visibility
- Soft deletes
- Active/inactive status

#### Key Methods:

- active() - Filter to active categories only
- public() - Filter to public categories only
- parents() - Get top-level categories
- children() - Get subcategories
- findAllNested() - Get nested category structure with last activity

### ThreadModel

Represents discussion threads created within categories.

#### Key Features:

- Belongs to a CategoryModel
- Thread author and optional editor tracking
- Soft deletes
- Tagging support
- Reaction counting
- Answer marking system
- View counting
- Can be closed or sticky

#### Search Types:

- recent-threads - Ordered by creation
- recent-posts - Ordered by latest reply
- unanswered - Threads without answers
- my-threads - Current user's threads

### PostModel

Individual replies and posts within threads.

#### Key Features:

- Can be replies to other posts (reply_to field)
- Author and optional editor tracking
- Soft deletes
- Visibility flag
- Reaction counting
- Can be marked as deleted or as thread answer
- Include user signature option
- Main posts vs. replies distinction

### ReactionModel

Manages user reactions (currently "likes") to threads and posts.

#### Key Features:

- Supports multiple reaction types (currently only REACTION_LIKE = 1)
- Tracks which user reacted to which resource
- Includes timestamps
