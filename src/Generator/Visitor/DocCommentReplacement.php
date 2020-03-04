<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;
use function sprintf;
use function str_replace;

final class DocCommentReplacement extends NodeVisitorAbstract
{
    /** @var TypeMap */
    private $typeMap;
    /** @var string[] */
    private $annotations;

    public function __construct(TypeMap $typeMap, string ...$annotations)
    {
        $this->typeMap = $typeMap;
        $this->annotations = $annotations;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return;
        }
        $doc = $node->getDocComment();
        if (!$doc) {
            return;
        }
        $search = [];
        $replace = [];
        foreach ($this->typeMap as $type => $argument) {
            foreach ($this->annotations as $annotation) {
                $search[] = sprintf($annotation, $type);
                $replace[] = sprintf($annotation, $argument);
            }
        }
        $node->setDocComment(new Doc(str_replace($search, $replace, $doc)));
    }
}
