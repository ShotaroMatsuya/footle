<?php
class DomDocumentParser
{

	private $doc;
	private $data;

	public function __construct($url)
	{

		$options = array(
			'http' => array(
				'method' => "GET",
				'header' => "User-Agent: footleBot/0.1\n",
				'follow_location' => true
			),
			'ssl' => array(
				'verify_peer'      => false,
				'verify_peer_name' => false
			)
		);
		$context = stream_context_create($options);

		$this->doc = new DomDocument();
		@$this->doc->loadHTML(file_get_contents($url, false, $context));
		// $this->data = file_get_contents('https://www.example.com/', false, $context);
	}

	public function testRunning()
	{
		return $this->data;
	}
	public function getLinks()
	{
		return $this->doc->getElementsByTagName("a");
	}

	public function getTitleTags()
	{
		return $this->doc->getElementsByTagName("title");
	}

	public function getMetaTags()
	{
		return $this->doc->getElementsByTagName("meta");
	}
	public function getImages()
	{
		return $this->doc->getElementsByTagName("img");
	}
}
