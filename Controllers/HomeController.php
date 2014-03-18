<?php
namespace modules\crud\Controllers;

use Library\Core\ControllerException;
use Library\Core\CoreEntityException;

/**
 * Crud HomeController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *
 * Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 * the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *
 * @author Nicolas Bonnici
 *
 */
class HomeController extends \Library\Core\Auth {

    /**
     * Crud model couch instance
     * @var \modules\crud\Models\Crud
     */
    private $oCrudModel;

    /**
     * One dimensional array to restrict the CrudController entities scope (Before even check the ACL)
     * @var array
     */
    private $aEntitiesScope = array();

    /**
     * Pre dispatch hook for CrudController's actions
     *
     * @throws ControllerException
     */
    public function __preDispatch()
    {
        $this->_view['iStatus'] = self::XHR_STATUS_ERROR;

        if (
            count($this->aEntitiesScope) > 0 &&
            !in_array($this->_params['entity'], $this->aEntitiesScope)
        ) {
            throw new ControllerException('Entity restricted in CrudController scope', \modules\crud\Models\Crud::ERROR_FORBIDDEN_BY_ACL);
        }

        if ($this->oUser->getId() !== intval($this->_session['iduser'])) {
            throw new ControllerException('User session is invalid', \modules\crud\Models\Crud::ERROR_USER_INVALID);
        }

        // Check user permissions on entity then entity itself
        if (
            isset($this->_params['entity']) &&
            ($sEntityName = $this->_params['entity']) &&
            strlen($sEntityName) > 0 &&
            ($sAction = substr($this->_action, 0, (strlen($this->_action) - strlen('action')))) &&
            in_array($sAction, array('create', 'read', 'update', 'delete', 'list', 'listByUser')) &&
            ($sCheckMethodName = 'has' . $sAction . 'Access') &&
            method_exists($this, $sCheckMethodName) &&
            $this->{$sCheckMethodName}(strtolower($sEntityName))
        ) {

            try {
                $iPrimaryKey = ((isset($this->_params['pk']) && intval($this->_params['pk']) > 0) ? intval($this->_params['pk']) : 0);

                // Check Entity instance with Crud model constructor
                $this->oCrudModel = new \modules\crud\Models\Crud(ucfirst($sEntityName), $iPrimaryKey, $this->oUser);
            } catch (\modules\crud\Models\CrudModelException $oException) {
                throw new ControllerException('Invalid Entity requested!', \modules\crud\Models\Crud::ERROR_ENTITY_NOT_LOADABLE);
            }

        } else {
            throw new ControllerException('Error forbidden by ACL or unauthorized action: ' . $this->_action, \modules\crud\Models\Crud::ERROR_FORBIDDEN_BY_ACL);
        }
    }

    /**
     * Post dispatch hook
     */
    public function __postDispatch() {}

    /**
     * Create an \app\Entities entity object then pass it to a given view
     *
     * @param string $sViewTpl
     */
    public function createAction($sViewTpl = 'crud/create.tpl')
    {
        try {
            // Toutes les données du formulaire en JSON
            if (isset($this->_params['parameters'])) {
                $aParameters = json_decode($this->_params['parameters'], true);
            }

            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            if ($this->oCrudModel->create($aParameters)) {
                $this->_view['bCreateEntity'] = true;
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->_view['bCreateEntity'] = false;
            }

        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['bCreateNewEntity'] = false;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }

        $this->render($sViewTpl, $this->_view['iStatus']);
    }

    /**
     * Read an entity then pass it to a given view
     *
     * @param string $sViewTpl
     */
    public function readAction($sViewTpl = 'crud/read.tpl')
    {
        assert('($oEntity = $this->oCrudModel->getEntity()) && $oEntity->isLoaded()');
        try {
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            $this->_view['oEntity'] = $this->oCrudModel->read();
            $this->_view['iStatus'] = self::XHR_STATUS_OK;

        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['iStatus'] = self::XHR_STATUS_ERROR;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        $this->render($sViewTpl, $this->_view['iStatus']);
    }

    /**
     * Update an \app\Entities entity object then pass them to a given view
     *
     * @param unknown $sViewTpl
     */
    public function updateAction($sViewTpl = 'crud/update.tpl')
    {
        try {
            // Toutes les données du formulaire en JSON
            if (isset($this->_params['parameters'])) {
                $aParameters = json_decode($this->_params['parameters'], true);
            }

            // la vue
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            if (($this->_view['bUpdateEntity'] = $this->oCrudModel->update($aParameters)) === true) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntity'] = $this->oCrudModel->getEntity();
            } else {
                $this->_view['bUpdateEntity'] = false; // clean exception
            }
        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['bUpdateEntity'] = false;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }

        $this->render($sViewTpl, $this->_view['iStatus']);
    }


    /**
     * Delete an \app\Entities entity object
     */
    public function deleteAction($sViewTpl = 'crud/delete.tpl')
    {
        try {
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            if ($this->oCrudModel->delete()) {
                $this->_view['bDeleteEntity'] = true;
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
            } else {
                $this->_view['bDeleteEntity'] = false; // delete exception
            }
        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['bDeleteEntity'] = false;
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }

        $this->render($sViewTpl, $this->_view['iStatus']);
    }

    /**
     * List \app\Entities entity then pass them to a given view
     *
     * @param string $sViewTpl
     */
    public function listAction($sViewTpl = 'crud/list.tpl')
    {
        try {
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            if ($this->oCrudModel->loadEntities()) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntities'] = $this->oCrudModel->getEntities();
            }

        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }
        $this->render($sViewTpl, $this->_view['iStatus']);
    }

    /**
     * Load latest entities restricted to the curently instantiate \app\Entities\User session scope
     *
     * @param string $sViewTpl
     */
    public function listByUserAction($sViewTpl = 'crud/list.tpl')
    {
        assert('$this->oCrudModel instanceof \modules\crud\Models\Crud');
        try {
            if (isset($this->_params['view']) && strlen(isset($this->_params['view'])) > 0) {
                $sViewTpl = $this->_params['view'];
            }

            if ($this->oCrudModel->loadUserEntities()) {
                $this->_view['iStatus'] = self::XHR_STATUS_OK;
                $this->_view['oEntities'] = $this->oCrudModel->getEntities();
                $this->_view['aEntityAttributes'] = $this->oCrudModel->getEntityAttributes();
            }

        } catch (\modules\crud\Models\CrudModelException $oException) {
            $this->_view['error_message'] = $oException->getMessage();
            $this->_view['error_code'] = $oException->getCode();
        }

        $this->render($sViewTpl, $this->_view['iStatus']);
    }

}