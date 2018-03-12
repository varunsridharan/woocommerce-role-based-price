<?php

class WooCommerce_Role_Based_Price_Admin_Notices {

    protected static $instance;
    protected        $noticesArrayName;
    protected        $REQUESTID;
    protected        $notices;

    /**
     * Costructor (private since this is a singleton)
     */
    private function __construct() {
        self::$instance         = NULL;
        $this->noticesArrayName = WC_RBP_DB . 'AdminNotices';
        $this->REQUESTID        = WC_RBP_DB . 'MSG';
        $this->notices          = array();
        $this->loadNotices();
        $this->auto_remove_Notice();
        add_action('admin_notices', array( $this, 'displayNotices' ));
    }

    /**
     * Loads notices from DB
     */
    private function loadNotices() {
        $notices = get_option($this->noticesArrayName);
        if( is_array($notices) ) {
            $this->notices = $notices;
        }
    }

    /**
     * Removes Notice By Getting ID From GET / POST METHOD
     */
    public function auto_remove_Notice() {

        if( isset($_REQUEST[$this->REQUESTID]) ) {
            $nonce = $_REQUEST['_wpnonce'];
            $this->deleteNotice($_REQUEST[$this->REQUESTID]);
            if( wp_get_referer() ) {
                wp_safe_redirect(wp_get_referer());
            }
        }
    }

    /**
     * Deletes a notice
     *
     * @param int $notId The notice unique id
     */
    public function deleteNotice($notId) {
        foreach( $this->notices as $key => $notice ) {
            if( $notice->getId() === $notId ) {
                unset($this->notices[$key]);
                break;
            }
        }
        $this->storeNotices();
    }

    /**
     * Stores notices in DB
     */
    private function storeNotices() {
        update_option($this->noticesArrayName, $this->notices);
    }

