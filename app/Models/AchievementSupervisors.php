<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementSupervisors extends Model
{
    use HasFactory;
    public function insertSupervisor($achievementId, $supervisorId){
        $this->insert(
            [
                'achievement_id' => $achievementId,
                'supervisor_id' => $supervisorId
            ]
        );
    }
}
