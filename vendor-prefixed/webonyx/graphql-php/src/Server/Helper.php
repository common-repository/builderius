<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Server;

use Builderius\GraphQL\Error\DebugFlag;
use Builderius\GraphQL\Error\Error;
use Builderius\GraphQL\Error\FormattedError;
use Builderius\GraphQL\Error\InvariantViolation;
use Builderius\GraphQL\Executor\ExecutionResult;
use Builderius\GraphQL\Executor\Executor;
use Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use Builderius\GraphQL\Executor\Promise\Promise;
use Builderius\GraphQL\Executor\Promise\PromiseAdapter;
use Builderius\GraphQL\GraphQL;
use Builderius\GraphQL\Language\AST\DocumentNode;
use Builderius\GraphQL\Language\Parser;
use Builderius\GraphQL\Utils\AST;
use Builderius\GraphQL\Utils\Utils;
use JsonSerializable;
use Builderius\Psr\Http\Message\RequestInterface;
use Builderius\Psr\Http\Message\ResponseInterface;
use Builderius\Psr\Http\Message\ServerRequestInterface;
use Builderius\Psr\Http\Message\StreamInterface;
use function count;
use function file_get_contents;
use function header;
use function html_entity_decode;
use function is_array;
use function is_callable;
use function is_string;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function parse_str;
use function sprintf;
use function stripos;
/**
 * Contains functionality that could be re-used by various server implementations
 */
