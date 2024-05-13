<?php
use MFR\TYPO3ReportsEndpoint\Reaction\ReportsEndpointReaction;

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'sys_reaction',
    'reaction_type',
    [
        'label' => ReportsEndpointReaction::getDescription(),
        'value' => ReportsEndpointReaction::getType(),
        'icon' => ReportsEndpointReaction::getIconIdentifier(),
    ]
);