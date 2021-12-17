<?php

namespace LimeSurvey\Models\Services;

use SPSS\Exception;

/**
 * This class should have all sql-statements for getting data from the
 * database limeservice_statistics.
 *
 */
class LimeserviceStatistics
{

    /**
     * The connection to the database.
     *
     * @var \CDbConnection
     */
    private $dbConnection;

    /**
     * The installation id for this user.
     *
     * @var int
     */
    private $userInstallationId;

    /**
     * Initializes class params
     *
     * @param \CDbConnection $dbConnection
     * @param int $userInstallationId
     */
    public function __construct($dbConnection, $userInstallationId)
    {
        $this->dbConnection = $dbConnection;
        $this->userInstallationId = $userInstallationId;
    }


    /**
     * Updates the fields "modified" and "pageviews_admin" in table pageviews
     * for a specific subdomain+rootdomain(PK) .
     *
     * @param $subdomain
     * @param $domain
     * @return int|null number of effected rows or null in case of exception
     */
    public function updatePageViewsAdmin($subdomain, $domain){
        $sql = "Update pageviews set modified=now(), pageviews_admin=pageviews_admin+1 where subdomain='{$subdomain}' and rootdomain='{$domain}'";

        try {
            $affectedRows = $this->dbConnection->createCommand($sql)->execute();
        }catch(\CException $e){
            //todo: maybe log it for us, if anything went wrong here?
            return null;
        }

        return $affectedRows;
    }

    /**
     * Inserts an entry in tabel pageview for a specific subdomain+rootdomain(PK).
     * Fields are initialised:
     *      pageviews_client = 0
     *      pageviews_admin = 1
     *      lastaccess = now
     *      created = now
     *      modified = now
     *
     * @param $subdomain
     * @param $domain
     * @return int|null  nubmer of rows inserted (should only be one), or null in case of exception
     */
    public function insertPageViews($subdomain, $domain){
        $sql = "insert into pageviews (pageviews_admin, pageviews_client, subdomain, rootdomain, lastaccess, created, modified ) 
        values (1,0,'{$subdomain}','{$domain}',now(), now(), now())";

        try {
            $affectedRows = $this->dbConnection->createCommand($sql)->execute();
        }catch(\CException $e){
            //todo: maybe log it for us, if anything went wrong here?
            return null;
        }

        return $affectedRows;
    }
}