class Helper
{
    /**
     * Parses HTTP request using PHP globals and returns GraphQL OperationParams
     * contained in this request. For batched requests it returns an array of OperationParams.
     *
     * This function does not check validity of these params
     * (validation is performed separately in validateOperationParams() method).
     *
     * If $readRawBodyFn argument is not provided - will attempt to read raw request body
     * from `php://input` stream.
     *
     * Internally it normalizes input to $method, $bodyParams and $queryParams and
     * calls `parseRequestParams()` to produce actual return value.
     *
     * For PSR-7 request parsing use `parsePsrRequest()` instead.
     *
     * @return OperationParams|OperationParams[]
     *
     * @throws RequestError
     *
     * @api
     */
    public function parseHttpRequest(?callable $readRawBodyFn = null)
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? null;
        $bodyParams = [];
        $urlParams = $_GET;
        if ($method === 'POST') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? null;
            if ($contentType === null) {
                throw new \Builderius\GraphQL\Server\RequestError('Missing "Content-Type" header');
            }
            if (\stripos($contentType, 'application/graphql') !== \false) {
                $rawBody = $readRawBodyFn ? $readRawBodyFn() : $this->readRawBody();
                $bodyParams = ['query' => $rawBody ?? ''];
            } elseif (\stripos($contentType, 'application/json') !== \false) {
                $rawBody = $readRawBodyFn ? $readRawBodyFn() : $this->readRawBody();
                $bodyParams = \json_decode($rawBody ?? '', \true);
                if (\json_last_error()) {
                    throw new \Builderius\GraphQL\Server\RequestError('Could not parse JSON: ' . \json_last_error_msg());
                }
                if (!\is_array($bodyParams)) {
                    throw new \Builderius\GraphQL\Server\RequestError('GraphQL Server expects JSON object or array, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($bodyParams));
                }
            } elseif (\stripos($contentType, 'application/x-www-form-urlencoded') !== \false) {
                $bodyParams = $_POST;
            } elseif (\stripos($contentType, 'multipart/form-data') !== \false) {
                $bodyParams = $_POST;
            } else {
                throw new \Builderius\GraphQL\Server\RequestError('Unexpected content type: ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($contentType));
            }
        }
        return $this->parseRequestParams($method, $bodyParams, $urlParams);
    }
    /**
     * Parses normalized request params and returns instance of OperationParams
     * or array of OperationParams in case of batch operation.
     *
     * Returned value is a suitable input for `executeOperation` or `executeBatch` (if array)
     *
     * @param string  $method
     * @param mixed[] $bodyParams
     * @param mixed[] $queryParams
     *
     * @return OperationParams|OperationParams[]
     *
     * @throws RequestError
     *
     * @api
     */
    public function parseRequestParams($method, array $bodyParams, array $queryParams)
    {
        if ($method === 'GET') {
            $result = \Builderius\GraphQL\Server\OperationParams::create($queryParams, \true);
        } elseif ($method === 'POST') {
            if (isset($bodyParams[0])) {
                $result = [];
                foreach ($bodyParams as $index => $entry) {
                    $op = \Builderius\GraphQL\Server\OperationParams::create($entry);
                    $result[] = $op;
                }
            } else {
                $result = \Builderius\GraphQL\Server\OperationParams::create($bodyParams);
            }
        } else {
            throw new \Builderius\GraphQL\Server\RequestError('HTTP Method "' . $method . '" is not supported');
        }
        return $result;
    }
    /**
     * Checks validity of OperationParams extracted from HTTP request and returns an array of errors
     * if params are invalid (or empty array when params are valid)
     *
     * @return array<int, RequestError>
     *
     * @api
     */
    public function validateOperationParams(\Builderius\GraphQL\Server\OperationParams $params)
    {
        $errors = [];
        if (!$params->query && !$params->queryId) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request must include at least one of those two parameters: "query" or "queryId"');
        }
        if ($params->query && $params->queryId) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request parameters "query" and "queryId" are mutually exclusive');
        }
        if ($params->query !== null && !\is_string($params->query)) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request parameter "query" must be string, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($params->query));
        }
        if ($params->queryId !== null && !\is_string($params->queryId)) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request parameter "queryId" must be string, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($params->queryId));
        }
        if ($params->operation !== null && !\is_string($params->operation)) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request parameter "operation" must be string, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($params->operation));
        }
        if ($params->variables !== null && (!\is_array($params->variables) || isset($params->variables[0]))) {
            $errors[] = new \Builderius\GraphQL\Server\RequestError('GraphQL Request parameter "variables" must be object or JSON string parsed to object, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($params->getOriginalInput('variables')));
        }
        return $errors;
    }
    /**
     * Executes GraphQL operation with given server configuration and returns execution result
     * (or promise when promise adapter is different from SyncPromiseAdapter)
     *
     * @return ExecutionResult|Promise
     *
     * @api
     */
    public function executeOperation(\Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $op)
    {
        $promiseAdapter = $config->getPromiseAdapter() ?? \Builderius\GraphQL\Executor\Executor::getPromiseAdapter();
        $result = $this->promiseToExecuteOperation($promiseAdapter, $config, $op);
        if ($promiseAdapter instanceof \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter) {
            $result = $promiseAdapter->wait($result);
        }
        return $result;
    }
    /**
     * Executes batched GraphQL operations with shared promise queue
     * (thus, effectively batching deferreds|promises of all queries at once)
     *
     * @param OperationParams[] $operations
     *
     * @return ExecutionResult|ExecutionResult[]|Promise
     *
     * @api
     */
    public function executeBatch(\Builderius\GraphQL\Server\ServerConfig $config, array $operations)
    {
        $promiseAdapter = $config->getPromiseAdapter() ?? \Builderius\GraphQL\Executor\Executor::getPromiseAdapter();
        $result = [];
        foreach ($operations as $operation) {
            $result[] = $this->promiseToExecuteOperation($promiseAdapter, $config, $operation, \true);
        }
        $result = $promiseAdapter->all($result);
        // Wait for promised results when using sync promises
        if ($promiseAdapter instanceof \Builderius\GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter) {
            $result = $promiseAdapter->wait($result);
        }
        return $result;
    }
    /**
     * @param bool $isBatch
     *
     * @return Promise
     */
    private function promiseToExecuteOperation(\Builderius\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $op, $isBatch = \false)
    {
        try {
            if ($config->getSchema() === null) {
                throw new \Builderius\GraphQL\Error\InvariantViolation('Schema is required for the server');
            }
            if ($isBatch && !$config->getQueryBatching()) {
                throw new \Builderius\GraphQL\Server\RequestError('Batched queries are not supported by this server');
            }
            $errors = $this->validateOperationParams($op);
            if (\count($errors) > 0) {
                $errors = \Builderius\GraphQL\Utils\Utils::map($errors, static function (\Builderius\GraphQL\Server\RequestError $err) : Error {
                    return \Builderius\GraphQL\Error\Error::createLocatedError($err, null, null);
                });
                return $promiseAdapter->createFulfilled(new \Builderius\GraphQL\Executor\ExecutionResult(null, $errors));
            }
            $doc = $op->queryId ? $this->loadPersistedQuery($config, $op) : $op->query;
            if (!$doc instanceof \Builderius\GraphQL\Language\AST\DocumentNode) {
                $doc = \Builderius\GraphQL\Language\Parser::parse($doc);
            }
            $operationType = \Builderius\GraphQL\Utils\AST::getOperation($doc, $op->operation);
            if ($operationType === \false) {
                throw new \Builderius\GraphQL\Server\RequestError('Failed to determine operation type');
            }
            if ($operationType !== 'query' && $op->isReadOnly()) {
                throw new \Builderius\GraphQL\Server\RequestError('GET supports only query operation');
            }
            $result = \Builderius\GraphQL\GraphQL::promiseToExecute($promiseAdapter, $config->getSchema(), $doc, $this->resolveRootValue($config, $op, $doc, $operationType), $this->resolveContextValue($config, $op, $doc, $operationType), $op->variables, $op->operation, $config->getFieldResolver(), $this->resolveValidationRules($config, $op, $doc, $operationType));
        } catch (\Builderius\GraphQL\Server\RequestError $e) {
            $result = $promiseAdapter->createFulfilled(new \Builderius\GraphQL\Executor\ExecutionResult(null, [\Builderius\GraphQL\Error\Error::createLocatedError($e)]));
        } catch (\Builderius\GraphQL\Error\Error $e) {
            $result = $promiseAdapter->createFulfilled(new \Builderius\GraphQL\Executor\ExecutionResult(null, [$e]));
        }
        $applyErrorHandling = static function (\Builderius\GraphQL\Executor\ExecutionResult $result) use($config) : ExecutionResult {
            if ($config->getErrorsHandler()) {
                $result->setErrorsHandler($config->getErrorsHandler());
            }
            if ($config->getErrorFormatter() || $config->getDebugFlag() !== \Builderius\GraphQL\Error\DebugFlag::NONE) {
                $result->setErrorFormatter(\Builderius\GraphQL\Error\FormattedError::prepareFormatter($config->getErrorFormatter(), $config->getDebugFlag()));
            }
            return $result;
        };
        return $result->then($applyErrorHandling);
    }
    /**
     * @return mixed
     *
     * @throws RequestError
     */
    private function loadPersistedQuery(\Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $operationParams)
    {
        // Load query if we got persisted query id:
        $loader = $config->getPersistentQueryLoader();
        if ($loader === null) {
            throw new \Builderius\GraphQL\Server\RequestError('Persisted queries are not supported by this server');
        }
        $source = $loader($operationParams->queryId, $operationParams);
        if (!\is_string($source) && !$source instanceof \Builderius\GraphQL\Language\AST\DocumentNode) {
            throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Persistent query loader must return query string or instance of %s but got: %s', \Builderius\GraphQL\Language\AST\DocumentNode::class, \Builderius\GraphQL\Utils\Utils::printSafe($source)));
        }
        return $source;
    }
    /**
     * @param string $operationType
     *
     * @return mixed[]|null
     */
    private function resolveValidationRules(\Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $params, \Builderius\GraphQL\Language\AST\DocumentNode $doc, $operationType)
    {
        // Allow customizing validation rules per operation:
        $validationRules = $config->getValidationRules();
        if (\is_callable($validationRules)) {
            $validationRules = $validationRules($params, $doc, $operationType);
            if (!\is_array($validationRules)) {
                throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Expecting validation rules to be array or callable returning array, but got: %s', \Builderius\GraphQL\Utils\Utils::printSafe($validationRules)));
            }
        }
        return $validationRules;
    }
    /**
     * @return mixed
     */
    private function resolveRootValue(\Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $params, \Builderius\GraphQL\Language\AST\DocumentNode $doc, string $operationType)
    {
        $rootValue = $config->getRootValue();
        if (\is_callable($rootValue)) {
            $rootValue = $rootValue($params, $doc, $operationType);
        }
        return $rootValue;
    }
    /**
     * @param string $operationType
     *
     * @return mixed
     */
    private function resolveContextValue(\Builderius\GraphQL\Server\ServerConfig $config, \Builderius\GraphQL\Server\OperationParams $params, \Builderius\GraphQL\Language\AST\DocumentNode $doc, $operationType)
    {
        $context = $config->getContext();
        if (\is_callable($context)) {
            $context = $context($params, $doc, $operationType);
        }
        return $context;
    }
    /**
     * Send response using standard PHP `header()` and `echo`.
     *
     * @param Promise|ExecutionResult|ExecutionResult[] $result
     * @param bool                                      $exitWhenDone
     *
     * @api
     */
    public function sendResponse($result, $exitWhenDone = \false)
    {
        if ($result instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
            $result->then(function ($actualResult) use($exitWhenDone) : void {
                $this->doSendResponse($actualResult, $exitWhenDone);
            });
        } else {
            $this->doSendResponse($result, $exitWhenDone);
        }
    }
    private function doSendResponse($result, $exitWhenDone)
    {
        $httpStatus = $this->resolveHttpStatus($result);
        $this->emitResponse($result, $httpStatus, $exitWhenDone);
    }
    /**
     * @param mixed[]|JsonSerializable $jsonSerializable
     * @param int                      $httpStatus
     * @param bool                     $exitWhenDone
     */
    public function emitResponse($jsonSerializable, $httpStatus, $exitWhenDone)
    {
        $body = \json_encode($jsonSerializable);
        \header('Content-Type: application/json', \true, $httpStatus);
        echo $body;
        if ($exitWhenDone) {
            exit;
        }
    }
    /**
     * @return bool|string
     */
    private function readRawBody()
    {
        return \file_get_contents('php://input');
    }
    /**
     * @param ExecutionResult|mixed[] $result
     *
     * @return int
     */
    private function resolveHttpStatus($result)
    {
        if (\is_array($result) && isset($result[0])) {
            \Builderius\GraphQL\Utils\Utils::each($result, static function ($executionResult, $index) : void {
                if (!$executionResult instanceof \Builderius\GraphQL\Executor\ExecutionResult) {
                    throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Expecting every entry of batched query result to be instance of %s but entry at position %d is %s', \Builderius\GraphQL\Executor\ExecutionResult::class, $index, \Builderius\GraphQL\Utils\Utils::printSafe($executionResult)));
                }
            });
            $httpStatus = 200;
        } else {
            if (!$result instanceof \Builderius\GraphQL\Executor\ExecutionResult) {
                throw new \Builderius\GraphQL\Error\InvariantViolation(\sprintf('Expecting query result to be instance of %s but got %s', \Builderius\GraphQL\Executor\ExecutionResult::class, \Builderius\GraphQL\Utils\Utils::printSafe($result)));
            }
            if ($result->data === null && \count($result->errors) > 0) {
                $httpStatus = 400;
            } else {
                $httpStatus = 200;
            }
        }
        return $httpStatus;
    }
    /**
     * Converts PSR-7 request to OperationParams[]
     *
     * @return OperationParams[]|OperationParams
     *
     * @throws RequestError
     *
     * @api
     */
    public function parsePsrRequest(\Builderius\Psr\Http\Message\RequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            $bodyParams = [];
        } else {
            $contentType = $request->getHeader('content-type');
            if (!isset($contentType[0])) {
                throw new \Builderius\GraphQL\Server\RequestError('Missing "Content-Type" header');
            }
            if (\stripos($contentType[0], 'application/graphql') !== \false) {
                $bodyParams = ['query' => (string) $request->getBody()];
            } elseif (\stripos($contentType[0], 'application/json') !== \false) {
                $bodyParams = $request instanceof \Builderius\Psr\Http\Message\ServerRequestInterface ? $request->getParsedBody() : \json_decode((string) $request->getBody(), \true);
                if ($bodyParams === null) {
                    throw new \Builderius\GraphQL\Error\InvariantViolation($request instanceof \Builderius\Psr\Http\Message\ServerRequestInterface ? 'Expected to receive a parsed body for "application/json" PSR-7 request but got null' : 'Expected to receive a JSON array in body for "application/json" PSR-7 request');
                }
                if (!\is_array($bodyParams)) {
                    throw new \Builderius\GraphQL\Server\RequestError('GraphQL Server expects JSON object or array, but got ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($bodyParams));
                }
            } else {
                \parse_str((string) $request->getBody(), $bodyParams);
                if (!\is_array($bodyParams)) {
                    throw new \Builderius\GraphQL\Server\RequestError('Unexpected content type: ' . \Builderius\GraphQL\Utils\Utils::printSafeJson($contentType[0]));
                }
            }
        }
        \parse_str(\html_entity_decode($request->getUri()->getQuery()), $queryParams);
        return $this->parseRequestParams($request->getMethod(), $bodyParams, $queryParams);
    }
    /**
     * Converts query execution result to PSR-7 response
     *
     * @param Promise|ExecutionResult|ExecutionResult[] $result
     *
     * @return Promise|ResponseInterface
     *
     * @api
     */
    public function toPsrResponse($result, \Builderius\Psr\Http\Message\ResponseInterface $response, \Builderius\Psr\Http\Message\StreamInterface $writableBodyStream)
    {
        if ($result instanceof \Builderius\GraphQL\Executor\Promise\Promise) {
            return $result->then(function ($actualResult) use($response, $writableBodyStream) {
                return $this->doConvertToPsrResponse($actualResult, $response, $writableBodyStream);
            });
        }
        return $this->doConvertToPsrResponse($result, $response, $writableBodyStream);
    }
    private function doConvertToPsrResponse($result, \Builderius\Psr\Http\Message\ResponseInterface $response, \Builderius\Psr\Http\Message\StreamInterface $writableBodyStream)
    {
        $httpStatus = $this->resolveHttpStatus($result);
        $result = \json_encode($result);
        $writableBodyStream->write($result);
        return $response->withStatus($httpStatus)->withHeader('Content-Type', 'application/json')->withBody($writableBodyStream);
    }
}
