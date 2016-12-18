<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="columns">
    <div class="left-column">
        <div id="sidebar">
            <?php
            $this->beginWidget('zii.widgets.CPortlet', array(
                'title'=>'Operations',
            ));
            $this->widget('zii.widgets.CMenu', array(
                'items'=>$this->menu,
                'htmlOptions'=>array('class'=>'operations'),
            ));
            $this->endWidget();
            ?>
        </div><!-- sidebar -->
    </div>
    <div class="right-column">
        <div id="content">
            <?php echo $content; ?>
        </div><!-- content -->
    </div>
</div>
<?php $this->endContent(); ?>