<?php
/**
 * This view render the main menu bar, with configuration menu
 * @var $sitename
 * @var $activesurveyscount
 * @var $dataForConfigMenu
 */
?>

<!-- admin menu bar -->
<nav class="navbar">
  <div class="navbar-header">
      <button class="navbar-toggle hidden-md hidden-lg" type="button" data-toggle="collapse" data-target="#small-screens-menus">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>


        <a class="navbar-brand" href="<?php echo $this->createUrl("/admin/"); ?>">
            <?php echo $sitename; ?>
        </a>
    </div>


    <!-- Only on xs screens -->
    <div class="collapse navbar-collapse pull-left hidden-sm  hidden-md hidden-lg" id="small-screens-menus">
        <ul class="nav navbar-nav hidden-sm  hidden-md hidden-lg">

            <li><br/><br/></li>
            <!-- active surveys -->
            <?php if ($activesurveyscount > 0): ?>
                <li>
                    <a href="<?php echo $this->createUrl('admin/survey/sa/listsurveys/active/Y');?>">
                        <?php eT("Active surveys");?> <span class="badge badge-success"><?php echo $activesurveyscount ?></span>
                    </a>
                </li>
            <?php endif;?>

            <!-- List surveys -->
            <li>
                <a href="<?php echo $this->createUrl("admin/survey/sa/listsurveys"); ?>">
                    <?php eT("List surveys");?>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <a href="<?php echo $this->createUrl("admin/authentication/sa/logout"); ?>">
                    <?php eT("Logout");?>
                </a>
            </li>
        </ul>
    </div>

    <div class="collapse navbar-collapse js-navbar-collapse pull-right">
        <ul class="nav navbar-nav navbar-right">

            <?php // ============ Update LimeService Begin ======================================================= ?>
            <?php if (Permission::model()->hasGlobalPermission('superadmin')): ?>
                <li class="dropdown-split-left">
                    <marquee style="margin-top: 1em; max-width: 500px"><a href="<?php echo Yii::app()->createUrl("admin/update"); ?>"><strong class="text-warning"><?php eT("New update available:");?> </strong> <?php eT('Update now to Version 5.x.');?></a></marquee>
                </li>
            <?php endif; ?>
            <?php // ============ Update LimeService End======================================================= / ?>


            <!-- Tutorial menu -->
            <?php $this->renderPartial( "/admin/super/_tutorial_menu", []); ?>
            
            <!-- Configuration menu -->
            <?php $this->renderPartial( "/admin/super/_configuration_menu", $dataForConfigMenu ); ?>

            <!-- Surveys menus -->
            <li class="dropdown-split-left">
                <a style="" href="<?php echo $this->createUrl("admin/survey/sa/listsurveys"); ?>"><span class="icon-list" ></span>
                    <?php eT("Surveys");?>
                </a>
            </li>
            <li class="dropdown dropdown-split-right">
                <a style="padding-left: 5px;padding-right: 5px;" href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="sr-only">Toggle Dropdown</span>
                    <span style="margin-left: 0px;" class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                         <?php if (Permission::model()->hasGlobalPermission('surveys','create')): ?>
                         <!-- Create a new survey -->
                         <li>
                             <a href="<?php echo $this->createUrl("admin/survey/sa/newsurvey"); ?>">
                                 <?php eT("Create a new survey");?>
                             </a>
                         </li>

                         <!-- Import a survey -->
                         <li>
                           <a href="<?php echo $this->createUrl("admin/survey/sa/newsurvey/tab/import"); ?>">
                               <?php eT("Import a survey");?>
                           </a>
                         </li>

                         <!-- Import a survey -->
                         <li>
                           <a href="<?php echo $this->createUrl("admin/survey/sa/newsurvey/tab/copy"); ?>">
                               <?php eT("Copy a survey");?>
                           </a>
                         </li>

                         <li class="divider"></li>
                        <?php endif;?>
                         <!-- List surveys -->
                         <li>
                             <a href="<?php echo $this->createUrl("admin/survey/sa/listsurveys"); ?>">
                                 <?php eT("List surveys");?>
                             </a>
                         </li>

                       </ul>
                     </li>

            <!-- user menu -->
            <!-- active surveys -->
            <?php if ($activesurveyscount > 0): ?>
                <li>
                    <a href="<?php echo $this->createUrl('admin/survey/sa/listsurveys/active/Y');?>">
                        <?php eT("Active surveys");?> <span class="badge badge-success"> <?php echo $activesurveyscount ?> </span>
                    </a>
                </li>
            <?php endif;?>
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
                    "<li data-toggle='tooltip' data-placement='bottom' data-title='%s'><a href='https://www.limesurvey.org/pricing'><span class='fa fa-comments'></span>&nbsp;%d</a></li>",
                    gT('Response balance'),
                    $data['responses_avail']
                );
                printf(
                    "<li data-toggle='tooltip' data-placement='bottom' data-title='%s'><a href='%s'><span class='fa fa-database'></span>&nbsp;%d / %d</a></li>",
                    gT('Storage used / storage available (MB)'),
                    $sStorageUrl,
                    $data['storage_used'],
                    $data['upload_storage_size']
                );
            }
            //===============End LimeService Mod ?>

            <!-- Extra menus from plugins -->
            <?php // TODO: This views should be in same module as ExtraMenu and ExtraMenuItem classes (not plugin) ?>
            <?php foreach ($extraMenus as $menu): ?>
                <li class="dropdown">
                    <?php if ($menu->isDropDown()): ?>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                          <?php echo $menu->getLabel(); ?>
                          &nbsp;
                          <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach ($menu->getMenuItems() as $menuItem): ?>
                                <?php if ($menuItem->isDivider()): ?>
                                    <li class="divider"></li>
                                <?php elseif ($menuItem->isSmallText()): ?>
                                    <li class="dropdown-header"><?php echo $menuItem->getLabel();?></li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?php echo $menuItem->getHref(); ?>">
                                            <!-- Spit out icon if present -->
                                            <?php if ($menuItem->getIconClass() != ''): ?>
                                              <span class="<?php echo $menuItem->getIconClass(); ?>">&nbsp;</span>
                                            <?php endif; ?>
                                            <?php echo $menuItem->getLabel(); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?php echo $menu->getHref(); ?>"><?php echo $menu->getLabel(); ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" ><span class="icon-user" ></span> <?php echo Yii::app()->session['user'];?> <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="<?php echo $this->createUrl("/admin/user/sa/personalsettings"); ?>"><?php eT("My account");?></a>
                    </li>

                    <li class="divider"></li>

                    <!-- Logout -->
                    <li>
                        <a href="<?php echo $this->createUrl("admin/authentication/sa/logout"); ?>">
                            <?php eT("Logout");?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Admin notification system -->
            <?php echo $adminNotifications; ?>

        </ul>
    </div><!-- /.nav-collapse -->
    
    <!-- LimeService Modification Start -->
    <!-- Maintenance mode -->
    <?php $sMaintenanceMode = App()->getConfig('maintenancemode');
    if ($sMaintenanceMode == 'hard' || $sMaintenanceMode == 'soft'){ ?>
        <div class="collapse navbar-collapse js-navbar-collapse pull-right">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a class="text-warning" href="<?php echo $this->createUrl("admin/globalsettings"); ?>" title="<?php eT("Click here to change maintenance mode setting."); ?>" >
                        <span class="fa fa-warning" ></span>
                        <?php eT("Maintenance mode is active!");?>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>
    <!-- LimeService Modification End -->
</nav>
<script type="text/javascript">
    //show tooltips 
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
</script>
