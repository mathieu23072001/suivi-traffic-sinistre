<?php

namespace App\Service;

use App\Entity\Sinistre;
use App\Remote\MercureInterface;
use App\Repository\DeclarationRepository;
use App\Repository\SinistreRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureService implements MercureInterface
{

    private HubInterface $hub;
    private DeclarationRepository $declarationRepository;

    public function __construct(HubInterface $hub, DeclarationRepository $declarationRepository)
    {
        $this->hub = $hub;
        $this->declarationRepository = $declarationRepository;
    }

    public function getDeclarationToPublish() {
        $s = [];
        $sinistres = [];
        $sinistresBrut = $this->declarationRepository->findAllToPublish();
        foreach ($sinistresBrut as $item) {
            $s['latitude'] = $item->getLatitude();
            $s['longitude'] = $item->getLongitude();
            $s['id'] = $item->getId();
            $s['zoom'] = 150;
            $s['lieu'] = $item->getLibelleDeclaration();
            $s['image'] = $item->getFirstImage();
            $s['howManyLikes'] = $item->howManyLikes();
            $s['howManyDislikes'] = $item->howManyDislikes();
            $s['likes'] = $item->getLikes();
            $s['dislikes'] = $item->getDislikes();
            if($item instanceof Sinistre){
                $s['isSinistre'] = true;
            }else{
                $s['isSinistre'] = false;
            }

            $sinistres[] = $s;
        }
        return $sinistres;
    }
    public function publishUnSinistre()
    {
        $sinistres = $this->getDeclarationToPublish();
        $update = new Update(
            'sinistre',
            json_encode(['sinistre' => $sinistres])
        );

        $this->hub->publish($update);
    }
}