<?php

namespace Icybee\Modules\Users\Block;

use Brickrouge\Button;
use Brickrouge\Element;
use Brickrouge\Form;
use ICanBoogie\Binding\PrototypedBindings;

class AvailableSitesBlock extends Element
{
	use PrototypedBindings;

	public function render()
	{
		$app = $this->app;
		$document = $app->document;
		$document->js->add('available-sites.js');
		$document->page_title = 'Select a website';

		/* @var $site \Icybee\Modules\Sites\Site */
		$site = $app->site;
		$ws_title = \ICanBoogie\escape($site->admin_title ? $site->admin_title : $site->title .':' . $site->language);

		$available = $site->model
		->where('site_id IN(' . implode(',', $app->user->restricted_sites_ids) . ')')
		->order('admin_title, title')
		->all;

		$uri = substr($_SERVER['REQUEST_URI'], strlen($site->path));
		$options = [];

		foreach ($available as $site)
		{
			$title = $site->title . ':' . $site->language;

			if ($site->admin_title)
			{
				$title .= ' (' . $site->admin_title . ')';
			}

			$options[$site->url . $uri] = $title;
		}

		$form = new Form([

			Form::ACTIONS => new Button('Change', [

				'class' => 'btn-primary',
				'type' => 'submit'

			]),

			Form::RENDERER => Form\GroupRenderer::class,

			Element::CHILDREN => [

				new Element('select', [

					Element::DESCRIPTION => "Select one of the website available to your profile.",
					Element::OPTIONS => $options

				])
			],

			'name' => 'change-working-site',
			'class' => 'form-primary'

		]);

		return <<<EOT
<div id="block--site-access-denied" class="block-alert">
<h2>Access denied</h2>
<p>You don't have permission to access the administration interface for the website <q>$ws_title</q>,
please select another website to work with:</p>
$form
</div>
EOT;
	}
}
