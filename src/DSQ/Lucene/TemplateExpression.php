<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene;

use StringTemplate\Engine;

class TemplateExpression extends AbstractLuceneExpression
{
    /**
     * @var Engine
     */
    private $engine;

    protected $template;

    /**
     * @param string $template  The template string
     * @param string|array $value
     * @param float $boost
     * @param string $type
     */
    public function __construct($template, $value = '', $boost = 1.0, $type = 'basic')
    {
        $this->template = $template;
        parent::__construct($value, $boost, $type);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = $this->getEngine()->render($this->template, $this->getValue());

        if ($this->getBoost() != 1.0)
            $result = "($result)" . $this->boostSuffix();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return false;
    }

    /**
     * Set Engine
     *
     * @param Engine $engine
     *
     * @return TemplateExpression The current instance
     */
    public function setEngine(Engine $engine)
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * Get Engine
     *
     * @return Engine
     */
    public function getEngine()
    {
        if (!isset($this->engine))
            $this->engine = new Engine;

        return $this->engine;
    }
}