<?php namespace DataStaging\Traits;


trait EloquentScopes{

	/**
	 * This scope allows querys to scope into only the results that are enabled
	 * 	i.e. School::enabled()->get(); // gets all enabled
	 * 		 School::enabled()->where('name', 'anderson')->first();  // get first enabled anderson entry
	 */
	public function scopeEnabled($query)
	{
		return $query->where('enabled', true);
	}
}