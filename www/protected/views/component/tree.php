<?php

$this->widget('CTreeView',array(
        'id'=>'treeView',
        'data'=>null,
        'animated'=>'fast', //quick animation
        'collapsed'=>"true",//remember must giving quote for boolean value in here
        'htmlOptions'=>array(
            'class'=>'treeview',//there are some classes that ready to use
        )
    )
);