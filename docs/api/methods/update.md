# update

更新方法用于管理 Bot 与 Telegram 服务器之间的连接。

## logOut

注销当前 Bot 会话，通常用于从 Webhook 切换到长轮询等场景。

```php
$bot->logOut();
```

返回 `true` 表示注销成功。

## close

关闭与 Telegram 的连接并释放资源。

```php
$bot->close();
```

返回 `true` 表示关闭成功。
