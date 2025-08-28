<?php

declare(strict_types=1);

namespace XBot\Telegram\Models\Response;

use XBot\Telegram\Models\DTO\BaseDTO;
use XBot\Telegram\Exceptions\ApiException;

/**
 * Telegram API 响应包装类
 * 
 * 包装 Telegram API 的响应数据
 */
class TelegramResponse
{
    /**
     * 响应是否成功
     */
    protected bool $ok;

    /**
     * 响应结果数据
     */
    protected mixed $result;

    /**
     * 错误代码（当 ok 为 false 时）
     */
    protected ?int $errorCode = null;

    /**
     * 错误描述（当 ok 为 false 时）
     */
    protected ?string $description = null;

    /**
     * 错误参数（当 ok 为 false 时）
     */
    protected array $parameters = [];

    /**
     * 原始响应数据
     */
    protected array $rawResponse;

    /**
     * HTTP 状态码
     */
    protected int $statusCode;

    /**
     * 响应头
     */
    protected array $headers;

    /**
     * Bot 名称
     */
    protected ?string $botName = null;

    public function __construct(
        array $responseData,
        int $statusCode = 200,
        array $headers = [],
        ?string $botName = null
    ) {
        $this->rawResponse = $responseData;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->botName = $botName;

        $this->parseResponse($responseData);
    }

    /**
     * 解析响应数据
     */
    protected function parseResponse(array $responseData): void
    {
        $this->ok = $responseData['ok'] ?? false;

        if ($this->ok) {
            $this->result = $responseData['result'] ?? null;
        } else {
            $this->errorCode = $responseData['error_code'] ?? 0;
            $this->description = $responseData['description'] ?? 'Unknown error';
            $this->parameters = $responseData['parameters'] ?? [];
        }
    }

    /**
     * 检查响应是否成功
     */
    public function isOk(): bool
    {
        return $this->ok;
    }

    /**
     * 检查响应是否失败
     */
    public function isError(): bool
    {
        return !$this->ok;
    }

    /**
     * 获取响应结果
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * 获取错误代码
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * 获取错误描述
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * 获取错误参数
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * 获取原始响应数据
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    /**
     * 获取 HTTP 状态码
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 获取响应头
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 获取指定响应头
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * 获取 Bot 名称
     */
    public function getBotName(): ?string
    {
        return $this->botName;
    }

    /**
     * 将结果转换为指定的 DTO 类
     */
    public function toDTO(string $dtoClass): BaseDTO
    {
        if (!$this->isOk()) {
            throw new ApiException(
                $this->description ?? 'Unknown error',
                $this->errorCode ?? 0,
                $this->parameters,
                null,
                ['raw_response' => $this->rawResponse],
                $this->botName
            );
        }

        if (!is_subclass_of($dtoClass, BaseDTO::class)) {
            throw new \InvalidArgumentException("Class {$dtoClass} must extend BaseDTO");
        }

        if (!is_array($this->result)) {
            throw new \InvalidArgumentException('Result must be an array to convert to DTO');
        }

        return $dtoClass::fromArray($this->result);
    }

    /**
     * 将结果转换为 DTO 数组
     */
    public function toDTOArray(string $dtoClass): array
    {
        if (!$this->isOk()) {
            throw new ApiException(
                $this->description ?? 'Unknown error',
                $this->errorCode ?? 0,
                $this->parameters,
                null,
                ['raw_response' => $this->rawResponse],
                $this->botName
            );
        }

        if (!is_subclass_of($dtoClass, BaseDTO::class)) {
            throw new \InvalidArgumentException("Class {$dtoClass} must extend BaseDTO");
        }

        if (!is_array($this->result)) {
            throw new \InvalidArgumentException('Result must be an array to convert to DTO array');
        }

        $dtoArray = [];
        foreach ($this->result as $item) {
            if (is_array($item)) {
                $dtoArray[] = $dtoClass::fromArray($item);
            }
        }

        return $dtoArray;
    }

    /**
     * 确保响应成功，否则抛出异常
     */
    public function ensureOk(): static
    {
        if (!$this->isOk()) {
            throw new ApiException(
                $this->description ?? 'Unknown error',
                $this->errorCode ?? 0,
                $this->parameters,
                null,
                ['raw_response' => $this->rawResponse],
                $this->botName
            );
        }

        return $this;
    }

    /**
     * 获取重试延迟时间（针对 429 错误）
     */
    public function getRetryAfter(): ?int
    {
        return $this->parameters['retry_after'] ?? null;
    }

    /**
     * 获取迁移到的聊天 ID（针对群组迁移）
     */
    public function getMigrateToChatId(): ?int
    {
        return $this->parameters['migrate_to_chat_id'] ?? null;
    }

