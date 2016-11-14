<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Coworker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class CoworkerAdminController
 *
 * @category Controller
 * @package  AppBundle\Controller\Admin
 * @author   David Romaní <david@flux.cat>
 */
class CoworkerAdminController extends BaseAdminController
{
    /**
     * Custom show action redirect to public frontend view
     *
     * @param int|string|null $id
     * @param Request         $request
     *
     * @return Response
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function showAction($id = null, Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Coworker $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        return $this->redirectToRoute(
            'front_coworker_detail',
            array(
                'slug'  => $object->getSlug(),
            )
        );
    }
}
