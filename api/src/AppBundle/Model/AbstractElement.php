<?php

namespace AppBundle\Model;

use DateTime;

abstract class AbstractElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $tags;

    /**
     * @var DateTime
     */
    protected $updated;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $extension;


    public function __construct(array $meta)
    {
        $filename = $meta['filename'];

        // Parse tags from filename
        preg_match_all('/#([^\s.,\/#!$%\^&\*;:{}=\-`~()]+)/', $filename, $tags);
        $tags = $tags[1];
        foreach($tags as $k => $tag) {
            // Remove tags from filename
            $filename = str_replace("#$tag", "", $filename);
            // Replace underscores by spaces in tags
            $tags[$k] = str_replace("_", " ", $tag);
        }
        // Replace multiple spaces by single space
        $name = preg_replace('/\s+/', ' ', trim($filename));

        $updated = new DateTime();
        $updated->setTimestamp($meta['timestamp']);

        $this->name = $name;
        $this->tags = $tags;
        $this->updated = $updated;
        $this->size = $meta['size'];
        $this->extension = $meta['extension'];
    }
}
