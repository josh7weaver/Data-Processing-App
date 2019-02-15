<?php namespace DataStaging\Traits;

use DataStaging\Mapper;

trait ModelGetter
{
    public function getModel( $shortModelName )
    {
        $shortModelName = strtolower($shortModelName);

        return Mapper::NAME_TO_MODEL_MAP()[ $shortModelName ];
    }
}