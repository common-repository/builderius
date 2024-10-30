<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Language;

use Builderius\GraphQL\Error\SyntaxError;
use Builderius\GraphQL\Language\AST\ArgumentNode;
use Builderius\GraphQL\Language\AST\BooleanValueNode;
use Builderius\GraphQL\Language\AST\DefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveDefinitionNode;
use Builderius\GraphQL\Language\AST\DirectiveNode;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumTypeExtensionNode;
use Builderius\GraphQL\Language\AST\EnumValueDefinitionNode;
use Builderius\GraphQL\Language\AST\EnumValueNode;
use Builderius\GraphQL\Language\AST\ExecutableDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldDefinitionNode;
use Builderius\GraphQL\Language\AST\FieldNode;
use Builderius\GraphQL\Language\AST\FloatValueNode;
use Builderius\GraphQL\Language\AST\FragmentDefinitionNode;
use Builderius\GraphQL\Language\AST\FragmentSpreadNode;
use Builderius\GraphQL\Language\AST\InlineFragmentNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\InputValueDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use Builderius\GraphQL\Language\AST\IntValueNode;
use Builderius\GraphQL\Language\AST\ListTypeNode;
use Builderius\GraphQL\Language\AST\ListValueNode;
use Builderius\GraphQL\Language\AST\Location;
use Builderius\GraphQL\Language\AST\NamedTypeNode;
use Builderius\GraphQL\Language\AST\NameNode;
use Builderius\GraphQL\Language\AST\Node;
use Builderius\GraphQL\Language\AST\NodeList;
use Builderius\GraphQL\Language\AST\NonNullTypeNode;
use Builderius\GraphQL\Language\AST\NullValueNode;
use Builderius\GraphQL\Language\AST\ObjectFieldNode;
use Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode;
use Builderius\GraphQL\Language\AST\ObjectValueNode;
use Builderius\GraphQL\Language\AST\OperationDefinitionNode;
use Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SchemaDefinitionNode;
use Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode;
use Builderius\GraphQL\Language\AST\SelectionNode;
use Builderius\GraphQL\Language\AST\SelectionSetNode;
use Builderius\GraphQL\Language\AST\StringValueNode;
use Builderius\GraphQL\Language\AST\TypeExtensionNode;
use Builderius\GraphQL\Language\AST\TypeNode;
use Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode;
use Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode;
use Builderius\GraphQL\Language\AST\UnionTypeExtensionNode;
use Builderius\GraphQL\Language\AST\ValueNode;
use Builderius\GraphQL\Language\AST\VariableDefinitionNode;
use Builderius\GraphQL\Language\AST\VariableNode;
use function count;
use function sprintf;
/**
 * Parses string containing GraphQL query or [type definition](type-system/type-language.md) to Abstract Syntax Tree.
 *
 * Those magic functions allow partial parsing:
 *
 * @method static NameNode name(Source|string $source, bool[] $options = [])
 * @method static DocumentNode document(Source|string $source, bool[] $options = [])
 * @method static ExecutableDefinitionNode|TypeSystemDefinitionNode definition(Source|string $source, bool[] $options = [])
 * @method static ExecutableDefinitionNode executableDefinition(Source|string $source, bool[] $options = [])
 * @method static OperationDefinitionNode operationDefinition(Source|string $source, bool[] $options = [])
 * @method static string operationType(Source|string $source, bool[] $options = [])
 * @method static NodeList<VariableDefinitionNode> variableDefinitions(Source|string $source, bool[] $options = [])
 * @method static VariableDefinitionNode variableDefinition(Source|string $source, bool[] $options = [])
 * @method static VariableNode variable(Source|string $source, bool[] $options = [])
 * @method static SelectionSetNode selectionSet(Source|string $source, bool[] $options = [])
 * @method static mixed selection(Source|string $source, bool[] $options = [])
 * @method static FieldNode field(Source|string $source, bool[] $options = [])
 * @method static NodeList<ArgumentNode> arguments(Source|string $source, bool[] $options = [])
 * @method static NodeList<ArgumentNode> constArguments(Source|string $source, bool[] $options = [])
 * @method static ArgumentNode argument(Source|string $source, bool[] $options = [])
 * @method static ArgumentNode constArgument(Source|string $source, bool[] $options = [])
 * @method static FragmentSpreadNode|InlineFragmentNode fragment(Source|string $source, bool[] $options = [])
 * @method static FragmentDefinitionNode fragmentDefinition(Source|string $source, bool[] $options = [])
 * @method static NameNode fragmentName(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|NullValueNode|ObjectValueNode|StringValueNode|VariableNode valueLiteral(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|NullValueNode|ObjectValueNode|StringValueNode constValueLiteral(Source|string $source, bool[] $options = [])
 * @method static StringValueNode stringLiteral(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode constValue(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode variableValue(Source|string $source, bool[] $options = [])
 * @method static ListValueNode array(Source|string $source, bool[] $options = [])
 * @method static ListValueNode constArray(Source|string $source, bool[] $options = [])
 * @method static ObjectValueNode object(Source|string $source, bool[] $options = [])
 * @method static ObjectValueNode constObject(Source|string $source, bool[] $options = [])
 * @method static ObjectFieldNode objectField(Source|string $source, bool[] $options = [])
 * @method static ObjectFieldNode constObjectField(Source|string $source, bool[] $options = [])
 * @method static NodeList<DirectiveNode> directives(Source|string $source, bool[] $options = [])
 * @method static NodeList<DirectiveNode> constDirectives(Source|string $source, bool[] $options = [])
 * @method static DirectiveNode directive(Source|string $source, bool[] $options = [])
 * @method static DirectiveNode constDirective(Source|string $source, bool[] $options = [])
 * @method static ListTypeNode|NamedTypeNode|NonNullTypeNode typeReference(Source|string $source, bool[] $options = [])
 * @method static NamedTypeNode namedType(Source|string $source, bool[] $options = [])
 * @method static TypeSystemDefinitionNode typeSystemDefinition(Source|string $source, bool[] $options = [])
 * @method static StringValueNode|null description(Source|string $source, bool[] $options = [])
 * @method static SchemaDefinitionNode schemaDefinition(Source|string $source, bool[] $options = [])
 * @method static OperationTypeDefinitionNode operationTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static ScalarTypeDefinitionNode scalarTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static ObjectTypeDefinitionNode objectTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NamedTypeNode> implementsInterfaces(Source|string $source, bool[] $options = [])
 * @method static NodeList<FieldDefinitionNode> fieldsDefinition(Source|string $source, bool[] $options = [])
 * @method static FieldDefinitionNode fieldDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<InputValueDefinitionNode> argumentsDefinition(Source|string $source, bool[] $options = [])
 * @method static InputValueDefinitionNode inputValueDefinition(Source|string $source, bool[] $options = [])
 * @method static InterfaceTypeDefinitionNode interfaceTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static UnionTypeDefinitionNode unionTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NamedTypeNode> unionMemberTypes(Source|string $source, bool[] $options = [])
 * @method static EnumTypeDefinitionNode enumTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<EnumValueDefinitionNode> enumValuesDefinition(Source|string $source, bool[] $options = [])
 * @method static EnumValueDefinitionNode enumValueDefinition(Source|string $source, bool[] $options = [])
 * @method static InputObjectTypeDefinitionNode inputObjectTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<InputValueDefinitionNode> inputFieldsDefinition(Source|string $source, bool[] $options = [])
 * @method static TypeExtensionNode typeExtension(Source|string $source, bool[] $options = [])
 * @method static SchemaTypeExtensionNode schemaTypeExtension(Source|string $source, bool[] $options = [])
 * @method static ScalarTypeExtensionNode scalarTypeExtension(Source|string $source, bool[] $options = [])
 * @method static ObjectTypeExtensionNode objectTypeExtension(Source|string $source, bool[] $options = [])
 * @method static InterfaceTypeExtensionNode interfaceTypeExtension(Source|string $source, bool[] $options = [])
 * @method static UnionTypeExtensionNode unionTypeExtension(Source|string $source, bool[] $options = [])
 * @method static EnumTypeExtensionNode enumTypeExtension(Source|string $source, bool[] $options = [])
 * @method static InputObjectTypeExtensionNode inputObjectTypeExtension(Source|string $source, bool[] $options = [])
 * @method static DirectiveDefinitionNode directiveDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NameNode> directiveLocations(Source|string $source, bool[] $options = [])
 * @method static NameNode directiveLocation(Source|string $source, bool[] $options = [])
 */
