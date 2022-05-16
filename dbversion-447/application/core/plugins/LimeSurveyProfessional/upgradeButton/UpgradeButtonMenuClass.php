<?php

/**
 * Extending the basic menu class with an icon in front of the label
 */

namespace LimeSurveyProfessional\upgradeButton;

class UpgradeButtonMenuClass extends \LimeSurvey\Menu\Menu
{
    public function getLabel()
    {
        return "<i class='" . $this->iconClass . "'></i>&nbsp;" . $this->label;
    }
}
