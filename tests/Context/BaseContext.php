<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:48
 */

namespace Tests\Context;

use Doctrine\ODM\MongoDB\DocumentManager;

abstract class BaseContext
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * BaseContext constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }
}