<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 *
 * @package AAM
 * @author Vasyl Martyniuk <support@wpaam.com>
 * @copyright Copyright C 2013 Vasyl Martyniuk
 * @license GNU General Public License {@link http://www.gnu.org/licenses/}
 */
class aam_Control_Object_Term extends aam_Control_Object {

    /**
     *
     */
    const UID = 'term';

    /**
     *
     */
    const ACTION_BROWSE = 'browse';

    /**
     *
     */
    const ACTION_EXCLUDE = 'exclude';

    /**
     *
     */
    const ACTION_EDIT = 'edit';

    /**
     *
     */
    const ACTION_LIST = 'list';

    /**
     *
     * @var type
     */
    private $_term = null;

    /**
     *
     * @var type
     */
    private $_option = array();

    /**
     * @inheritdoc
     */
    public function __sleep(){
        return array('_term', '_option');
    }

    /**
     * @inheritdoc
     */
    public function save($params = null) {
        if (is_array($params)) {
            $this->getSubject()->updateOption(
                    $params, self::UID, $this->getTerm()->term_id
            );
            //set flag that this subject has custom settings
            $this->getSubject()->setFlag(aam_Control_Subject::FLAG_MODIFIED);
        }
    }

    /**
     * Get Object Unique ID
     * 
     * @return string
     * 
     * @access public
     */
    public function getUID() {
        return self::UID;
    }

    /**
     *
     * @param type $object_id
     */
    public function init($object_id) {
        if ($object_id) {
            //initialize term first
            $term = get_term($object_id, $this->getTaxonomy($object_id));
            if ($term && !is_wp_error($term)) {
                $this->setTerm($term);
                $access = $this->getSubject()->readOption(
                        self::UID, $this->getTerm()->term_id
                );
                $inherit = aam_Core_ConfigPress::getParam(
                        'aam.term.inherit', 'true'
                );
                if (empty($access) && ($inherit == 'true')) {
                    //try to get any parent restriction
                    $access = $this->inheritAccess($this->getTerm()->parent);
                }
                
                //even if parent category is empty, try to read the parent subject
                if (empty($access)){
                    $access = $this->getSubject()->readParentSubject(
                            self::UID, $this->getTerm()->term_id
                    );
                }

                $this->setOption(
                        apply_filters('aam_term_access_option', $access, $this)
                );
            }
        }
    }

    /**
     *
     * @return type
     */
    public function delete() {
        return $this->getSubject()->deleteOption(
                        self::UID, $this->getTerm()->term_id
        );
    }

    /**
     *
     * @param type $term_id
     * @return array
     */
    private function inheritAccess($term_id) {
        $term = new aam_Control_Object_Term($this->getSubject(), $term_id);
        if ($term->getTerm()) {
            $access = $term->getOption();
            if (empty($access) && $term->getTerm()->parent) {
                $this->inheritAccess($term->getTerm()->parent);
            } elseif (!empty($access)) {
                $access['inherited'] = true;
            }
        } else {
            $access = array();
        }

        return $access;
    }

    /**
     *
     * @global type $wpdb
     * @param type $object_id
     * @return type
     */
    private function getTaxonomy($object_id) {
        global $wpdb;

        $query = "SELECT taxonomy FROM {$wpdb->term_taxonomy} ";
        $query .= "WHERE term_id = {$object_id}";

        return $wpdb->get_var($query);
    }

    /**
     *
     * @param type $term
     */
    public function setTerm($term) {
        $this->_term = $term;
    }

    /**
     *
     * @return type
     */
    public function getTerm() {
        return $this->_term;
    }

    /**
     *
     * @param type $option
     */
    public function setOption($option) {
        $this->_option = (is_array($option) ? $option : array());
    }

    /**
     *
     * @return type
     */
    public function getOption() {
        return $this->_option;
    }

    /**
     * @inheritdoc
     */
    public function cacheObject(){
        return true;
    }

    /**
     *
     * @param type $area
     * @param type $action
     * @return type
     */
    public function has($area, $action) {
        $response = false;
        if (isset($this->_option['term'][$area][$action])) {
            $response = (intval($this->_option['term'][$area][$action]) ? true : false);
        }

        return $response;
    }

}