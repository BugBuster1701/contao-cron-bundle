<?php

namespace BugBuster\CronBundle\Services;

interface JobTypesInterface
{
    /**
     * Job Constants
     * @var integer
     */
    const JOB_TYPE_FILE = 1;
    const JOB_TYPE_ROUTE = 2;
    const JOB_TYPE_URL = 3;
    const JOB_TYPE_COMMAND = 4;
}