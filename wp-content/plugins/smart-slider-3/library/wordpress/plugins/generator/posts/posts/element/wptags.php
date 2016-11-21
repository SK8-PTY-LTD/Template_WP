<?php

N2Loader::import('libraries.form.element.list');

class N2ElementWPTags extends N2ElementList
{

    function fetchElement() {
        $terms = get_terms('post_tag');
        $this->_xml->addChild('option', 'All')
                   ->addAttribute('value', 0);
        if (count($terms)) {
            foreach ($terms AS $term) {
                $this->_xml->addChild('option', htmlspecialchars('- ' . $term->name))
                           ->addAttribute('value', $term->term_id);
            }
        }

        return parent::fetchElement();
    }

}
