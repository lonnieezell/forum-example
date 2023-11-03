<?php

namespace App\Models;

use App\Entities\UserDelete;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class UserDeleteModel extends Model
{
    protected $table            = 'users_delete';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = UserDelete::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'scheduled_at'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get active.
     */
    public function active()
    {
        $this->where('scheduled_at >', Time::now());

        return $this;
    }

    /**
     * Ready to be deleted.
     */
    public function expired()
    {
        $this->where('scheduled_at <=', Time::now());

        return $this;
    }
}
