
<li class="dropdown larger-dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <span class="fa fa-question-circle" ></span>
        <?php eT('Help');?>
        <span class="caret"></span>
    </a>
    <!-- LimeService mod start -->
    <ul class="dropdown-menu larger-dropdown" id="help-dropdown">
        <?php $this->renderPartial( "/admin/super/_tutorial_menu", []); ?>
        <li class="divider"></li>
        <li>
            <a href="https://help.limesurvey.org" target="_blank">
                <span class="fa fa-question-circle" ></span>
                <?php eT('Help Center');?>
                <i class="fa fa-external-link  pull-right"></i>
            </a>
        </li>
        <li>
            <a href="https://help.limesurvey.org/portal/kb/get-started/quick-start-guide" target="_blank">
                <span class="fa fa-play-circle" ></span>
                <?php eT('Quick start guide');?>
                <i class="fa fa-external-link  pull-right"></i>
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="https://help.limesurvey.org/portal/kb/faqs" target="_blank">
                <span class="fa fa-list" ></span>
                <?php eT('FAQ');?>
                <i class="fa fa-external-link  pull-right"></i>
            </a>
        </li>
        <?php 
            // LimeService Mod Start
            if(isset($issuperadmin) && intval($issuperadmin) == 1 ) { 
        ?>
        <li>
            <a href="https://www.limesurvey.org/pricing" target="_blank">
                <span class="fa fa-arrow-up" ></span>
                <?php eT('Upgrade now');?>
                <i class="fa fa-external-link  pull-right"></i>
            </a>
        </li>
        <?php
            }
            // LimeService Mod End
        ?>
    </ul>
    <!-- LimeService mod end -->
</li>