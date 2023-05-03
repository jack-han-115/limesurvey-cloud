
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
            <a class="dropdown-item" href="https://help.limesurvey.org" target="_blank">
                <span class="ri-question-fill" ></span>
                <?php eT('Help Center');?>
                <i class="ri-external-link-fill  float-end"></i>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="https://help.limesurvey.org/portal/kb/get-started/quick-start-guide" target="_blank">
                <span class="ri-play-circle-fill" ></span>
                <?php eT('Quick start guide');?>
                <i class="ri-external-link-fill  float-end"></i>
            </a>
        </li>
        <li class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="https://help.limesurvey.org/portal/kb/faqs" target="_blank">
                <span class="ri-bug-fill" ></span>
                <?php eT('FAQ');?>
                <i class="ri-external-link-fill  float-end"></i>
            </a>
        </li>
        <?php 
            // LimeService Mod Start
            if(isset($issuperadmin) && intval($issuperadmin) == 1 ) {
        ?>
        <li>
            <a class="dropdown-item" href="https://limesurvey.org/" target="_blank" class="dropdown-item">
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
