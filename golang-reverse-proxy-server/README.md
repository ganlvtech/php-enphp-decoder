# golang-reverse-proxy-server

[PHP2Shell](https://en.wikipedia.org/wiki/PHP2Shell) CVE affects php-fpm servers. PHP maintainers only provide security patches for `php >= 8.2`. And I don't want to update my code. So I create a simple golang server to reverse proxy the request (It works like php cgi).

```bash
docker run -it --rm -v "$(pwd)":/app -w /app -e CGO_ENABLED=0 golang:1.27 go build main.go
docker run -d --restart always --name enphp-decoder-reverse-proxy -p 3000:3000 -v "$(dirname "$(pwd)")":/app -w /app/"$(basename "$(pwd)")" php:7.4.33-fpm ./main
```

```caddy
enphp.ganlv.tech {
	log
	reverse_proxy 127.0.0.1:3000
}
```
