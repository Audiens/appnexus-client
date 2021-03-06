<?php

namespace Audiens\AppnexusClient\exceptions;

use Audiens\AppnexusClient\repository\RepositoryResponse;

/**
 * Class ReportException
 */
class ReportException extends \Exception
{

    /**
     * @param RepositoryResponse $repositoryResponse
     *
     * @return self
     */
    public static function failed(RepositoryResponse $repositoryResponse)
    {
        return new self('Failed call: '.$repositoryResponse->getError()->getError());
    }

    /**+
     * @param $missingIndex
     *
     * @return self
     */
    public static function missingIndex($missingIndex)
    {
        return new self('Invalid reposnse missing: '.$missingIndex);
    }

    /**
     * @return self
     */
    public static function validation($message)
    {
        return new self($message);
    }
}
