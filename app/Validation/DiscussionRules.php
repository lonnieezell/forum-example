<?php

namespace App\Validation;

class DiscussionRules
{
    public function thread_exists(string $value, ?string &$error = null): bool
    {
        $result = db_connect()
            ->table('threads')
            ->select('1')
            ->where('id', $value)
            ->limit(1)
            ->get()
            ->getRow();

        if ($result === null) {
            $error = 'This thread does not exist';

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
}
