<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Extension;

use Builderius\Symfony\Component\Workflow\Registry;
use Builderius\Symfony\Component\Workflow\Transition;
use Builderius\Symfony\Component\Workflow\TransitionBlockerList;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * WorkflowExtension.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class WorkflowExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $workflowRegistry;
    public function __construct(\Builderius\Symfony\Component\Workflow\Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('workflow_can', [$this, 'canTransition']), new \Builderius\Twig\TwigFunction('workflow_transitions', [$this, 'getEnabledTransitions']), new \Builderius\Twig\TwigFunction('workflow_has_marked_place', [$this, 'hasMarkedPlace']), new \Builderius\Twig\TwigFunction('workflow_marked_places', [$this, 'getMarkedPlaces']), new \Builderius\Twig\TwigFunction('workflow_metadata', [$this, 'getMetadata']), new \Builderius\Twig\TwigFunction('workflow_transition_blockers', [$this, 'buildTransitionBlockerList'])];
    }
    /**
     * Returns true if the transition is enabled.
     */
    public function canTransition(object $subject, string $transitionName, string $name = null) : bool
    {
        return $this->workflowRegistry->get($subject, $name)->can($subject, $transitionName);
    }
    /**
     * Returns all enabled transitions.
     *
     * @return Transition[] All enabled transitions
     */
    public function getEnabledTransitions(object $subject, string $name = null) : array
    {
        return $this->workflowRegistry->get($subject, $name)->getEnabledTransitions($subject);
    }
    /**
     * Returns true if the place is marked.
     */
    public function hasMarkedPlace(object $subject, string $placeName, string $name = null) : bool
    {
        return $this->workflowRegistry->get($subject, $name)->getMarking($subject)->has($placeName);
    }
    /**
     * Returns marked places.
     *
     * @return string[]|int[]
     */
    public function getMarkedPlaces(object $subject, bool $placesNameOnly = \true, string $name = null) : array
    {
        $places = $this->workflowRegistry->get($subject, $name)->getMarking($subject)->getPlaces();
        if ($placesNameOnly) {
            return \array_keys($places);
        }
        return $places;
    }
    /**
     * Returns the metadata for a specific subject.
     *
     * @param string|Transition|null $metadataSubject Use null to get workflow metadata
     *                                                Use a string (the place name) to get place metadata
     *                                                Use a Transition instance to get transition metadata
     */
    public function getMetadata(object $subject, string $key, $metadataSubject = null, string $name = null)
    {
        return $this->workflowRegistry->get($subject, $name)->getMetadataStore()->getMetadata($key, $metadataSubject);
    }
    public function buildTransitionBlockerList(object $subject, string $transitionName, string $name = null) : \Builderius\Symfony\Component\Workflow\TransitionBlockerList
    {
        $workflow = $this->workflowRegistry->get($subject, $name);
        return $workflow->buildTransitionBlockerList($subject, $transitionName);
    }
}
