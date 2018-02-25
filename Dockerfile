FROM alpine:latest
EXPOSE 8010
RUN apk add --no-cache git nodejs && \
        git clone https://github.com/ety001/steem-editor.git /app && \
        cd /app/fe && \
        npm install 
WORKDIR /app/fe
CMD ["/app/run.sh"] 
