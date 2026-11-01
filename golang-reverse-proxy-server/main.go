package main

import (
	"bytes"
	"errors"
	"fmt"
	"io"
	"log/slog"
	"net/http"
	"net/url"
	"os/exec"
	"sync"
	"sync/atomic"
)

func main() {
	mux := http.NewServeMux()
	mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		if r.URL.Path == "/" || r.URL.Path == "/index.php" {
			http.ServeFile(w, r, "public/index.html")
		} else if r.URL.Path == "/result.txt" {
			http.ServeFile(w, r, "public/result.txt")
		} else {
			http.NotFound(w, r)
		}
	})

	var mu sync.Mutex
	var queueLength int32
	mux.HandleFunc("/decode.php", func(w http.ResponseWriter, r *http.Request) {
		if r.Method != http.MethodPost {
			slog.ErrorContext(r.Context(), "Method Not Allowed")
			http.Error(w, "Method Not Allowed", http.StatusMethodNotAllowed)
			return
		}
		newQueueLength := atomic.AddInt32(&queueLength, 1)
		defer func() {
			atomic.AddInt32(&queueLength, -1)
		}()
		if newQueueLength > 5 {
			slog.ErrorContext(r.Context(), "Too Many Requests")
			http.Error(w, "Too Many Requests", http.StatusTooManyRequests)
			return
		}

		r.Body = http.MaxBytesReader(w, r.Body, 10*1024*1024) // 10 MiB
		file, fileHeader, err := r.FormFile("file")
		if err != nil {
			slog.ErrorContext(r.Context(), "File upload error", "err", err)
			if _, ok := errors.AsType[*http.MaxBytesError](err); ok {
				http.Error(w, "Request body is too large: "+err.Error(), http.StatusRequestEntityTooLarge)
				return
			}
			http.Error(w, "File upload error: "+err.Error(), http.StatusBadRequest)
			return
		}
		defer file.Close()
		data, err := io.ReadAll(file)
		if err != nil {
			slog.ErrorContext(r.Context(), "Read file error", "err", err)
			http.Error(w, "Read file error: "+err.Error(), http.StatusInternalServerError)
			return
		}
		if len(data) == 0 {
			slog.ErrorContext(r.Context(), "Empty file")
			http.Error(w, "Empty file", http.StatusBadRequest)
			return
		}
		slog.InfoContext(r.Context(), "read file ok", "fileLen", len(data))

		mu.Lock()
		defer mu.Unlock()

		var stdout bytes.Buffer
		var stderr bytes.Buffer
		cmd := exec.Command("php", "decode.php")
		cmd.Env = append(cmd.Environ(), "REMOTE_ADDR="+r.Header.Get("X-Real-IP"), "FILE_NAME="+fileHeader.Filename)
		cmd.Stdin = bytes.NewReader(data)
		cmd.Stdout = &stdout
		cmd.Stderr = &stderr
		err = cmd.Run()
		if err != nil {
			slog.ErrorContext(r.Context(), "Decode EnPHP error", "err", err, "stderr", stderr.String(), "stdout", stdout.String())
			http.Error(w, "Decode EnPHP error: "+err.Error(), http.StatusInternalServerError)
			return
		}
		slog.InfoContext(r.Context(), "decode.php executed", "stderr", stderr.String())
		firstLine, output, ok := bytes.Cut(stdout.Bytes(), []byte("\n"))
		if ok && bytes.HasPrefix(firstLine, []byte("OK")) {
			w.Header().Set("Content-Type", "application/octet-stream")
			w.Header().Set("Content-Disposition", fmt.Sprintf("attachment; filename=\"%s\"", url.QueryEscape(fileHeader.Filename)))
			w.Write(output)
		} else {
			w.Header().Set("Content-Type", "text/plain; charset=utf-8")
			slog.ErrorContext(r.Context(), "decode error", "stdout", stdout.String())
			http.Error(w, stdout.String(), http.StatusInternalServerError)
			return
		}
	})

	fmt.Println("Listening on :3000")
	http.ListenAndServe(":3000", mux)
}
