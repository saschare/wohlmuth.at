<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Skin_Module_HTML_Meta_TeasertitleAsHeadline_Class extends Aitsu_Module_Abstract {

    protected function _init() {

        $output = '';
        if ($this->_get('HTML_Meta_TeasertitleAsHeadline', $output)) {
            return $output;
        }

        $headline = htmlentities(Aitsu_Content_Text :: get('Headline', 0), ENT_COMPAT, 'UTF-8');
        if (empty($headline)) {
            $headline = htmlentities(stripslashes(Aitsu_Core_Article :: factory()->teasertitle), ENT_COMPAT, 'UTF-8');
        }

        if ($this->_params->tag == 'no') {
            $output = $headline;
        } else {
            $output = '<' . $this->_params->tag . '>' . $headline . '</' . $this->_params->tag . '>';
        }

        if (Aitsu_Registry :: isEdit()) {
            $output = '<code class="aitsu_params" style="display:none;">' . $this->_context['params'] . '</code>' . $output;
        }

        $this->_save($output, 60 * 60 * 24 * 30);

        return $output;
    }

}