<?php
namespace bundles\crud\Controllers;

/**
 * Crud PublicController
 *
 */
class PublicController extends \Library\Core\Controller
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
    protected $aEntitiesScope = array(
    	'Category',
    	'Tag',
    	'Post',
    	'FeedItem',
    	'feed'
    );

    /**
     * Allowed actions
     * @var unknown
     */
    protected $aActionsScope = array(
        'read',
        'list'
    );

    /**
     * Pre dispatch hook for CrudController's actions
     * Instantiate Crud model couch
     *
     * @throws PublicControllerException
     */
    public function __preDispatch()
    {
        $this->aView['iStatus'] = self::XHR_STATUS_ERROR;

        if (count($this->aEntitiesScope) > 0 && ! in_array($this->aParams['entity'], $this->aEntitiesScope)) {
            throw new PublicControllerException('Forbidden or not found entity!', \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        } elseif (
            ($sAction = strtolower(substr($this->sAction, 0, (strlen($this->sAction) - strlen('action'))))) &&
            ! in_array($sAction, $this->aActionsScope )
        ) {
            throw new PublicControllerException('Forbidden or not found action! (' . $$sAction . ') ', \Library\Core\Crud::ERROR_FORBIDDEN_BY_ACL);
        } else {

            try {
                $iPrimaryKey = ((isset($this->aParams['pk']) && intval($this->aParams['pk']) > 0) ? intval($this->aParams['pk']) : 0);
                $sEntityName = $this->aParams['entity'];
                // Check Entity instance with Crud model constructor
                $this->oCrudModel = new \bundles\crud\Models\Crud(ucfirst($sEntityName), $iPrimaryKey, $this->oUser);
            } catch (\bundles\crud\Models\CrudModelException $oException) {
                throw new PublicControllerException('Invalid Entity requested!', \Library\Core\Crud::ERROR_ENTITY_NOT_LOADABLE);
            }

        }


    }

    /**
     * Post dispatch hook
     */
    public function __postDispatch()
    {}

    /**
     * List \app\Entities entity then pass them to a given view
     *
     * @param string $sViewTpl
     */
    public function listAction($sViewTpl = 'list/list.tpl', $iOffset = 0, $iLoadStep = 25)
    {
        assert('$this->oCrudModel instanceof \bundles\crud\Models\Crud');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }

            if (isset($this->aParams['ioffset']) && $this->aParams['ioffset'] > 0) {
                $iOffset = (int) $this->aParams['ioffset'];
            }

            if (isset($this->aParams['iLoadStep']) && $this->aParams['iLoadStep'] > 0) {
                $iLoadStep = (int) $this->aParams['iLoadStep'];
            }

            if ($iOffset === 0) {
                $iLoadStep = 50;
            }
            $aLimit = array($iOffset, $iLoadStep);
            if ($this->oCrudModel->load('created', 'DESC', $aLimit)) {
                $this->aView['iStatus'] = self::XHR_STATUS_OK;
                $this->aView['oEntities'] = $this->oCrudModel->getEntities();
            }
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }

    /**
     * Read an entity then pass it to a given view
     *
     * @param string $sViewTpl
     */
    public function readAction($sViewTpl = 'read/read.tpl')
    {
        assert('($oEntity = $this->oCrudModel->getEntity()) && $oEntity->isLoaded()');
        try {
            if (isset($this->aParams['view']) && strlen(isset($this->aParams['view'])) > 0) {
                $sViewTpl = $this->aParams['view'];
            }

            $oEntity = $this->oCrudModel->getEntity();
            $oEntity->pk = $oEntity->getId();
            $this->aView['oEntity'] = $oEntity;

            $this->aView['aEntityFields'] = array();
            foreach ($oEntity->getAttributes() as $sAttribute) {
                if ($oEntity->getDataType($sAttribute) === 'string') {
                    $this->aView['aEntityFields'][$sAttribute] = $oEntity->{$sAttribute};
                }
            }
            $this->aView['iStatus'] = self::XHR_STATUS_OK;
        } catch (\bundles\crud\Models\CrudModelException $oException) {
            $this->aView['iStatus'] = self::XHR_STATUS_ERROR;
            $this->aView['error_message'] = $oException->getMessage();
            $this->aView['error_code'] = $oException->getCode();
        }
        $this->oView->render($this->aView, $sViewTpl, $this->aView['iStatus'], false, true);
    }

}

class PublicControllerException extends \Exception
{}
