FROM alpine:latest
WORKDIR /app
EXPOSE 8010
RUN apk add --no-cache git nodejs && \
        git clone https://github.com/ety001/steem-editor.git /app && \
        chmod +x /app/run.sh && \
        cd /app/fe && \
        npm install 
CMD ["/app/run.sh"] 
