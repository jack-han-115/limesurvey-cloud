<?php

/**
 * This view render the main menu bar, with configuration menu
 * @var $sitename
 * @var $activesurveyscount
 * @var $dataForConfigMenu
 * @var $dataForHelpMenu
 */
?>

<!-- admin menu bar -->
<nav class="navbar navbar-expand-md">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#small-screens-menus" aria-controls="small-screens-menus" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?php echo $this->createUrl("/admin/"); ?>">
            <img src="/assets/images/logo-icon-white.png" height="34" class="d-inline-block align-bottom" alt="">
            <?= $sitename ?>
        </a>
        <!-- Only on xs screens -->
        <div class="collapse navbar-collapse " id="small-screens-menus">
            <ul class="nav navbar-nav">
                <!-- active surveys -->
                <?php if ($activesurveyscount > 0) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $this->createUrl('surveyAdministration/listsurveys/active/Y'); ?>">
                            <?php eT("Active surveys"); ?> <span class="badge"><?php echo $activesurveyscount ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <!-- List surveys -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("surveyAdministration/listsurveys"); ?>">
                        <?php eT("List surveys"); ?>
                    </a>
                </li>
                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/authentication/sa/logout"); ?>">
                        <?php eT("Logout"); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse justify-content-center">
            <ul class="nav navbar-nav">
                <!-- Maintenance mode -->
                <?php $sMaintenanceMode = getGlobalSetting('maintenancemode');
                if ($sMaintenanceMode === 'hard' || $sMaintenanceMode === 'soft') { ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="<?php echo $this->createUrl("admin/globalsettings"); ?>" title="<?php eT("Click here to change maintenance mode setting."); ?>">
                            <span class="ri-alert-fil"></span>
                            <?php eT("Maintenance mode is active!"); ?>
                        </a>
                    </li>
                <?php } ?>

                <!-- Prepended extra menus from plugins -->
                <?php $this->renderPartial("application.libraries.MenuObjects.views._extraMenu", ['extraMenus' => $extraMenus, 'middleSection' => true, 'prependedMenu' => true]); ?>

                <!-- create survey -->
                <li class="nav-item">
                    <a href="<?php echo $this->createUrl("surveyAdministration/newSurvey"); ?>" class="nav-link">
                        <button type="button" class="btn btn-info btn-create" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="<?= gT('Create survey') ?>">
                            <i class="ri-add-line"></i>
                        </button>
                    </a>
                </li>
                <!-- Surveys menus -->

                <li
                    class="nav-item d-flex"><a
                        href="<?php echo $this->createUrl("surveyAdministration/listsurveys"); ?>"
                        class="nav-link ps-0"><?php eT("Surveys"); ?></a>
                    <?php if ($activesurveyscount > 0) : ?>
                        <a
                            class="nav-link ps-0 active-surveys"
                            href="<?php echo $this->createUrl('surveyAdministration/listsurveys/active/Y'); ?>"
                        ><span class="badge"> <?php echo $activesurveyscount ?> </span></a>
                    <?php endif; ?>
                </li>


                <!-- Help menu -->
	            <?php
	                // LimeService mod start
	                $this->renderPartial( "/admin/super/_help_menu", isset($dataForHelpMenu) ? $dataForHelpMenu : []);
	                // LimeService mod end
	            ?>

                <!-- Configuration menu -->
                <?php $this->renderPartial("/admin/super/_configuration_menu", $dataForConfigMenu); ?>


                <!-- Extra menus from plugins -->
                <?php $this->renderPartial("application.libraries.MenuObjects.views._extraMenu", ['extraMenus' => $extraMenus, 'middleSection' => true, 'prependedMenu' => false]); ?>
            </ul>
        </div>

        <div class="collapse navbar-collapse justify-content-end">
            <ul class="nav navbar-nav">
                <!-- Extra menus from plugins -->
                <?php $this->renderPartial("application.libraries.MenuObjects.views._extraMenu", ['extraMenus' => $extraMenus, 'middleSection' => false, 'prependedMenu' => true]); ?>
                <?php
                //===============Begin LimeService Mod
                $sDomain=$_SERVER['SERVER_NAME'];
                $sSubdomain=substr($sDomain,0,strpos($sDomain,'.'));
                $sDomain=substr($sDomain,strpos($sDomain,'.')+1);
                $iUserId = (int) substr(Yii::app()->db->username, 6);
                $sStorageUrl = $this->createUrl('/admin/globalsettings') . '#storage';

                $data = Yii::app()->dbstats->createCommand("SELECT i.upload_storage_size, b.storage_used, b.responses_avail FROM limeservice_system.balances b JOIN limeservice_system.installations i ON b.user_id = i.user_id WHERE i.user_id = ". $iUserId)->queryRow();
                if ($data) {
                    printf(
                        "<li class='nav-item d-flex' data-bs-toggle='tooltip' data-bs-placement='bottom' title='%s'><a href='https://www.limesurvey.org/pricing' class='nav-link'><span class='ri-question-answer-line'></span>&nbsp;%d</a></li>",
                        gT('Response balance'),
                        $data['responses_avail']
                    );
                    $storageString = sprintf("%.1f / %.1f",
                        $data['storage_used'],
                        $data['upload_storage_size']);
                    $aLangData = getLanguageData();
                    $radix = getRadixPointData($aLangData[Yii::app()->session['adminlang']]['radixpoint']);
                    $storageString = str_replace('.0', '', $storageString);
                    $storageString = str_replace('.', $radix['separator'], $storageString);

                    printf(
                        "<li class='nav-item d-flex' data-bs-toggle='tooltip' data-bs-placement='bottom' title='%s'><a href='%s' class='nav-link'><span class='ri-database-2-line'></span>&nbsp;$storageString</a></li>",
                        gT('Storage used / storage available (MB)'),
                        $sStorageUrl
                    );
                }
                //===============End LimeService Mod ?>
                <!-- Admin notification system -->
                <?php echo $adminNotifications; ?>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        <!-- <i class="ri-user-fill"></i> <?php echo Yii::app()->session['user']; ?> <span class="caret"></span></a> -->
                        <span class='rounded-circle text-center d-flex align-items-center justify-content-center me-1'>
                            <?= strtoupper(substr((string) Yii::app()->session['user'], 0, 1)) ?>
                        </span>
                        <?= Yii::app()->session['user']; ?>
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu dropdown-menu-end" role="menu">
                        <li>
                            <a class="dropdown-item" href="<?php echo $this->createUrl("/admin/user/sa/personalsettings"); ?>">
                                <?php eT("Account"); ?>
                            </a>
                        </li>

                        <li class="dropdown-divider"></li>

                        <!-- Logout -->
                        <li>
                            <a class="dropdown-item" href="<?php echo $this->createUrl("admin/authentication/sa/logout"); ?>">
                                <?php eT("Logout"); ?>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Extra menus from plugins -->
                <?php $this->renderPartial("application.libraries.MenuObjects.views._extraMenu", ['extraMenus' => $extraMenus, 'middleSection' => false, 'prependedMenu' => false]); ?>
            </ul>
        </div><!-- /.nav-collapse -->

    </div>
</nav>
<script type="text/javascript">
    //show tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    })

    $(document).ajaxComplete(function(handler) {
        window.LS.doToolTip();
    });
</script>
