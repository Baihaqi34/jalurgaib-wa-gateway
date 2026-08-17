FROM golang:1.24-alpine AS builder

# Install build dependencies for CGO (SQLite)
RUN apk add --no-cache gcc musl-dev sqlite-dev

WORKDIR /app

COPY go.mod go.sum ./
RUN go mod download

COPY . .

# Build Go binary with CGO enabled for SQLite
RUN CGO_ENABLED=1 GOOS=linux go build -ldflags="-s -w" -o wa-engine main.go

FROM alpine:latest

RUN apk add --no-cache ca-certificates sqlite-libs tzdata

WORKDIR /app

COPY --from=builder /app/wa-engine /app/wa-engine
RUN mkdir -p /app/data

EXPOSE 8080

CMD ["/app/wa-engine"]
