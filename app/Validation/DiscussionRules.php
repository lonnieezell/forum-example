<?php

namespace App\Validation;

use CodeIgniter\I18n\Time;
use Exception;

class DiscussionRules
{
    public function thread_exists(string $value, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('threads')
            ->select('category_id')
            ->where('id', $value)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This thread does not exist';

            return false;
        }

        // Check if you're allowed to see the thread based on the category permissions
        if (! service('policy')->checkCategoryPermissions($result->category_id)) {
            $error = 'You are not allowed to access this thread';

            return false;
        }

        return true;
    }

    public function post_exists(string $value, string $params, array $data, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('posts')
            ->select('1')
            ->where('id', $value)
            ->where('thread_id', (int) $data['thread_id'])
            ->where('reply_to', null)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = "The thread or post you're trying to reply does not exist";

            return false;
        }

        return true;
    }

    public function thread_report(string $value, string $params, array $data, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('threads')
            ->select('1')
            ->where('id', $value)
            ->where('author_id !=', $params)
            ->where('deleted_at', null)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This thread does not exist or cannot be reported';

            return false;
        }

        return true;
    }

    public function post_report(string $value, string $params, array $data, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('posts')
            ->select('1')
            ->where('id', $value)
            ->where('author_id !=', $params)
            ->where('deleted_at', null)
            ->where('marked_as_deleted', null)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This post does not exist or cannot be reported';

            return false;
        }

        return true;
    }

    public function valid_post_thread(string $value, string $params, array $data, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('posts')
            ->select('1')
            ->where('id', $value)
            ->where('thread_id', $params)
            ->where('deleted_at', null)
            ->where('marked_as_deleted', null)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This post does not belong in this thread';

            return false;
        }

        return true;
    }

    public function valid_tags(string $value, string $params, array $data, ?string &$error = null): bool
    {
        $value = explode(',', $value);

        if (count($value) > $params) {
            $error = 'The number of tags is too high.';

            return false;
        }

        $pattern = '/^[a-z0-9-]{0,20}$/';

        foreach ($value as $string) {
            if (! preg_match($pattern, $string)) {
                $error = 'Tag "' . $string . '" is not valid.';

                return false;
            }
        }

        return true;
    }

    public function category_exists(string $value, string $params, array $data, ?string &$error = null)
    {
        $result = db_connect()
            ->table('categories')
            ->select('1')
            ->where('slug', $value)
            ->when(
                $params === 'child',
                static fn ($query) => $query->where('parent_id !=', null)
            )
            ->when(
                $params === 'parent',
                static fn ($query) => $query->where('parent_id', null)
            )
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This category does not exist';

            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function unique_report(string $value, string $params, array $data, ?string &$error = null)
    {
        [$type, $id] = explode(',', $params);

        $result = db_connect()
            ->table('moderation_reports')
            ->select('created_at')
            ->where('resource_id', $value)
            ->where('resource_type', $type)
            ->where('author_id', $id)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result !== null) {
            $time  = Time::createFromFormat('Y-m-d H:i:s', $result->created_at)->humanize();
            $error = "You already reported this {$type} {$time}";

            return false;
        }

        return true;
    }

    public function date_range_when_field(?string $value, string $params, array $data, ?string &$error = null)
    {
        [$fieldSearch, $fieldValue] = explode(',', $params);

        helper('array');

        // Don't bother when fieldSearch has incorrect fieldValue
        if (dot_array_search($fieldSearch, $data) !== $fieldValue) {
            return true;
        }

        // Check the date range format
        $pattern = '/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/';
        if (! preg_match($pattern, (string) $value)) {
            $error = 'Incorrect date range format';

            return false;
        }

        return true;
    }
}
