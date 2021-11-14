<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Specification
{
    /** @var string */
    private $id;

    /** @var array<string, Path> */
    private $paths;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id    = $id;
        $this->paths = [];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Path $path
     */
    public function addPath(Path $path)
    {
        $this->paths[$path->getUrl()] = $path;
    }

    /**
     * @param string $url
     *
     * @return Path|null
     */
    public function findPath($url)
    {
        return isset($this->paths[$url]) ? $this->paths[$url] : null;
    }

    /**
     * @return array<string, Path>
     */
    public function getPaths()
    {
        return $this->paths;
    }
}