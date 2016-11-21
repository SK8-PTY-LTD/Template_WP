<?php
N2Form::importElement('hidden');

class N2ElementItems extends N2ElementHidden
{


    function fetchElement() {
        $items = array();
        N2Plugin::callPlugin('ssitem', 'onNextendSliderItemList', array(&$items));
        ob_start();
        ?>
        <div id="smartslider-slide-toolbox-item" class="nextend-clearfix smartslider-slide-toolbox-view">
            <?php
            $itemModel = new N2SmartsliderItemModel();

            foreach ($items AS $type => $item) {
                echo N2Html::openTag("div", array(
                    "id"                => "smartslider-slide-toolbox-item-type-{$type}",
                    "style"             => "display:none",
                    "data-itemtemplate" => $item[1],
                    "data-itemvalues"   => $item[3]
                ));
                $itemModel->renderForm($type, $item);
                echo N2Html::closeTag("div");
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}