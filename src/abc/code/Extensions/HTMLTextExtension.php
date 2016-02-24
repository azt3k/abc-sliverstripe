<?php

class HTMLTextExtension extends Extension {

	public function FirstBlock() {
		return $this->owner->FirstBlocks(1);
	}

	public function FirstBlocks($num = 2) {

		// get the Content
		$content = (string) $this->owner;

		// append thestuff dom doc adds incorrectly
		$content = '<!doctype html><html><body>' . $content . '</body></html>';

		// load it into dom doc
		$dom = new DOMDocument;
		@$dom->loadHTML($content);

		// find the body fragment
		$body = $dom->getElementsByTagName('body')[0];

		// init the output string
		$out = new DOMDocument();
		$out->loadHTML('<!doctype html><html><body></body></html>');
		$oBody = $out->getElementsByTagName('body')[0];

		// loop through nodes appending children to the output
		$i = 0;
		foreach ($body->childNodes as $node) {

			// print_r($node);

			// skip text nodes
			if ($node->nodeName == '#text') continue;

			// exit the loop if we have what we need
			if ($i>=$num) break;

			// append the node
			$oBody->appendChild($out->importNode($node, true));

			// increment node count
			$i++;
		}

		// cleanup output
		$htmlFragment = preg_replace(
			'/^<!DOCTYPE.+?>/',
			'',
			str_replace(
				array(
					'<html>',
					'</html>',
					'<body>',
					'</body>'
				),
				array('', '', '', '')
				,
				$out->saveHTML()
			)
		);

		return $htmlFragment;
	}
}
