<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
	use HasFactory;

	protected $fillable = [
		'player_id',
		'expression',
		'correct_answer',
		'user_answer',
		'is_correct'
	];

	public function player(): BelongsTo
	{
		return $this->belongsTo(Player::class);
	}
}
