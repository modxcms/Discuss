<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
/**
 * @package discuss
 * @subpackage lexicon
 */
$_lang['discuss.attachment_err_upload'] = 'Une erreur est survenue lors de l\'upload du fichier : [[+error]]';
$_lang['discuss.attachment_add'] = 'Ajouter une pièce jointe';
$_lang['discuss.attachments'] = 'Pièces jointes';
$_lang['discuss.attachments_max'] = 'maximum de [[+max]]';
$_lang['discuss.attachment_bad_type'] = 'Le fichier [[+idx]] n\'est pas un type de fichier autorisé.';
$_lang['discuss.attachment_err_upload'] = 'Une erreur est survenue lors de l\'upload du fichier [[+idx]].';
$_lang['discuss.attachment_too_large'] = 'Le fichier [[+idx]] ne doit pas peser plus de [[+maxSize]] bytes; celui-ci fait [[+size]] bytes. Veuilez choisir un fichier plus léger.';

$_lang['discuss.message'] = 'Message';
$_lang['discuss.new_post_type_instructions'] = '&larr; Est-ce une discussion générale ou être-vous à la recherche d\'une réponse précise ?';
$_lang['discuss.question_instructions'] = '<b>Aidez-nous à vous aider</b>. Si vous avez des difficultés techniques, veuillez indiquer les informations requises : type et version de serveur web, version de PHP, sa configuration et son mode d\'exécution; informations de MySQL et <em>surtout</em> la version de MODX utilisée ainsi que la liste des add-ons installés.';
$_lang['discuss.question_links'] = '<a href="[[++discuss.questions_link]]" title="Lisez notre poste sur les meilleurs moyens de demander de l\'aide et les informations à considérer en amont">Comment obtenir de l\'aide</a> <a href="" class="surroundText" data-text="[[%discuss.question_template]]" title="Un modèle pratique afin que vous n\'oubliez aucune information importante">Insérer le modèle suggéré pour les questions techniques</a>';
$_lang['discuss.question_template'] = '[b]Description du problème:[/b]

[b]Étapes pour reproduire le problème :[/b]

[b]Résultat attendu :[/b]

[ul]
[*][b]MODX Version :[/b]
[*][b]PHP Version :[/b]
[*][b]Base de données (MySQL, SQL Server, etc) Version :[/b]
[*][b]Informations serveur additionnels :[/b]
[*][b]Add-ons MODX installés :[/b]
[*][b]Contenu du log d\'erreurs :[/b] [i](en pièce jointe si trop volumineu)[/i]
[/ul]
';
$_lang['discuss.discussion_instructions'] = '<b>Soyez clair, concis et répondez au sujet initial</b>. Indiquez un titre de discussion qui donne un aperçu du sujet sans avoir besoin de le lire complètement. Limitez si possible vos sujets à un seul par fil.';
$_lang['discuss.discussion_links'] = '<a href="[[++discuss.guidelines_link]]" title="Découvrez ce que MODX considère commes des sujets appropriés">Lisez les règles des forums…</a>';
$_lang['discuss.new_post_made'] = 'Un nouveau poste a été créé';
$_lang['discuss.notify_of_replies'] = 'Être notifié des réponses';

$_lang['discuss.correct_errors'] = 'Veuillez corriger les erreures du formulaire.';
$_lang['discuss.post_err_create'] = 'Une erreur est survenue lors de la création du nouveau fil.';
$_lang['discuss.post_err_nf'] = 'Poste non trouvé!';
$_lang['discuss.post_err_ns'] = 'Poste non spécifié!';
$_lang['discuss.post_err_ns_message'] = 'Veuillez indiquer un message.';
$_lang['discuss.post_err_ns_title'] = 'Veuillez indiquer un titre de poste valide.';
$_lang['discuss.post_err_remove'] = 'Une erreur est survenue lors de la suppression du poste.';
$_lang['discuss.post_err_reply'] = 'Une erreur est survenue lors de la création de la réponse.';
$_lang['discuss.post_err_save'] = 'Une erreur est survenue lors de l\'enregistrement du poste.';
$_lang['discuss.thread_err_nf'] = 'Fil non trouvé.';

$_lang['discuss.post_modify'] = 'Modifier le poste';
$_lang['discuss.post_new'] = 'Créer un nouveau poste';
$_lang['discuss.post_reply'] = 'Répondre au poste';

$_lang['discuss.solved'] = 'Répondu';
$_lang['discuss.unsolved'] = 'Aucune réponse';

$_lang['discuss.thread_remove'] = 'Supprimer le fil';
$_lang['discuss.thread_remove_confirm'] = 'Êtes-vous sûr de vouloir définitivement supprimer le fil "[[+thread]]" ?';
$_lang['discuss.thread_summary'] = 'Résumé du fil';

$_lang['discuss.title'] = 'Titre';
$_lang['discuss.title_helper'] = 'Entrez un titre de poste descriptif…';
$_lang['discuss.views'] = 'Vues';
$_lang['discuss.preview'] = 'Aperçu';
$_lang['discuss.save_changes'] = 'Enregistrer les modifications';

//nukable below?
$_lang['discuss.thread'] = 'Fil';
