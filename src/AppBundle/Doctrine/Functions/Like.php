<?php

namespace AppBundle\Doctrine\Functions;

use Doctrine\ORM\Query\AST\ArithmeticExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Like extends FunctionNode
{
    /**
     * @var ArithmeticExpression
     */
    public $value;
    /**
     * @var Node
     */
    public $matcher;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->value = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->matcher = $parser->StringExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            '(%s LIKE %s)',
            $this->value->dispatch($sqlWalker),
            $this->matcher->dispatch($sqlWalker)
        );
    }
}
