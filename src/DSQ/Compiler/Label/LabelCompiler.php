<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler\Label;

use DSQ\Compiler\MatcherCompiler;
use DSQ\Expression\CompositeExpression;
use DSQ\Expression\FieldExpression;

/**
 * Transform an Expression into a (nested) array of label/values copules.
 *
 * Class LabelCompiler
 * @package DSQ\Compiler
 */
class LabelCompiler extends MatcherCompiler
{
    private $fieldNameDefaultCallback;

    /**
     * Initialize maps
     *
     * @param callable|null $fieldNameDefaultCallback
     */
    public function __construct($fieldNameDefaultCallback = null)
    {
        parent::__construct();
        
        $this->fieldNameDefaultCallback = $fieldNameDefaultCallback;

        $this
            ->mapByClass('DSQ\Expression\FieldExpression', array($this, 'mapField'))
            ->mapByClass('DSQ\Expression\TreeExpression', array($this, 'mapTree'))
        ;
    }

    /**
     * @param CompositeExpression $expression
     * @param LabelCompiler $compiler
     * @return HumanReadableExpr
     */
    public function mapTree(CompositeExpression $expression, LabelCompiler $compiler)
    {
        return new HumanReadableExpr(
            $expression->getValue(),
            $compiler->compileArray($expression->getChildren())
        );
    }

    /**
     * @param FieldExpression $expression
     * @param LabelCompiler $compiler
     * @return HumanReadableExpr
     */
    public function mapField(FieldExpression $expression, LabelCompiler $compiler)
    {
        return new HumanReadableExpr(
            $compiler->getFieldLabel($expression->getField()),
            is_array($expression->getValue()) ? json_encode($expression->getValue()) : $expression->getValue()
        );
    }

    /**
     * @param $fieldname
     * @return mixed
     */
    public function getFieldLabel($fieldname)
    {
        if (!isset($this->fieldNameDefaultCallback))
            return $fieldname;

        return call_user_func($this->fieldNameDefaultCallback, $fieldname);
    }
} 