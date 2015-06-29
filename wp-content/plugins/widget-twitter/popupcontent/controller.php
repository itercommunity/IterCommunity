<?php
class PopupcontentController {
    ////////////////////////////////////////////////////////////////////////////////////////
    // Events                                                                             //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Constants                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Variables                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
	
	private $function_kind;
	
    ////////////////////////////////////////////////////////////////////////////////////////
    // Constructor & Destructor                                                           //
    ////////////////////////////////////////////////////////////////////////////////////////
	
    public function __construct() { 
	$this->function_kind = ((isset($_REQUEST['function_kind']) && esc_html(stripslashes($_REQUEST['function_kind']))!='') ? esc_html(stripslashes($_REQUEST['function_kind'])) : '');
	}
	
    ////////////////////////////////////////////////////////////////////////////////////////
    // Public Methods                                                                     //
    ////////////////////////////////////////////////////////////////////////////////////////
	
    public function execute() {
        $task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'display';
        if (method_exists($this, $task)) {
            $this->$task();
        } else {
            $this->display();
        }
    }
    
	public function display() {	
	    require_once WD_WDTI_DIR . '/popupcontent/model.php';
        $model = new PopupcontentModel();

        require_once WD_WDTI_DIR . '/popupcontent/view.php';
        $view = new PopupcontentView($this->function_kind, $model);
        $view->execute();
	}
}
?>