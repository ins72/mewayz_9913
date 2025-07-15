<?php
/**
 * Workspace Management Console Page
 * Professional workspace and team management interface
 */

use function Laravel\Folio\render;
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

$pageData = [
    'title' => 'Workspace Management - Mewayz',
    'description' => 'Advanced workspace and team collaboration tools',
    'keywords' => 'workspace, team, collaboration, management, organization'
];

render('livewire.components.console.workspace.page', $pageData);
?>