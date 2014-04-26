<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('toilettage_accueil', new Route('/toilettage/accueil', array(
    '_controller' => 'ToilettagestructureBundle:Main:accueil',
)));

$collection->add('toilettage_toilettage', new Route('/toilettage/toilettage', array(
    '_controller' => 'ToilettagestructureBundle:Main:toilettage',
)));
$collection->add('toilettage_accessoire', new Route('/toilettage', array(
    '_controller' => 'ToilettagestructureBundle:Main:accueil',
)));
$collection->add('toilettage_photo', new Route('/toilettage/photos', array(
    '_controller' => 'ToilettagestructureBundle:Main:galerie',
)));
$collection->add('toilettage_contact', new Route('/toilettage/contact', array(
    '_controller' => 'ToilettagestructureBundle:Main:contact',
)));
return $collection;
