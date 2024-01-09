<?php

namespace App\Remote;

use App\Entity\PositionGeographique;
use App\Entity\Utilisateur;

interface CalculMoyenneInterface
{
    public function insertArraylist(PositionGeographique $pt): void;

    public function init();


    public function insertTab(array $liste, int $nbElement = 1000): ?array;

    public function triTab(array $tabATrier) : array;

    public function CalculvitesseMoyUsager(array $liste, int $duree=10): float;

    /**
     * @param array $tabTrie
     * @return array[Utilisateur]|null
     */
    public function listUsagers(array $tabTrie): ?array;

    public function initialisationVoies(array $tabUsagers): ?array;

    public function affectationVoie(array &$tab1, array &$tab2): void;

    public function moyenneVitesseParVoie(array $tabDeLaMemeVoie): array;

    public function tabUsagers($tabTrie, $listeUsers): array;

    public function tabUsager($tabTrieTrie, User $user): array;

    public function attribVoieTabUsager(array &$tabUsagers) : array;


    public function calculDistance(PositionGeographique $point1, PositionGeographique $point2): float;

    public function duree(PositionGeographique $point1, PositionGeographique $point2): int;

    public function grouperTabUsagersParVoie(array $tabsAffectesVoies): array;

    public function getVitesseVoie(): array;
    
    public function data() : array;

    public function data2() : array;

}