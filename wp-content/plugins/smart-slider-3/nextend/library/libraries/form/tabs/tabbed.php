<?php
N2Loader::import('libraries.form.tab');

class N2TabTabbed extends N2Tab {

    var $_tabs;

    function initTabs() {
        if (count($this->_tabs) == 0) {

            foreach ($this->_xml->params as $tab) {
                $test = N2XmlHelper::getAttribute($tab, 'test');
                if ($test == '' || $this->_form->makeTest($test)) {
                    $type = N2XmlHelper::getAttribute($tab, 'type');
                    if ($type == '') $type = 'default';
                    N2Loader::import('libraries.form.tabs.' . $type);
                    $class = 'N2Tab' . ucfirst($type);

                    $this->_tabs[N2XmlHelper::getAttribute($tab, 'name')] = new $class($this->_form, $tab);
                }
            }

            N2Pluggable::doAction('N2TabTabbed' . N2XmlHelper::getAttribute($this->_xml, 'name'), array(
                $this
            ));
        }
    }

    public function addTabXML($file) {
        $xml = simplexml_load_string(file_get_contents($file));

        foreach ($xml->params as $tab) {
            $test = N2XmlHelper::getAttribute($tab, 'test');
            if ($test == '' || $this->_form->makeTest($test)) {
                $type = N2XmlHelper::getAttribute($tab, 'type');
                if ($type == '') $type = 'default';
                N2Loader::import('libraries.form.tabs.' . $type);
                $class = 'N2Tab' . ucfirst($type);

                $a                                          = array();
                $a[N2XmlHelper::getAttribute($tab, 'name')] = new $class($this->_form, $tab);
                $this->_tabs                                = self::array_insert($this->_tabs, $a, 2);
                //$this->_tabs[N2XmlHelper::getAttribute($tab, 'name')] = new $class($this->_form, $tab);
            }
        }
    }

    private function array_insert($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }

    function render($control_name) {
        $this->initTabs();

        $id = 'n2-form-matrix-' . $this->_name;

        $active = intval(N2XmlHelper::getAttribute($this->_xml, 'active'));
        $active = $active > 0 ? $active - 1 : 0;

        $underlined = N2XmlHelper::getAttribute($this->_xml, 'underlined');

        $classes = N2XmlHelper::getAttribute($this->_xml, 'classes');
        ?>

        <div id="<?php echo $id; ?>" class="n2-form-tab n2-form-matrix">
            <div
                class="n2-h2 n2-content-box-title-bg n2-form-matrix-views <?php echo $classes; ?>">
                <?php
                $i     = 0;
                $class = ($underlined ? 'n2-underline' : '') . ' n2-h4 n2-uc n2-has-underline n2-form-matrix-menu';


                foreach ($this->_tabs AS $tabName => $tab) {


                    echo N2Html::tag("div", array(
                        "class" => $class . ($i == $active ? ' n2-active' : '') . ' n2-fm-' . $tabName
                    ), N2Html::tag("span", array("class" => "n2-underline"), n2_(N2XmlHelper::getAttribute($tab->_xml, 'label'))));

                    $i++;
                }
                ?>
            </div>

            <div class="n2-tabs">
                <?php
                $i = 0;
                foreach ($this->_tabs AS $tabName => $tab) {
                    echo N2Html::openTag('div', array(
                        'class' => 'n2-form-matrix-pane' . ($i == $active ? ' n2-active' : '') . ' n2-fm-' . $tabName
                    ));
                    $tab->render($control_name);
                    echo N2Html::closeTag('div');
                    $i++;
                }
                ?>
            </div>
        </div>

        <?php
        N2JS::addInline('
            (function(){
                var matrix = $("#' . $id . '"),
                    views = matrix.find("> .n2-form-matrix-views > div"),
                    panes = matrix.find("> .n2-tabs > div");
                views.on("click", function(){
                    views.removeClass("n2-active");
                    panes.removeClass("n2-active");
                    var i = views.index(this);
                    views.eq(i).addClass("n2-active");
                    panes.eq(i).addClass("n2-active");
                });
            })();
        ');
        ?>
        <?php
    }

}
