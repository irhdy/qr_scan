<?php

namespace App\Models;

use App\Models\Scan;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances_';
    protected $fillable = [
        'participant_id',
        'id_scan',
        'scan_at',
        'scan_by',
    ];

    public function participant(){
        return $this->belongsTo(Participant::class, "participant_id", "id");
    }
    public function scan()
    {
        return $this->belongsTo(Scan::class, "id_scan", "id");
    }
}
