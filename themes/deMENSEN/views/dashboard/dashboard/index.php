<?php

// Dirty fix - redirect - works
//CController::redirect('/index.php?r=space/space&sguid=6c5523a2-0572-40cc-92f3-56b6eb6b9e9d');

// Load 'space' module
//Yii::app()->getModule('space');

// Get 1st (and only) space
$space=SpaceMembership::GetUserSpaces()[0];

?>

<div class="container space-layout-container">
    <div class="row">
        <div class="col-md-12">
            <?php $this->widget('application.modules_core.space.widgets.SpaceHeaderWidget', array('space' => $space)); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 layout-nav-container">
            <?php $this->widget('application.modules_core.space.widgets.SpaceMenuWidget', array('space' => $space)); ?>
            <?php $this->widget('application.modules_core.space.widgets.SpaceAdminMenuWidget', array('space' => $space)); ?>
            <br/>
        </div>

        <?php if (isset($this->hideSidebar) && $this->hideSidebar) : ?>
            <div class="col-md-10 layout-content-container">
                <?php echo $content; ?>
            </div>
        <?php else: ?>
            <div class="col-md-7 layout-content-container">
                <?php
                    $this->widget('application.modules_core.post.widgets.PostFormWidget', array(
    'contentContainer' => $space,
));

$this->widget('application.modules_core.wall.widgets.StreamWidget', array(
    'contentContainer' => $space,
    'streamAction' => '//space/space/stream',
    'messageStreamEmpty' => ($space->canWrite()) ?
            Yii::t('SpaceModule.views_space_index', '<b>This space is still empty!</b><br>Start by posting something here...') :
            Yii::t('SpaceModule.views_space_index', '<b>This space is still empty!</b>'),
    'messageStreamEmptyCss' => ($space->canWrite()) ?
            'placeholder-empty-stream' :
            '',
));
                ?>
            </div>
            <div class="col-md-3 layout-sidebar-container">
                <?php
                $this->widget('application.modules_core.space.widgets.SpaceSidebarWidget', array(
                    'widgets' => array(
                        array('application.modules_core.activity.widgets.ActivityStreamWidget', array('contentContainer' => $space, 'streamAction' => '//space/space/stream'), array('sortOrder' => 100)),
                        array('application.modules_core.space.widgets.SpaceMemberWidget', array('space' => $space), array('sortOrder' => 200)),
                    )
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php

/*$this->widget('application.modules_core.post.widgets.PostFormWidget', array(
    'contentContainer' => $space,
));

$this->widget('application.modules_core.wall.widgets.StreamWidget', array(
    'contentContainer' => $space,
    'streamAction' => '//space/space/stream',
    'messageStreamEmpty' => ($space->canWrite()) ?
            Yii::t('SpaceModule.views_space_index', '<b>This space is still empty!</b><br>Start by posting something here...') :
            Yii::t('SpaceModule.views_space_index', '<b>This space is still empty!</b>'),
    'messageStreamEmptyCss' => ($space->canWrite()) ?
            'placeholder-empty-stream' :
            '',
));*/

?>

