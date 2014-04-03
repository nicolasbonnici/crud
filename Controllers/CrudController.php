<?php
namespace bundles\crud\Controllers;

/**
 * Crud HomeController
 *
 * @todo Tout est réunis dans le Core pour faire un scaffold des forms
 *
 *       Check user's permissions with the \Libray\Core\ACL component layer first then the CRUD model methods and finaly
 *       the \Libray\Core\Entity component to ensure data integrity on write access and perform the database request
 *
 * @author Nicolas Bonnici
 *
 */
class CrudController extends \Library\Core\Auth
{

    /**
     * Crud model couch instance
     *
     * @var \bundles\crud\Models\Crud
     */
    protected $oCrudModel;

    /**
     * One dimensional array to restrict the CrudController entities scope (Before even check the ACL)
     *
     * @var array
     */
    protected $aEntitiesScope = array();

    protected $aActionsScope = array(
        'create',
        'read',
        'update',
        'delete',
        'list',
        'listByUser'
    );

    /**
     * Pre dispatch hook for CrudController's actions
     * Ask ACL component for the current request
     * Instantiate Crud model couch
     *
     * @throws CrudControllerException
     */
    public function __preDispatch()
    {
        $this->_view['iStatus'] = self::XHR_STATUS_ERROR;

        if (count($this->aEntitiesScope) > 0 && ! in_array($this->_params['entity'], $this->aEntitiesScope)) {
            throw new CrudControllerException('Entity restricted in CrudController scope', \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        }

        if ($this->oUser->getId() !== intval($this->_session['iduser'])) {
            throw new CrudControllerException('User session is invalid', \Library\Core\Crud::ERROR_USER_INVALID);
        }

        // Check user permissions on entity then entity itself
        if (isset($this->_params['entity']) && ($sEntityName = $this->_params['entity']) && strlen($sEntityName) > 0 && ($sAction = strtolower(substr($this->_controller, 0, (strlen($this->_controller) - strlen('controller'))))) && in_array($sAction, $this->aActionsScope) && ($sCheckMethodName = 'has' . $sAction . 'Access') && method_exists($this, $sCheckMethodName) && $this->{$sCheckMethodName}(strtolower($sEntityName))) {

            try {
                $iPrimaryKey = ((isset($this->_params['pk']) && intval($this->_params['pk']) > 0) ? intval($this->_params['pk']) : 0);

                // Check Entity instance with Crud model constructor
                $this->oCrudModel = new \bundles\crud\Models\Crud(ucfirst($sEntityName), $iPrimaryKey, $this->oUser);
            } catch (\bundles\crud\Models\CrudModelException $oException) {
                throw new CrudControllerException('Invalid Entity requested!', \Library\Core\Crud::ERROR_ENTITY_NOT_LOADABLE);
            }
        } else {
            throw new CrudControllerException('Error forbidden by ACL or unauthorized action: ' . $this->_controller, \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        }
    }

    /**
     * Post dispatch hook
     */
    public function __postDispatch()
    {}
}

class CrudControllerException extends \Exception
{
}