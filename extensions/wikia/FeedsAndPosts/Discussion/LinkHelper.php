<?php
namespace Wikia\FeedsAndPosts\Discussion;

use GuzzleHttp\Psr7\Uri;
use IContextSource;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\parse_query;

class LinkHelper {
	private $baseDomain;
	private $scriptPath;

	/**
	 * LinkHelper constructor.
	 * @param string $baseDomain
	 * @param string $scriptPath
	 */
	public function __construct( string $baseDomain, string $scriptPath ) {
		$this->baseDomain = $baseDomain;
		$this->scriptPath = $scriptPath;
	}

	public function buildForumlink( Uri $uri, IContextSource $requestContext ): string {
		$urlParts = explode( "/", $uri->getPath() );
		$forumId = end( $urlParts );

		$controllerQueryParams = [
			'controller' => 'DiscussionForum',
			'method' => 'getForum',
			'forumId' => $forumId,
		];

		return $this->buildLink( $uri, $controllerQueryParams );
	}

	public function buildPermalink( Uri $uri, IContextSource $requestContext ): string {
		$urlParts = explode( "/", $uri->getPath() );
		$postId = end( $urlParts );

		$controllerQueryParams = [
			'controller' => 'DiscussionPermalink',
			'method' => 'getThreadByPostId',
			'postId' => $postId
		];

		return $this->buildLink( $uri, $controllerQueryParams );
	}

	public function buildThreadLink( Uri $uri, IContextSource $requestContext ): string {
		$urlParts = explode( "/", $uri->getPath() );
		$threadId = end( $urlParts );

		$controllerQueryParams = [
			'controller' => 'DiscussionThread',
			'method' => 'getThread',
			'threadId' => $threadId,
		];

		return $this->buildLink( $uri, $controllerQueryParams );
	}

	public function buildThreadsLink( Uri $uri, IContextSource $requestContext ): string {
		$controllerQueryParams = [
			'controller' => 'DiscussionThread',
			'method' => 'getThreads',
		];

		return $this->buildLink( $uri, $controllerQueryParams );
	}

	public function buildPostsLink( Uri $uri, IContextSource $requestContext ): string {
		$controllerQueryParams = [
			'controller' => 'DiscussionPost',
			'method' => 'getPosts',
		];

		return $this->buildLink( $uri, $controllerQueryParams );
	}

	private function buildLink( Uri $uri, array $controllerQueryParams ) {
		foreach ( parse_query( $uri->getQuery() ) as $paramName => $value ) {
			$controllerQueryParams[$paramName] = $value;
		}

		return $this->baseDomain . $this->scriptPath . '/wikia.php?' . build_query( $controllerQueryParams );
	}
}
