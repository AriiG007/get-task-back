<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Stage extends Model
{
    use HasUuids;

    protected $table = 'stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function nextStage()
    {
        return Stage::where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();
    }

    public function previousStage()
    {
        return Stage::where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }
}
