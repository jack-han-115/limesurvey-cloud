<?php

namespace LimeSurvey\Helpers\Update;

use LsDefaultDataSets;
use SurveymenuEntries;

class Update_490 extends DatabaseUpdateBase
{
    /**
     * This table is needed to collect failed emails.
     */
    public function up()
    {
        $this->db->createCommand()->update("{{surveymenu_entries}}", ['title' => 'Privacy policy settings', 'menu_title' => 'Privacy policy', 'menu_description' => 'Edit privacy policy settings'], "name='datasecurity'");
        // LimeService Mod start
        $this->db->createCommand()->update("{{boxes}}", ['url' => 'userManagement/index', 'title' => 'User management', 'desc' => 'User management'], "url='admin/user'");
        // LimeService Mod end
    }
}