    /**
     * 检查是否为速率限制错误
     */
    public function isRateLimited(): bool
    {
        return $this->errorCode === 429;
    }

    /**
     * 检查是否为群组迁移错误
     */
    public function isChatMigrated(): bool
    {
        return $this->getMigrateToChatId() !== null;
    }

    /**
     * 检查是否为权限错误
     */
    public function isForbidden(): bool
    {
        return $this->errorCode === 403;
    }

    /**
     * 检查是否为资源不存在错误
     */
    public function isNotFound(): bool
    {
        return $this->errorCode === 404;
    }

    /**
     * 检查是否为参数错误
     */
    public function isBadRequest(): bool
    {
        return $this->errorCode === 400;
    }

    /**
     * 检查是否为认证错误
     */
    public function isUnauthorized(): bool
    {
        return $this->errorCode === 401;
    }

    /**
     * 检查是否为冲突错误
     */
    public function isConflict(): bool
    {
        return $this->errorCode === 409;
    }

    /**
     * 将响应转换为数组
     */
    public function toArray(): array
    {
        return [
            'ok' => $this->ok,
            'result' => $this->result,
            'error_code' => $this->errorCode,
            'description' => $this->description,
            'parameters' => $this->parameters,
            'status_code' => $this->statusCode,
            'bot_name' => $this->botName,
        ];
    }

    /**
     * 将响应转换为 JSON
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * 字符串表示
     */
    public function __toString(): string
    {
        return $this->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 从 JSON 字符串创建响应实例
     */
    public static function fromJson(
        string $json, 
        int $statusCode = 200, 
        array $headers = [], 
        ?string $botName = null
    ): static {
        $data = json_decode($json, true);
        
        if ($data === null) {
            throw new \InvalidArgumentException('Invalid JSON response');
        }

        return new static($data, $statusCode, $headers, $botName);
    }

    /**
     * 创建成功响应
     */
    public static function success(
        mixed $result, 
        int $statusCode = 200, 
        array $headers = [], 
        ?string $botName = null
    ): static {
        return new static([
            'ok' => true,
            'result' => $result
        ], $statusCode, $headers, $botName);
    }

    /**
     * 创建错误响应
     */
    public static function error(
        string $description,
        int $errorCode = 400,
        array $parameters = [],
        int $statusCode = 400,
        array $headers = [],
        ?string $botName = null
    ): static {
        return new static([
            'ok' => false,
            'error_code' => $errorCode,
            'description' => $description,
            'parameters' => $parameters
        ], $statusCode, $headers, $botName);
    }
}

/**
 * 分页响应包装类
 */
class PaginatedResponse extends TelegramResponse
{
    /**
     * 总数量
     */
    protected ?int $total = null;

    /**
     * 当前页码
     */
    protected int $page = 1;

    /**
     * 每页数量
     */
    protected int $perPage = 100;

    /**
     * 偏移量
     */
    protected int $offset = 0;

    /**
     * 是否有更多数据
     */
    protected bool $hasMore = false;

    public function __construct(
        array $responseData,
        int $page = 1,
        int $perPage = 100,
        int $offset = 0,
        ?int $total = null,
        int $statusCode = 200,
        array $headers = [],
        ?string $botName = null
    ) {
        parent::__construct($responseData, $statusCode, $headers, $botName);
        
        $this->page = $page;
        $this->perPage = $perPage;
        $this->offset = $offset;
        $this->total = $total;
        
        // 检查是否有更多数据
        if (is_array($this->result)) {
            $this->hasMore = count($this->result) >= $this->perPage;
        }
    }

    /**
     * 获取当前页码
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * 获取每页数量
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * 获取偏移量
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * 获取总数量
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * 检查是否有更多数据
     */
    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    /**
     * 获取下一页的偏移量
     */
    public function getNextOffset(): int
    {
        return $this->offset + $this->perPage;
    }

    /**
     * 获取当前页的数据数量
     */
    public function getCount(): int
    {
        return is_array($this->result) ? count($this->result) : 0;
    }

    /**
     * 检查是否为第一页
     */
    public function isFirstPage(): bool
    {
        return $this->page === 1;
    }

    /**
     * 检查是否为最后一页
     */
    public function isLastPage(): bool
    {
        return !$this->hasMore();
    }

    /**
     * 获取分页信息
     */
    public function getPaginationInfo(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
            'offset' => $this->offset,
            'total' => $this->total,
            'count' => $this->getCount(),
            'has_more' => $this->hasMore,
            'next_offset' => $this->getNextOffset(),
            'is_first_page' => $this->isFirstPage(),
            'is_last_page' => $this->isLastPage(),
        ];
    }

    /**
     * 将分页响应转换为数组
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'pagination' => $this->getPaginationInfo()
        ]);
    }
}