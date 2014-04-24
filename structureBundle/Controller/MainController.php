<?php

namespace Toilettage\structureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Toilettage\structureBundle\Entity\KeywordsRepo;

class MainController extends Controller
{
    public function accueilAction()
    {
        $params = $this->getParams('toilettage_accueil');
        return $this->render('ToilettagestructureBundle:Main:accueil.html.twig', $params);
    }
    public function contactAction()
    {
        $mail =  new MyMail();
        $form = $this->getForm($mail);
        $request = $this->get('request');
        $envoi = false;
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $m = $mail->getSwiftMailer();
                $this->get('mailer')->send($m); 
                $mail =  new MyMail();
                $form = $this->getForm($mail);
                $this->get('session')->set('envoie', true);
                return $this->redirect($this->generateUrl('literie_contact'));
            }
        }
        if ($this->get('session')->has('envoie'))
        {
            $this->get('session')->remove('envoie');
            $envoi = true;
        }
        $params = $this->getParams('literie_contact');
        $h = new HoraireRepo();
        $params['horaires'] = $h->getHoraires();
        $params['form'] = $form->createView();
        $params['envoie'] = $envoi;
        return $this->get('templating')->renderResponse('EuroLiteriestructureBundle:Main:contact.html.twig', $params);
    }
    private function getParams($page)
    {
        $dispatcher = $this->get('bundleDispatcher');
        $params['articles'] = $this->getDoctrine()->getRepository('yomaahBundle:Article')
                ->findByPage(array('pageUrl' => $page,'idSite' => $dispatcher->getIdSite()));
        $keywords = $this->getDoctrine()->getRepository('yomaahBundle:Page')->findKeywords($page, $dispatcher->getIdSite());
        $repoKeyword = new KeywordsRepo();
        $Gkeywords = $repoKeyword->getGeneralKeywords();
        if (!$keywords['keywords'])
        {
            $params['keywords'] = $Gkeywords;
        }else
        {
            $params['keywords'] = $Gkeywords.', '.$keywords['keywords']; 
        }
        $page = $this->getDoctrine()->getRepository('yomaahBundle:Page')->findPageByUrl(array('pageUrl' => $page, 'idSite' => $dispatcher->getIdSite()));
        $params['position'] = $page->getPosition();
        return $params;
    }
}
