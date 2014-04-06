<?php
namespace bundles\crud\Controllers;

/**
 * Crud DeleteController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *      
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *      
 * @author Nicolas Bonnici
 *        
 */
class DeleteController extends CrudController
{

    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * Delete an \app\Entities entity object
     */
    public function indexAction($sViewTpl = 'delete/delete.tpl')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }
            
            if ($this->oCrudModel->delete()) {
                $this->aView['bDeleteEntity'] = true;
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
            } else {
                $this->aView['bDeleteEntity'] = false; // delete exception
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['bDeleteEntity'] = false;
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        
        $this->render($sViewTpl, $this->aView['iStatus'], false, true);
    }
}