<?php

N2Loader::import('libraries.form.element.list');

class N2ElementWPCategories extends N2ElementList
{

    function fetchElement() {
        $args       = array(
            'type'         => 'post',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'category',
            'pad_counts'   => false

        );
        $categories = get_categories($args);
        $new        = array();
        foreach ($categories as $a) {
            $new[$a->category_parent][] = $a;
        }
        $list    = array();
        $options = $this->createTree($list, $new, 0);

        $this->_xml->addChild('option', 'All')
                   ->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars(' - ' . $option->treename))
                           ->addAttribute('value', $option->cat_ID);
            }
        }

        return parent::fetchElement();
    }

    function createTree(&$list, &$new, $parent, $cindent = '', $indent = '- ') {

        if (isset($new[$parent])) {
            for ($i = 0; $i < count($new[$parent]); $i++) {
                $new[$parent][$i]->treename = $cindent . $new[$parent][$i]->name;
                $list[]                     = $new[$parent][$i];
                $this->createTree($list, $new, $new[$parent][$i]->cat_ID, $cindent . $indent, $indent);
            }
        }
        return $list;
    }

}
