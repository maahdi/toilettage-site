<?php

namespace Toilettage\structureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Toilettage\structureBundle\Entity\KeywordsRepo;
use Yomaah\ajaxBundle\Controller\AjaxController;
use Yomaah\structureBundle\Entity\MyMail;

class MainController extends Controller
{
    public function accueilAction()
    {
        $params = $this->getParams('toilettage_accueil');
        return $this->render('ToilettagestructureBundle:Main:accueil.html.twig', $params);
    }

    public function toilettageAction()
    {
        $params = $this->getParams('toilettage_toilettage');
        return $this->render('ToilettagestructureBundle:Main:toilettage.html.twig', $params);
    }

    public function galerieAction()
    {
        $params = $this->getParams('toilettage_galerie');
        $params['images'] = AjaxController::imageSearch('galerie/active', 'Toilettage/structureBundle');
        return $this->render('ToilettagestructureBundle:Main:galerie.html.twig', $params);
    }

    private function getForm($mail)
    {
        $formBuilder = $this->createFormBuilder($mail);
        $formBuilder->add('Objet','text',array('attr' => array ('placeholder' => 'L\'objet de votre message'),'label' => 'Sujet de votre message :'))
                    ->add('From','email',array('attr' => array ('placeholder' => 'Votre adresse email'),'label' => 'Votre email :'))
                    ->add('Message','textarea',array('attr' => array ('placeholder' => 'Votre message ...'),'label' => 'Votre message :'));
        return $formBuilder->getForm();
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
                return $this->redirect($this->generateUrl('toilettage_contact'));
            }
        }
        if ($this->get('session')->has('envoie'))
        {
            $this->get('session')->remove('envoie');
            $envoi = true;
        }
        $params = $this->getParams('toilettage_contact');
        $tmp = array();
        foreach ($params['articles'] as $article)
        {
            if ($article->getTagName() == 'adresse_courrier' || $article->getTagName() == 'adresse_phone')
            {
                $tmp[] = $article;
            }
        }
        $params['adresse'] = $tmp;
        //$h = new HoraireRepo();
        //$params['horaires'] = $h->getHoraires();
        $params['form'] = $form->createView();
        $params['envoie'] = $envoi;
        return $this->get('templating')->renderResponse('ToilettagestructureBundle:Main:contact.html.twig', $params);
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
