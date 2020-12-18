<?php

namespace Totaa\TotaaFileUpload;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Totaa\TotaaFileUpload\Skeleton\SkeletonClass
 */
class TotaaFileUploadFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'totaa-file-upload';
    }
}