class Parser
{
    /**
     * Given a GraphQL source, parses it into a `GraphQL\Language\AST\DocumentNode`.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * Available options:
     *
     * noLocation: boolean,
     *   (By default, the parser creates AST nodes that know the location
     *   in the source that they correspond to. This configuration flag
     *   disables that behavior for performance or testing.)
     *
     * allowLegacySDLEmptyFields: boolean
     *   If enabled, the parser will parse empty fields sets in the Schema
     *   Definition Language. Otherwise, the parser will follow the current
     *   specification.
     *
     *   This option is provided to ease adoption of the final SDL specification
     *   and will be removed in a future major release.
     *
     * allowLegacySDLImplementsInterfaces: boolean
     *   If enabled, the parser will parse implemented interfaces with no `&`
     *   character between each interface. Otherwise, the parser will follow the
     *   current specification.
     *
     *   This option is provided to ease adoption of the final SDL specification
     *   and will be removed in a future major release.
     *
     * experimentalFragmentVariables: boolean,
     *   (If enabled, the parser will understand and parse variable definitions
     *   contained in a fragment definition. They'll be represented in the
     *   `variableDefinitions` field of the FragmentDefinitionNode.
     *
     *   The syntax is identical to normal, query-defined variables. For example:
     *
     *     fragment A($var: Boolean = false) on T  {
     *       ...
     *     }
     *
     *   Note: this feature is experimental and may change or be removed in the
     *   future.)
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return DocumentNode
     *
     * @throws SyntaxError
     *
     * @api
     */
    public static function parse($source, array $options = [])
    {
        $parser = new self($source, $options);
        return $parser->parseDocument();
    }
    /**
     * Given a string containing a GraphQL value (ex. `[42]`), parse the AST for
     * that value.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * This is useful within tools that operate upon GraphQL Values directly and
     * in isolation of complete GraphQL documents.
     *
     * Consider providing the results to the utility function: `GraphQL\Utils\AST::valueFromAST()`.
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode
     *
     * @api
     */
    public static function parseValue($source, array $options = [])
    {
        $parser = new \Builderius\GraphQL\Language\Parser($source, $options);
        $parser->expect(\Builderius\GraphQL\Language\Token::SOF);
        $value = $parser->parseValueLiteral(\false);
        $parser->expect(\Builderius\GraphQL\Language\Token::EOF);
        return $value;
    }
    /**
     * Given a string containing a GraphQL Type (ex. `[Int!]`), parse the AST for
     * that type.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * This is useful within tools that operate upon GraphQL Types directly and
     * in isolation of complete GraphQL documents.
     *
     * Consider providing the results to the utility function: `GraphQL\Utils\AST::typeFromAST()`.
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return ListTypeNode|NamedTypeNode|NonNullTypeNode
     *
     * @api
     */
    public static function parseType($source, array $options = [])
    {
        $parser = new \Builderius\GraphQL\Language\Parser($source, $options);
        $parser->expect(\Builderius\GraphQL\Language\Token::SOF);
        $type = $parser->parseTypeReference();
        $parser->expect(\Builderius\GraphQL\Language\Token::EOF);
        return $type;
    }
    /**
     * Parse partial source by delegating calls to the internal parseX methods.
     *
     * @param bool[] $arguments
     *
     * @throws SyntaxError
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $parser = new \Builderius\GraphQL\Language\Parser(...$arguments);
        $parser->expect(\Builderius\GraphQL\Language\Token::SOF);
        switch ($name) {
            case 'arguments':
            case 'valueLiteral':
            case 'array':
            case 'object':
            case 'objectField':
            case 'directives':
            case 'directive':
                $type = $parser->{'parse' . $name}(\false);
                break;
            case 'constArguments':
                $type = $parser->parseArguments(\true);
                break;
            case 'constValueLiteral':
                $type = $parser->parseValueLiteral(\true);
                break;
            case 'constArray':
                $type = $parser->parseArray(\true);
                break;
            case 'constObject':
                $type = $parser->parseObject(\true);
                break;
            case 'constObjectField':
                $type = $parser->parseObjectField(\true);
                break;
            case 'constDirectives':
                $type = $parser->parseDirectives(\true);
                break;
            case 'constDirective':
                $type = $parser->parseDirective(\true);
                break;
            default:
                $type = $parser->{'parse' . $name}();
        }
        $parser->expect(\Builderius\GraphQL\Language\Token::EOF);
        return $type;
    }
    /** @var Lexer */
    private $lexer;
    /**
     * @param Source|string $source
     * @param bool[]        $options
     */
    public function __construct($source, array $options = [])
    {
        $sourceObj = $source instanceof \Builderius\GraphQL\Language\Source ? $source : new \Builderius\GraphQL\Language\Source($source);
        $this->lexer = new \Builderius\GraphQL\Language\Lexer($sourceObj, $options);
    }
    /**
     * Returns a location object, used to identify the place in
     * the source that created a given parsed object.
     */
    private function loc(\Builderius\GraphQL\Language\Token $startToken) : ?\Builderius\GraphQL\Language\AST\Location
    {
        if (!($this->lexer->options['noLocation'] ?? \false)) {
            return new \Builderius\GraphQL\Language\AST\Location($startToken, $this->lexer->lastToken, $this->lexer->source);
        }
        return null;
    }
    /**
     * Determines if the next token is of a given kind
     */
    private function peek(string $kind) : bool
    {
        return $this->lexer->token->kind === $kind;
    }
    /**
     * If the next token is of the given kind, return true after advancing
     * the parser. Otherwise, do not change the parser state and return false.
     */
    private function skip(string $kind) : bool
    {
        $match = $this->lexer->token->kind === $kind;
        if ($match) {
            $this->lexer->advance();
        }
        return $match;
    }
    /**
     * If the next token is of the given kind, return that token after advancing
     * the parser. Otherwise, do not change the parser state and return false.
     *
     * @throws SyntaxError
     */
    private function expect(string $kind) : \Builderius\GraphQL\Language\Token
    {
        $token = $this->lexer->token;
        if ($token->kind === $kind) {
            $this->lexer->advance();
            return $token;
        }
        throw new \Builderius\GraphQL\Error\SyntaxError($this->lexer->source, $token->start, \sprintf('Expected %s, found %s', $kind, $token->getDescription()));
    }
    /**
     * If the next token is a keyword with the given value, advance the lexer.
     * Otherwise, throw an error.
     *
     * @throws SyntaxError
     */
    private function expectKeyword(string $value) : void
    {
        $token = $this->lexer->token;
        if ($token->kind !== \Builderius\GraphQL\Language\Token::NAME || $token->value !== $value) {
            throw new \Builderius\GraphQL\Error\SyntaxError($this->lexer->source, $token->start, 'Expected "' . $value . '", found ' . $token->getDescription());
        }
        $this->lexer->advance();
    }
    /**
     * If the next token is a given keyword, return "true" after advancing
     * the lexer. Otherwise, do not change the parser state and return "false".
     */
    private function expectOptionalKeyword(string $value) : bool
    {
        $token = $this->lexer->token;
        if ($token->kind === \Builderius\GraphQL\Language\Token::NAME && $token->value === $value) {
            $this->lexer->advance();
            return \true;
        }
        return \false;
    }
    private function unexpected(?\Builderius\GraphQL\Language\Token $atToken = null) : \Builderius\GraphQL\Error\SyntaxError
    {
        $token = $atToken ?? $this->lexer->token;
        return new \Builderius\GraphQL\Error\SyntaxError($this->lexer->source, $token->start, 'Unexpected ' . $token->getDescription());
    }
    /**
     * Returns a possibly empty list of parse nodes, determined by
     * the parseFn. This list begins with a lex token of openKind
     * and ends with a lex token of closeKind. Advances the parser
     * to the next lex token after the closing token.
     *
     * @throws SyntaxError
     */
    private function any(string $openKind, callable $parseFn, string $closeKind) : \Builderius\GraphQL\Language\AST\NodeList
    {
        $this->expect($openKind);
        $nodes = [];
        while (!$this->skip($closeKind)) {
            $nodes[] = $parseFn($this);
        }
        return new \Builderius\GraphQL\Language\AST\NodeList($nodes);
    }
    /**
     * Returns a non-empty list of parse nodes, determined by
     * the parseFn. This list begins with a lex token of openKind
     * and ends with a lex token of closeKind. Advances the parser
     * to the next lex token after the closing token.
     *
     * @throws SyntaxError
     */
    private function many(string $openKind, callable $parseFn, string $closeKind) : \Builderius\GraphQL\Language\AST\NodeList
    {
        $this->expect($openKind);
        $nodes = [$parseFn($this)];
        while (!$this->skip($closeKind)) {
            $nodes[] = $parseFn($this);
        }
        return new \Builderius\GraphQL\Language\AST\NodeList($nodes);
    }
    /**
     * Converts a name lex token into a name parse node.
     *
     * @throws SyntaxError
     */
    private function parseName() : \Builderius\GraphQL\Language\AST\NameNode
    {
        $token = $this->expect(\Builderius\GraphQL\Language\Token::NAME);
        return new \Builderius\GraphQL\Language\AST\NameNode(['value' => $token->value, 'loc' => $this->loc($token)]);
    }
    /**
     * Implements the parsing rules in the Document section.
     *
     * @throws SyntaxError
     */
    private function parseDocument() : \Builderius\GraphQL\Language\AST\DocumentNode
    {
        $start = $this->lexer->token;
        return new \Builderius\GraphQL\Language\AST\DocumentNode(['definitions' => $this->many(\Builderius\GraphQL\Language\Token::SOF, function () {
            return $this->parseDefinition();
        }, \Builderius\GraphQL\Language\Token::EOF), 'loc' => $this->loc($start)]);
    }
    /**
     * @return ExecutableDefinitionNode|TypeSystemDefinitionNode
     *
     * @throws SyntaxError
     */
    private function parseDefinition() : \Builderius\GraphQL\Language\AST\DefinitionNode
    {
        if ($this->peek(\Builderius\GraphQL\Language\Token::NAME)) {
            switch ($this->lexer->token->value) {
                case 'query':
                case 'mutation':
                case 'subscription':
                case 'fragment':
                    return $this->parseExecutableDefinition();
                // Note: The schema definition language is an experimental addition.
                case 'schema':
                case 'scalar':
                case 'type':
                case 'interface':
                case 'union':
                case 'enum':
                case 'input':
                case 'extend':
                case 'directive':
                    // Note: The schema definition language is an experimental addition.
                    return $this->parseTypeSystemDefinition();
            }
        } elseif ($this->peek(\Builderius\GraphQL\Language\Token::BRACE_L)) {
            return $this->parseExecutableDefinition();
        } elseif ($this->peekDescription()) {
            // Note: The schema definition language is an experimental addition.
            return $this->parseTypeSystemDefinition();
        }
        throw $this->unexpected();
    }
    /**
     * @throws SyntaxError
     */
    private function parseExecutableDefinition() : \Builderius\GraphQL\Language\AST\ExecutableDefinitionNode
    {
        if ($this->peek(\Builderius\GraphQL\Language\Token::NAME)) {
            switch ($this->lexer->token->value) {
                case 'query':
                case 'mutation':
                case 'subscription':
                    return $this->parseOperationDefinition();
                case 'fragment':
                    return $this->parseFragmentDefinition();
            }
        } elseif ($this->peek(\Builderius\GraphQL\Language\Token::BRACE_L)) {
            return $this->parseOperationDefinition();
        }
        throw $this->unexpected();
    }
    // Implements the parsing rules in the Operations section.
    /**
     * @throws SyntaxError
     */
    private function parseOperationDefinition() : \Builderius\GraphQL\Language\AST\OperationDefinitionNode
    {
        $start = $this->lexer->token;
        if ($this->peek(\Builderius\GraphQL\Language\Token::BRACE_L)) {
            return new \Builderius\GraphQL\Language\AST\OperationDefinitionNode(['operation' => 'query', 'name' => null, 'variableDefinitions' => new \Builderius\GraphQL\Language\AST\NodeList([]), 'directives' => new \Builderius\GraphQL\Language\AST\NodeList([]), 'selectionSet' => $this->parseSelectionSet(), 'loc' => $this->loc($start)]);
        }
        $operation = $this->parseOperationType();
        $name = null;
        if ($this->peek(\Builderius\GraphQL\Language\Token::NAME)) {
            $name = $this->parseName();
        }
        return new \Builderius\GraphQL\Language\AST\OperationDefinitionNode(['operation' => $operation, 'name' => $name, 'variableDefinitions' => $this->parseVariableDefinitions(), 'directives' => $this->parseDirectives(\false), 'selectionSet' => $this->parseSelectionSet(), 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseOperationType() : string
    {
        $operationToken = $this->expect(\Builderius\GraphQL\Language\Token::NAME);
        switch ($operationToken->value) {
            case 'query':
                return 'query';
            case 'mutation':
                return 'mutation';
            case 'subscription':
                return 'subscription';
        }
        throw $this->unexpected($operationToken);
    }
    private function parseVariableDefinitions() : \Builderius\GraphQL\Language\AST\NodeList
    {
        return $this->peek(\Builderius\GraphQL\Language\Token::PAREN_L) ? $this->many(\Builderius\GraphQL\Language\Token::PAREN_L, function () : VariableDefinitionNode {
            return $this->parseVariableDefinition();
        }, \Builderius\GraphQL\Language\Token::PAREN_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseVariableDefinition() : \Builderius\GraphQL\Language\AST\VariableDefinitionNode
    {
        $start = $this->lexer->token;
        $var = $this->parseVariable();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $type = $this->parseTypeReference();
        return new \Builderius\GraphQL\Language\AST\VariableDefinitionNode(['variable' => $var, 'type' => $type, 'defaultValue' => $this->skip(\Builderius\GraphQL\Language\Token::EQUALS) ? $this->parseValueLiteral(\true) : null, 'directives' => $this->parseDirectives(\true), 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseVariable() : \Builderius\GraphQL\Language\AST\VariableNode
    {
        $start = $this->lexer->token;
        $this->expect(\Builderius\GraphQL\Language\Token::DOLLAR);
        return new \Builderius\GraphQL\Language\AST\VariableNode(['name' => $this->parseName(), 'loc' => $this->loc($start)]);
    }
    private function parseSelectionSet() : \Builderius\GraphQL\Language\AST\SelectionSetNode
    {
        $start = $this->lexer->token;
        return new \Builderius\GraphQL\Language\AST\SelectionSetNode(['selections' => $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, function () : SelectionNode {
            return $this->parseSelection();
        }, \Builderius\GraphQL\Language\Token::BRACE_R), 'loc' => $this->loc($start)]);
    }
    /**
     *  Selection :
     *   - Field
     *   - FragmentSpread
     *   - InlineFragment
     */
    private function parseSelection() : \Builderius\GraphQL\Language\AST\SelectionNode
    {
        return $this->peek(\Builderius\GraphQL\Language\Token::SPREAD) ? $this->parseFragment() : $this->parseField();
    }
    /**
     * @throws SyntaxError
     */
    private function parseField() : \Builderius\GraphQL\Language\AST\FieldNode
    {
        $start = $this->lexer->token;
        $nameOrAlias = $this->parseName();
        if ($this->skip(\Builderius\GraphQL\Language\Token::COLON)) {
            $alias = $nameOrAlias;
            $name = $this->parseName();
        } else {
            $alias = null;
            $name = $nameOrAlias;
        }
        return new \Builderius\GraphQL\Language\AST\FieldNode(['alias' => $alias, 'name' => $name, 'arguments' => $this->parseArguments(\false), 'directives' => $this->parseDirectives(\false), 'selectionSet' => $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) ? $this->parseSelectionSet() : null, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseArguments(bool $isConst) : \Builderius\GraphQL\Language\AST\NodeList
    {
        $parseFn = $isConst ? function () : ArgumentNode {
            return $this->parseConstArgument();
        } : function () : ArgumentNode {
            return $this->parseArgument();
        };
        return $this->peek(\Builderius\GraphQL\Language\Token::PAREN_L) ? $this->many(\Builderius\GraphQL\Language\Token::PAREN_L, $parseFn, \Builderius\GraphQL\Language\Token::PAREN_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseArgument() : \Builderius\GraphQL\Language\AST\ArgumentNode
    {
        $start = $this->lexer->token;
        $name = $this->parseName();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $value = $this->parseValueLiteral(\false);
        return new \Builderius\GraphQL\Language\AST\ArgumentNode(['name' => $name, 'value' => $value, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseConstArgument() : \Builderius\GraphQL\Language\AST\ArgumentNode
    {
        $start = $this->lexer->token;
        $name = $this->parseName();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $value = $this->parseConstValue();
        return new \Builderius\GraphQL\Language\AST\ArgumentNode(['name' => $name, 'value' => $value, 'loc' => $this->loc($start)]);
    }
    // Implements the parsing rules in the Fragments section.
    /**
     * @return FragmentSpreadNode|InlineFragmentNode
     *
     * @throws SyntaxError
     */
    private function parseFragment() : \Builderius\GraphQL\Language\AST\SelectionNode
    {
        $start = $this->lexer->token;
        $this->expect(\Builderius\GraphQL\Language\Token::SPREAD);
        $hasTypeCondition = $this->expectOptionalKeyword('on');
        if (!$hasTypeCondition && $this->peek(\Builderius\GraphQL\Language\Token::NAME)) {
            return new \Builderius\GraphQL\Language\AST\FragmentSpreadNode(['name' => $this->parseFragmentName(), 'directives' => $this->parseDirectives(\false), 'loc' => $this->loc($start)]);
        }
        return new \Builderius\GraphQL\Language\AST\InlineFragmentNode(['typeCondition' => $hasTypeCondition ? $this->parseNamedType() : null, 'directives' => $this->parseDirectives(\false), 'selectionSet' => $this->parseSelectionSet(), 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseFragmentDefinition() : \Builderius\GraphQL\Language\AST\FragmentDefinitionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('fragment');
        $name = $this->parseFragmentName();
        // Experimental support for defining variables within fragments changes
        // the grammar of FragmentDefinition:
        //   - fragment FragmentName VariableDefinitions? on TypeCondition Directives? SelectionSet
        $variableDefinitions = null;
        if (isset($this->lexer->options['experimentalFragmentVariables'])) {
            $variableDefinitions = $this->parseVariableDefinitions();
        }
        $this->expectKeyword('on');
        $typeCondition = $this->parseNamedType();
        return new \Builderius\GraphQL\Language\AST\FragmentDefinitionNode(['name' => $name, 'variableDefinitions' => $variableDefinitions, 'typeCondition' => $typeCondition, 'directives' => $this->parseDirectives(\false), 'selectionSet' => $this->parseSelectionSet(), 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseFragmentName() : \Builderius\GraphQL\Language\AST\NameNode
    {
        if ($this->lexer->token->value === 'on') {
            throw $this->unexpected();
        }
        return $this->parseName();
    }
    // Implements the parsing rules in the Values section.
    /**
     * Value[Const] :
     *   - [~Const] Variable
     *   - IntValue
     *   - FloatValue
     *   - StringValue
     *   - BooleanValue
     *   - NullValue
     *   - EnumValue
     *   - ListValue[?Const]
     *   - ObjectValue[?Const]
     *
     * BooleanValue : one of `true` `false`
     *
     * NullValue : `null`
     *
     * EnumValue : Name but not `true`, `false` or `null`
     *
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode|VariableNode|ListValueNode|ObjectValueNode|NullValueNode
     *
     * @throws SyntaxError
     */
    private function parseValueLiteral(bool $isConst) : \Builderius\GraphQL\Language\AST\ValueNode
    {
        $token = $this->lexer->token;
        switch ($token->kind) {
            case \Builderius\GraphQL\Language\Token::BRACKET_L:
                return $this->parseArray($isConst);
            case \Builderius\GraphQL\Language\Token::BRACE_L:
                return $this->parseObject($isConst);
            case \Builderius\GraphQL\Language\Token::INT:
                $this->lexer->advance();
                return new \Builderius\GraphQL\Language\AST\IntValueNode(['value' => $token->value, 'loc' => $this->loc($token)]);
            case \Builderius\GraphQL\Language\Token::FLOAT:
                $this->lexer->advance();
                return new \Builderius\GraphQL\Language\AST\FloatValueNode(['value' => $token->value, 'loc' => $this->loc($token)]);
            case \Builderius\GraphQL\Language\Token::STRING:
            case \Builderius\GraphQL\Language\Token::BLOCK_STRING:
                return $this->parseStringLiteral();
            case \Builderius\GraphQL\Language\Token::NAME:
                if ($token->value === 'true' || $token->value === 'false') {
                    $this->lexer->advance();
                    return new \Builderius\GraphQL\Language\AST\BooleanValueNode(['value' => $token->value === 'true', 'loc' => $this->loc($token)]);
                }
                if ($token->value === 'null') {
                    $this->lexer->advance();
                    return new \Builderius\GraphQL\Language\AST\NullValueNode(['loc' => $this->loc($token)]);
                } else {
                    $this->lexer->advance();
                    return new \Builderius\GraphQL\Language\AST\EnumValueNode(['value' => $token->value, 'loc' => $this->loc($token)]);
                }
                break;
            case \Builderius\GraphQL\Language\Token::DOLLAR:
                if (!$isConst) {
                    return $this->parseVariable();
                }
                break;
        }
        throw $this->unexpected();
    }
    private function parseStringLiteral() : \Builderius\GraphQL\Language\AST\StringValueNode
    {
        $token = $this->lexer->token;
        $this->lexer->advance();
        return new \Builderius\GraphQL\Language\AST\StringValueNode(['value' => $token->value, 'block' => $token->kind === \Builderius\GraphQL\Language\Token::BLOCK_STRING, 'loc' => $this->loc($token)]);
    }
    /**
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode|VariableNode
     *
     * @throws SyntaxError
     */
    private function parseConstValue() : \Builderius\GraphQL\Language\AST\ValueNode
    {
        return $this->parseValueLiteral(\true);
    }
    /**
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode
     */
    private function parseVariableValue() : \Builderius\GraphQL\Language\AST\ValueNode
    {
        return $this->parseValueLiteral(\false);
    }
    private function parseArray(bool $isConst) : \Builderius\GraphQL\Language\AST\ListValueNode
    {
        $start = $this->lexer->token;
        $parseFn = $isConst ? function () {
            return $this->parseConstValue();
        } : function () {
            return $this->parseVariableValue();
        };
        return new \Builderius\GraphQL\Language\AST\ListValueNode(['values' => $this->any(\Builderius\GraphQL\Language\Token::BRACKET_L, $parseFn, \Builderius\GraphQL\Language\Token::BRACKET_R), 'loc' => $this->loc($start)]);
    }
    private function parseObject(bool $isConst) : \Builderius\GraphQL\Language\AST\ObjectValueNode
    {
        $start = $this->lexer->token;
        $this->expect(\Builderius\GraphQL\Language\Token::BRACE_L);
        $fields = [];
        while (!$this->skip(\Builderius\GraphQL\Language\Token::BRACE_R)) {
            $fields[] = $this->parseObjectField($isConst);
        }
        return new \Builderius\GraphQL\Language\AST\ObjectValueNode(['fields' => new \Builderius\GraphQL\Language\AST\NodeList($fields), 'loc' => $this->loc($start)]);
    }
    private function parseObjectField(bool $isConst) : \Builderius\GraphQL\Language\AST\ObjectFieldNode
    {
        $start = $this->lexer->token;
        $name = $this->parseName();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        return new \Builderius\GraphQL\Language\AST\ObjectFieldNode(['name' => $name, 'value' => $this->parseValueLiteral($isConst), 'loc' => $this->loc($start)]);
    }
    // Implements the parsing rules in the Directives section.
    /**
     * @throws SyntaxError
     */
    private function parseDirectives(bool $isConst) : \Builderius\GraphQL\Language\AST\NodeList
    {
        $directives = [];
        while ($this->peek(\Builderius\GraphQL\Language\Token::AT)) {
            $directives[] = $this->parseDirective($isConst);
        }
        return new \Builderius\GraphQL\Language\AST\NodeList($directives);
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirective(bool $isConst) : \Builderius\GraphQL\Language\AST\DirectiveNode
    {
        $start = $this->lexer->token;
        $this->expect(\Builderius\GraphQL\Language\Token::AT);
        return new \Builderius\GraphQL\Language\AST\DirectiveNode(['name' => $this->parseName(), 'arguments' => $this->parseArguments($isConst), 'loc' => $this->loc($start)]);
    }
    // Implements the parsing rules in the Types section.
    /**
     * Handles the Type: TypeName, ListType, and NonNullType parsing rules.
     *
     * @return ListTypeNode|NamedTypeNode|NonNullTypeNode
     *
     * @throws SyntaxError
     */
    private function parseTypeReference() : \Builderius\GraphQL\Language\AST\TypeNode
    {
        $start = $this->lexer->token;
        if ($this->skip(\Builderius\GraphQL\Language\Token::BRACKET_L)) {
            $type = $this->parseTypeReference();
            $this->expect(\Builderius\GraphQL\Language\Token::BRACKET_R);
            $type = new \Builderius\GraphQL\Language\AST\ListTypeNode(['type' => $type, 'loc' => $this->loc($start)]);
        } else {
            $type = $this->parseNamedType();
        }
        if ($this->skip(\Builderius\GraphQL\Language\Token::BANG)) {
            return new \Builderius\GraphQL\Language\AST\NonNullTypeNode(['type' => $type, 'loc' => $this->loc($start)]);
        }
        return $type;
    }
    private function parseNamedType() : \Builderius\GraphQL\Language\AST\NamedTypeNode
    {
        $start = $this->lexer->token;
        return new \Builderius\GraphQL\Language\AST\NamedTypeNode(['name' => $this->parseName(), 'loc' => $this->loc($start)]);
    }
    // Implements the parsing rules in the Type Definition section.
    /**
     * TypeSystemDefinition :
     *   - SchemaDefinition
     *   - TypeDefinition
     *   - TypeExtension
     *   - DirectiveDefinition
     *
     * TypeDefinition :
     *   - ScalarTypeDefinition
     *   - ObjectTypeDefinition
     *   - InterfaceTypeDefinition
     *   - UnionTypeDefinition
     *   - EnumTypeDefinition
     *   - InputObjectTypeDefinition
     *
     * @throws SyntaxError
     */
    private function parseTypeSystemDefinition() : \Builderius\GraphQL\Language\AST\TypeSystemDefinitionNode
    {
        // Many definitions begin with a description and require a lookahead.
        $keywordToken = $this->peekDescription() ? $this->lexer->lookahead() : $this->lexer->token;
        if ($keywordToken->kind === \Builderius\GraphQL\Language\Token::NAME) {
            switch ($keywordToken->value) {
                case 'schema':
                    return $this->parseSchemaDefinition();
                case 'scalar':
                    return $this->parseScalarTypeDefinition();
                case 'type':
                    return $this->parseObjectTypeDefinition();
                case 'interface':
                    return $this->parseInterfaceTypeDefinition();
                case 'union':
                    return $this->parseUnionTypeDefinition();
                case 'enum':
                    return $this->parseEnumTypeDefinition();
                case 'input':
                    return $this->parseInputObjectTypeDefinition();
                case 'extend':
                    return $this->parseTypeExtension();
                case 'directive':
                    return $this->parseDirectiveDefinition();
            }
        }
        throw $this->unexpected($keywordToken);
    }
    private function peekDescription() : bool
    {
        return $this->peek(\Builderius\GraphQL\Language\Token::STRING) || $this->peek(\Builderius\GraphQL\Language\Token::BLOCK_STRING);
    }
    private function parseDescription() : ?\Builderius\GraphQL\Language\AST\StringValueNode
    {
        if ($this->peekDescription()) {
            return $this->parseStringLiteral();
        }
        return null;
    }
    /**
     * @throws SyntaxError
     */
    private function parseSchemaDefinition() : \Builderius\GraphQL\Language\AST\SchemaDefinitionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('schema');
        $directives = $this->parseDirectives(\true);
        $operationTypes = $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, function () : OperationTypeDefinitionNode {
            return $this->parseOperationTypeDefinition();
        }, \Builderius\GraphQL\Language\Token::BRACE_R);
        return new \Builderius\GraphQL\Language\AST\SchemaDefinitionNode(['directives' => $directives, 'operationTypes' => $operationTypes, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseOperationTypeDefinition() : \Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $operation = $this->parseOperationType();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $type = $this->parseNamedType();
        return new \Builderius\GraphQL\Language\AST\OperationTypeDefinitionNode(['operation' => $operation, 'type' => $type, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseScalarTypeDefinition() : \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('scalar');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        return new \Builderius\GraphQL\Language\AST\ScalarTypeDefinitionNode(['name' => $name, 'directives' => $directives, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseObjectTypeDefinition() : \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('type');
        $name = $this->parseName();
        $interfaces = $this->parseImplementsInterfaces();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseFieldsDefinition();
        return new \Builderius\GraphQL\Language\AST\ObjectTypeDefinitionNode(['name' => $name, 'interfaces' => $interfaces, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * ImplementsInterfaces :
     *   - implements `&`? NamedType
     *   - ImplementsInterfaces & NamedType
     */
    private function parseImplementsInterfaces() : \Builderius\GraphQL\Language\AST\NodeList
    {
        $types = [];
        if ($this->expectOptionalKeyword('implements')) {
            // Optional leading ampersand
            $this->skip(\Builderius\GraphQL\Language\Token::AMP);
            do {
                $types[] = $this->parseNamedType();
            } while ($this->skip(\Builderius\GraphQL\Language\Token::AMP) || ($this->lexer->options['allowLegacySDLImplementsInterfaces'] ?? \false) && $this->peek(\Builderius\GraphQL\Language\Token::NAME));
        }
        return new \Builderius\GraphQL\Language\AST\NodeList($types);
    }
    /**
     * @throws SyntaxError
     */
    private function parseFieldsDefinition() : \Builderius\GraphQL\Language\AST\NodeList
    {
        // Legacy support for the SDL?
        if (($this->lexer->options['allowLegacySDLEmptyFields'] ?? \false) && $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) && $this->lexer->lookahead()->kind === \Builderius\GraphQL\Language\Token::BRACE_R) {
            $this->lexer->advance();
            $this->lexer->advance();
            /** @phpstan-var NodeList<FieldDefinitionNode&Node> $nodeList */
            $nodeList = new \Builderius\GraphQL\Language\AST\NodeList([]);
        } else {
            /** @phpstan-var NodeList<FieldDefinitionNode&Node> $nodeList */
            $nodeList = $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) ? $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, function () : FieldDefinitionNode {
                return $this->parseFieldDefinition();
            }, \Builderius\GraphQL\Language\Token::BRACE_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
        }
        return $nodeList;
    }
    /**
     * @throws SyntaxError
     */
    private function parseFieldDefinition() : \Builderius\GraphQL\Language\AST\FieldDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $name = $this->parseName();
        $args = $this->parseArgumentsDefinition();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $type = $this->parseTypeReference();
        $directives = $this->parseDirectives(\true);
        return new \Builderius\GraphQL\Language\AST\FieldDefinitionNode(['name' => $name, 'arguments' => $args, 'type' => $type, 'directives' => $directives, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseArgumentsDefinition() : \Builderius\GraphQL\Language\AST\NodeList
    {
        /** @var NodeList<InputValueDefinitionNode&Node> $nodeList */
        $nodeList = $this->peek(\Builderius\GraphQL\Language\Token::PAREN_L) ? $this->many(\Builderius\GraphQL\Language\Token::PAREN_L, function () : InputValueDefinitionNode {
            return $this->parseInputValueDefinition();
        }, \Builderius\GraphQL\Language\Token::PAREN_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
        return $nodeList;
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputValueDefinition() : \Builderius\GraphQL\Language\AST\InputValueDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $name = $this->parseName();
        $this->expect(\Builderius\GraphQL\Language\Token::COLON);
        $type = $this->parseTypeReference();
        $defaultValue = null;
        if ($this->skip(\Builderius\GraphQL\Language\Token::EQUALS)) {
            $defaultValue = $this->parseConstValue();
        }
        $directives = $this->parseDirectives(\true);
        return new \Builderius\GraphQL\Language\AST\InputValueDefinitionNode(['name' => $name, 'type' => $type, 'defaultValue' => $defaultValue, 'directives' => $directives, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseInterfaceTypeDefinition() : \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('interface');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseFieldsDefinition();
        return new \Builderius\GraphQL\Language\AST\InterfaceTypeDefinitionNode(['name' => $name, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * UnionTypeDefinition :
     *   - Description? union Name Directives[Const]? UnionMemberTypes?
     *
     * @throws SyntaxError
     */
    private function parseUnionTypeDefinition() : \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('union');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $types = $this->parseUnionMemberTypes();
        return new \Builderius\GraphQL\Language\AST\UnionTypeDefinitionNode(['name' => $name, 'directives' => $directives, 'types' => $types, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * UnionMemberTypes :
     *   - = `|`? NamedType
     *   - UnionMemberTypes | NamedType
     */
    private function parseUnionMemberTypes() : \Builderius\GraphQL\Language\AST\NodeList
    {
        $types = [];
        if ($this->skip(\Builderius\GraphQL\Language\Token::EQUALS)) {
            // Optional leading pipe
            $this->skip(\Builderius\GraphQL\Language\Token::PIPE);
            do {
                $types[] = $this->parseNamedType();
            } while ($this->skip(\Builderius\GraphQL\Language\Token::PIPE));
        }
        return new \Builderius\GraphQL\Language\AST\NodeList($types);
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumTypeDefinition() : \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('enum');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $values = $this->parseEnumValuesDefinition();
        return new \Builderius\GraphQL\Language\AST\EnumTypeDefinitionNode(['name' => $name, 'directives' => $directives, 'values' => $values, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumValuesDefinition() : \Builderius\GraphQL\Language\AST\NodeList
    {
        /** @var NodeList<EnumValueDefinitionNode&Node> $nodeList */
        $nodeList = $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) ? $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, function () : EnumValueDefinitionNode {
            return $this->parseEnumValueDefinition();
        }, \Builderius\GraphQL\Language\Token::BRACE_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
        return $nodeList;
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumValueDefinition() : \Builderius\GraphQL\Language\AST\EnumValueDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        return new \Builderius\GraphQL\Language\AST\EnumValueDefinitionNode(['name' => $name, 'directives' => $directives, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputObjectTypeDefinition() : \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('input');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseInputFieldsDefinition();
        return new \Builderius\GraphQL\Language\AST\InputObjectTypeDefinitionNode(['name' => $name, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start), 'description' => $description]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputFieldsDefinition() : \Builderius\GraphQL\Language\AST\NodeList
    {
        /** @var NodeList<InputValueDefinitionNode&Node> $nodeList */
        $nodeList = $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) ? $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, function () : InputValueDefinitionNode {
            return $this->parseInputValueDefinition();
        }, \Builderius\GraphQL\Language\Token::BRACE_R) : new \Builderius\GraphQL\Language\AST\NodeList([]);
        return $nodeList;
    }
    /**
     * TypeExtension :
     *   - ScalarTypeExtension
     *   - ObjectTypeExtension
     *   - InterfaceTypeExtension
     *   - UnionTypeExtension
     *   - EnumTypeExtension
     *   - InputObjectTypeDefinition
     *
     * @throws SyntaxError
     */
    private function parseTypeExtension() : \Builderius\GraphQL\Language\AST\TypeExtensionNode
    {
        $keywordToken = $this->lexer->lookahead();
        if ($keywordToken->kind === \Builderius\GraphQL\Language\Token::NAME) {
            switch ($keywordToken->value) {
                case 'schema':
                    return $this->parseSchemaTypeExtension();
                case 'scalar':
                    return $this->parseScalarTypeExtension();
                case 'type':
                    return $this->parseObjectTypeExtension();
                case 'interface':
                    return $this->parseInterfaceTypeExtension();
                case 'union':
                    return $this->parseUnionTypeExtension();
                case 'enum':
                    return $this->parseEnumTypeExtension();
                case 'input':
                    return $this->parseInputObjectTypeExtension();
            }
        }
        throw $this->unexpected($keywordToken);
    }
    /**
     * @throws SyntaxError
     */
    private function parseSchemaTypeExtension() : \Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('schema');
        $directives = $this->parseDirectives(\true);
        $operationTypes = $this->peek(\Builderius\GraphQL\Language\Token::BRACE_L) ? $this->many(\Builderius\GraphQL\Language\Token::BRACE_L, [$this, 'parseOperationTypeDefinition'], \Builderius\GraphQL\Language\Token::BRACE_R) : [];
        if (\count($directives) === 0 && \count($operationTypes) === 0) {
            $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\SchemaTypeExtensionNode(['directives' => $directives, 'operationTypes' => $operationTypes, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseScalarTypeExtension() : \Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('scalar');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        if (\count($directives) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\ScalarTypeExtensionNode(['name' => $name, 'directives' => $directives, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseObjectTypeExtension() : \Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('type');
        $name = $this->parseName();
        $interfaces = $this->parseImplementsInterfaces();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseFieldsDefinition();
        if (\count($interfaces) === 0 && \count($directives) === 0 && \count($fields) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\ObjectTypeExtensionNode(['name' => $name, 'interfaces' => $interfaces, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseInterfaceTypeExtension() : \Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('interface');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseFieldsDefinition();
        if (\count($directives) === 0 && \count($fields) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\InterfaceTypeExtensionNode(['name' => $name, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start)]);
    }
    /**
     * UnionTypeExtension :
     *   - extend union Name Directives[Const]? UnionMemberTypes
     *   - extend union Name Directives[Const]
     *
     * @throws SyntaxError
     */
    private function parseUnionTypeExtension() : \Builderius\GraphQL\Language\AST\UnionTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('union');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $types = $this->parseUnionMemberTypes();
        if (\count($directives) === 0 && \count($types) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\UnionTypeExtensionNode(['name' => $name, 'directives' => $directives, 'types' => $types, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumTypeExtension() : \Builderius\GraphQL\Language\AST\EnumTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('enum');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $values = $this->parseEnumValuesDefinition();
        if (\count($directives) === 0 && \count($values) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\EnumTypeExtensionNode(['name' => $name, 'directives' => $directives, 'values' => $values, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputObjectTypeExtension() : \Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode
    {
        $start = $this->lexer->token;
        $this->expectKeyword('extend');
        $this->expectKeyword('input');
        $name = $this->parseName();
        $directives = $this->parseDirectives(\true);
        $fields = $this->parseInputFieldsDefinition();
        if (\count($directives) === 0 && \count($fields) === 0) {
            throw $this->unexpected();
        }
        return new \Builderius\GraphQL\Language\AST\InputObjectTypeExtensionNode(['name' => $name, 'directives' => $directives, 'fields' => $fields, 'loc' => $this->loc($start)]);
    }
    /**
     * DirectiveDefinition :
     *   - Description? directive @ Name ArgumentsDefinition? `repeatable`? on DirectiveLocations
     *
     * @throws SyntaxError
     */
    private function parseDirectiveDefinition() : \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode
    {
        $start = $this->lexer->token;
        $description = $this->parseDescription();
        $this->expectKeyword('directive');
        $this->expect(\Builderius\GraphQL\Language\Token::AT);
        $name = $this->parseName();
        $args = $this->parseArgumentsDefinition();
        $repeatable = $this->expectOptionalKeyword('repeatable');
        $this->expectKeyword('on');
        $locations = $this->parseDirectiveLocations();
        return new \Builderius\GraphQL\Language\AST\DirectiveDefinitionNode(['name' => $name, 'description' => $description, 'arguments' => $args, 'repeatable' => $repeatable, 'locations' => $locations, 'loc' => $this->loc($start)]);
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirectiveLocations() : \Builderius\GraphQL\Language\AST\NodeList
    {
        // Optional leading pipe
        $this->skip(\Builderius\GraphQL\Language\Token::PIPE);
        $locations = [];
        do {
            $locations[] = $this->parseDirectiveLocation();
        } while ($this->skip(\Builderius\GraphQL\Language\Token::PIPE));
        return new \Builderius\GraphQL\Language\AST\NodeList($locations);
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirectiveLocation() : \Builderius\GraphQL\Language\AST\NameNode
    {
        $start = $this->lexer->token;
        $name = $this->parseName();
        if (\Builderius\GraphQL\Language\DirectiveLocation::has($name->value)) {
            return $name;
        }
        throw $this->unexpected($start);
    }
}
