<?php

namespace App\Models;

use App\Entities\Tag;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;
use ReflectionException;

class TagModel extends Model
{
    protected $table          = 'tags';
    protected $primaryKey     = 'id';
    protected $returnType     = Tag::class;
    protected $useSoftDeletes = false;
    protected $protectFields  = true;
    protected $allowedFields  = ['name'];
    protected $useTimestamps  = true;

    /**
     * Create many tags
     */
    public function createTags(array $tags, int $threadId): void
    {
        foreach ($tags as $tag) {
            $this->createTag($tag, $threadId);
        }
    }

    /**
     * Update many tags
     */
    public function updateTags(array $tags, int $threadId): void
    {
        $this->db->table('thread_tags')->where('thread_id', $threadId)->delete();

        $this->createTags($tags, $threadId);
        $this->cleanupTags();
    }

    /**
     * Create tag
     *
     * @throws ReflectionException
     */
    public function createTag(string $name, int $threadId): int
    {
        $data = [
            'tag_id'    => $this->findOrCreateId($name),
            'thread_id' => $threadId,
        ];

        $this->db->table('thread_tags')->insert($data);

        return $data['tag_id'];
    }

    /**
     * Cleanup tags which are no longer used.
     */
    public function cleanupTags(): bool|string
    {
        return $this->db
            ->table('tags')
            ->whereNotIn(
                'id',
                static fn (BaseBuilder $builder) => $builder->distinct()->select('tag_id')->from('thread_tags')
            )
            ->delete();
    }

    /**
     * Find or create tag and return tag ID.
     *
     * @throws ReflectionException
     */
    public function findOrCreateId(string $name): int
    {
        $tagId = $this->where('name', $name)->first()?->id;
        if (! $tagId) {
            $tagId = $this->insert(['name' => $name]);
        }

        return $tagId;
    }

    /**
     * Get tags for thread
     */
    public function getByThreadId(int $threadId): array
    {
        $tagIds = $this->db->table('thread_tags')
            ->select('tag_id')
            ->where('thread_id', $threadId)
            ->get()
            ->getResultArray();

        if (empty($tagIds)) {
            return [];
        }

        $tagIds = array_map('intval', array_column($tagIds, 'tag_id'));

        return $this->find($tagIds);
    }

    /**
     * Get tags for threads
     */
    public function getByThreadIds(array $threadIds): array
    {
        $tagIds = $this->db->table('thread_tags')
            ->select('tag_id, thread_id')
            ->whereIn('thread_id', $threadIds)
            ->get()
            ->getResultArray();

        if (empty($tagIds)) {
            return [];
        }

        $threadToTags = [];

        foreach ($tagIds as $tag) {
            $threadToTags[$tag['thread_id']][] = $tag['tag_id'];
        }
        $tagIds = array_map('intval', array_column($tagIds, 'tag_id'));

        $tags   = $this->find($tagIds);
        $tagIds = array_column($tags, 'id');
        $tags   = array_combine($tagIds, $tags);

        $results = [];

        foreach ($threadToTags as $threadId => $tagIds) {
            foreach ($tagIds as $tagId) {
                if (isset($tags[$tagId])) {
                    $results[$threadId][] = $tags[$tagId];
                }
            }
        }

        return $results;
    }
}
