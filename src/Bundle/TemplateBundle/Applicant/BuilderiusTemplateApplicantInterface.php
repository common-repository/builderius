<?php

namespace Builderius\Bundle\TemplateBundle\Applicant;

interface BuilderiusTemplateApplicantInterface
{
    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters);
}