    /**
     * Returns an instance of this class.
     *
     * @since 1.0.0
     * @return WP_Admin_Notices
     */
    public static function getInstance() {
        if( NULL == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Action hook to display notices.
     * Just echoes notices that should be displayed.
     */
    public function displayNotices() {
        foreach( $this->notices as $key => $notice ) {
            if( $this->isTimeToDisplay($notice) ) {
                echo $notice->getContentFormated($notice->getWrapper());
                $notice->incrementDisplayedTimes();
            }
            if( $notice->getTimes() > 0 ) {
                if( $notice->isTimeToDie() ) {
                    unset($this->notices[$key]);
                }
            }

        }
        $this->storeNotices();
    }

    /**
     * Checks if is time to display a notice
     *
     * @param WooCommerce_Role_Based_Price_Admin_Notice $notice
     *
     * @return bool
     */
    private function isTimeToDisplay(WooCommerce_Role_Based_Price_Admin_Notice $notice) {
        $screens = $notice->getScreen();
        if( ! empty($screens) ) {
            $curScreen = get_current_screen();
            if( ! is_array($screens) || ! in_array($curScreen->id, $screens) ) {
                return FALSE;
            }
        }

        $usersArray = $notice->getUsers();
        if( ! empty($usersArray) ) {
            $curUser = get_current_user_id();
            if( ! is_array($usersArray) || ! in_array($curUser, $usersArray) || $usersArray[$curUser] >= $notice->getTimes() ) {
                return FALSE;
            }


        } else if( $notice->getTimes() == 0 ) {
            return TRUE;
        } else if( $notice->getTimes() <= $notice->getDisplayedTimes() ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Adds a notice to be displayed
     *
     * @param erpAdminMessage $notice
     */
    public function addNotice(WooCommerce_Role_Based_Price_Admin_Notice $notice) {
        $this->notices[] = $notice;
        $this->storeNotices();
    }

}

/**
 * Abstract class of a notice
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
abstract class WooCommerce_Role_Based_Price_Admin_Notice {
    protected $content;
    protected $type;
    protected $screen;
    protected $id;
    protected $times            = 1;
    protected $users            = array();
    protected $displayedTimes   = 0;
    protected $displayedToUsers = array();
    protected $WithWraper       = TRUE;
    protected $is_dismissible   = TRUE;

    /**
     *
     * @param type  $content Coantent to be displayed
     * @param type  $times   How many times this notice will be displayed
     * @param array $screen  The admin screens this notice will be displayed into (empty for all screens)
     * @param array $users   Array of users this notice concernes (empty for all users)
     */
    public function __construct($content, $id = '', $times = 1, $screen = array(), $users = array(), $WithWraper = TRUE) {
        $this->content = $content;
        $this->screen  = $screen;
        if( empty($id) ) {
            $this->id = uniqid();
        } else {
            $this->id = $id;
        }
        $this->times      = $times;
        $this->users      = $users;
        $this->WithWraper = $WithWraper;
    }

    /**
     * Get the content of the notice
     *
     * @param bool $wrapInParTag If the content should be wrapped in a paragraph tag
     *
     * @return string Formated content
     */
    public function getContentFormated($wrapInParTag = TRUE) {
        $class = $this->type;
        $extrC = '';

        if( $this->is_dismissible ) {
            $class .= ' notice is-dismissible';
        }


        $before = '<div id="wc_pbp_notice_' . $this->id . '"  class="' . $class . '">';
        $before .= $wrapInParTag ? '<p>' : '';
        $after  = $wrapInParTag ? '</p>' : '';
        $after  .= '</div>';
        return $before . $this->getContent() . $after . $extrC;
    }

    /**
     * Get the notice string unformated
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     *
     * @param string $content
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Increment displayed times of the notice
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function incrementDisplayedTimes() {
        $this->displayedTimes++;

        if( array_key_exists(get_current_user_id(), $this->displayedToUsers) ) {
            $this->displayedToUsers[get_current_user_id()]++;
        } else {
            $this->displayedToUsers[get_current_user_id()] = 1;
        }
        return $this;
    }

    /**
     * Checks if the notice should me destroyed
     *
     * @return boolean True iff notice is deprecated
     */
    public function isTimeToDie() {
        if( empty($this->users) ) {
            return $this->displayedTimes >= $this->times;
        } else {
            $i = 0;
            foreach( $this->users as $key => $value ) {
                if( isset($this->displayedToUsers[$value]) && $this->displayedToUsers[$value] >= $this->times ) {
                    $i++;
                }
            }
            if( $i >= count($this->users) ) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Get the $WithWraper Value
     */
    public function getWrapper() {
        return $this->WithWraper;
    }

    /**
     * Set the $WithWraper Value
     *
     * @param boolean $screen
     */
    public function setWrapper($wrapper = TRUE) {
        $this->WithWraper = $wrapper;
        return $this;
    }

    /**
     * Get the current screen slug
     *
     * @return string Current screen slug
     */
    public function getScreen() {
        return $this->screen;
    }

    /**
     * Set the screens the notice will be displayed
     *
     * @param array $screen
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setScreen($screen) {
        $this->screen = $screen;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     *
     * @return int
     */
    public function getTimes() {
        return $this->times;
    }

    /**
     *
     * @param int $times
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setTimes($times) {
        $this->times = $times;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     *
     * @param array $users
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setUsers(Array $users) {
        $this->users = $users;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getDisplayedTimes() {
        return $this->displayedTimes;
    }

    /**
     *
     * @param int $displayedTimes
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setDisplayedTimes($displayedTimes) {
        $this->displayedTimes = $displayedTimes;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDisplayedToUsers() {
        return $this->displayedToUsers;
    }

    /**
     *
     * @param array $displayedToUsers
     *
     * @return \WooCommerce_Role_Based_Price_Admin_Notice
     */
    public function setDisplayedToUsers(Array $displayedToUsers) {
        $this->displayedToUsers = $displayedToUsers;
        return $this;
    }

}

/**
 * Type of notices
 */
class WooCommerce_Role_Based_Price_Admin_Error_Notice extends WooCommerce_Role_Based_Price_Admin_Notice {
    protected $type = 'error';
}

class WooCommerce_Role_Based_Price_Admin_Updated_Notice extends WooCommerce_Role_Based_Price_Admin_Notice {
    protected $type = 'updated';
}

class WooCommerce_Role_Based_Price_Admin_UpdateNag_Notice extends WooCommerce_Role_Based_Price_Admin_Notice {
    protected $type = 'update-nag';
}


/**
 * Hook action to admin init
 */
if( ! has_action('init', array( 'WooCommerce_Role_Based_Price_Admin_Notices', 'getInstance' )) ) {
    add_action('init', array( 'WooCommerce_Role_Based_Price_Admin_Notices', 'getInstance' ));
}