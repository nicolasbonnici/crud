<?php
namespace bundles\crud\Controllers;

/**
 * Crud CreateController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *      
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *      
 * @author Nicolas Bonnici
 *        
 */
class CreateController extends CrudController
{

    public function __preDispatch()
    {
        parent::__preDispatch();
    }

    /**
     * Create an \app\Entities entity object then pass it to a given view
     *
     * @param string $sViewTpl            
     */
    public function indexAction($sViewTpl = 'create/create.tpl')
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            // Toutes les données du formulaire en JSON
            if (isset($this->aParams['parameters'])) {
                $aParameters = json_decode($this->aParams['parameters'], true);
            }
            
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }
            
            if ($this->oCrudModel->create($aParameters)) {
                $this->aView['bCreateEntity'] = true;
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->aView['bCreateEntity'] = false;
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['bCreateNewEntity'] = false;
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }
}