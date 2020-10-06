<?php

namespace Bddy\Integrations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Integration extends Model
{
	protected $guarded = ['uuid'];

	protected $casts = [
		'active' => 'boolean',
		'settings' => 'json',
		'authentication_required' => 'boolean',
	];

	/**
	 * Generate uuid on creation.
	 */
	public static function boot()
	{
		parent::boot();
		self::creating(function (self $model) {
			$model->uuid = (string) Str::uuid();
		});
	}

	/**
	 * @return mixed|string
	 */
	public function getRouteKey()
	{
		return $this->uuid;
	}

	/**
	 * Relation to integratable model
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function model()
	{
		return $this->morphTo('model');
	}
}
