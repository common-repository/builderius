<?php

namespace Builderius\enshrined\svgSanitize\ElementReference;

class Usage
{
    /**
     * @var Subject
     */
    protected $subject;
    /**
     * @var int
     */
    protected $count;
    /**
     * @param Subject $subject
     * @param int $count
     */
    public function __construct(\Builderius\enshrined\svgSanitize\ElementReference\Subject $subject, $count = 1)
    {
        $this->subject = $subject;
        $this->count = (int) $count;
    }
    /**
     * @param int $by
     */
    public function increment($by = 1)
    {
        $this->count += (int) $by;
    }
    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
