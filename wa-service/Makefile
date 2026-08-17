.PHONY: build run tidy

build:
	go build -o wa-service.exe ./...

run:
	go run main.go

tidy:
	go mod tidy

install-deps:
	go get github.com/gorilla/mux@v1.8.1
	go get github.com/joho/godotenv@v1.5.1
	go get github.com/mattn/go-sqlite3@v1.14.22
	go get go.mau.fi/whatsmeow@latest
	go get google.golang.org/protobuf/proto@latest
	go mod tidy
