
<li class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" id="helpDropdown" aria-expanded="false" role="button">
        <!-- <i class="ri-question-fill"></i> -->
        <?php eT('Help');?>
    </a>
    <!-- LimeService mod start -->
    <ul class="dropdown-menu larger-dropdown" aria-labelledby="helpDropdown">
        <?php $this->renderPartial( "/admin/super/_tutorial_menu", []); ?>
        <li class="dropdown-divider"></li>
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
        <li class="dropdown-divider"></li>
        <li>
            <a href="https://help.limesurvey.org/portal/kb/faqs" target="_blank">
                <span class="ri-bug-fill" ></span>
                <?php eT('FAQ');?>
                <i class="fa fa-external-link  pull-right"></i>
            </a>
        </li>
        <?php 
            // LimeService Mod Start
            if(isset($issuperadmin) && intval($issuperadmin) == 1 ) {
        ?>
        <li>
            <a href="https://limesurvey.org/" target="_blank" class="dropdown-item">
                <span class="ri-star-fill" ></span>
                <?php eT('LimeSurvey Homepage');?>
                <i class=" ri-external-link-fill  float-end"></i>
            </a>
        </li>
        <?php
            }
            // LimeService Mod End
        ?>
    </ul>
    <!-- LimeService mod end -->
</li>
