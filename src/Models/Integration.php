<?php

namespace Bddy\Integrations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Integration extends Model
{

	/**
	 * Casts
	 *
	 * @var string[]
	 */
	protected $casts = [
		'active' => 'boolean',
		'settings' => 'json',
		'authentication_required' => 'boolean',
	];

	/**
	 * Relation to integratable model
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function model()
	{
		return $this->morphTo('model');
	}
}
